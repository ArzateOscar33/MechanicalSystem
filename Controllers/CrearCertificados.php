<?php

use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

require_once "vendor/autoload.php";
require_once "Config/Http/SecomextClient.php";
require_once "Models/PrevalidacionesModel.php";

class CrearCertificados extends Controller
{
    private PrevalidacionesModel $prevalModel;
    private SecomextClient $secomext;

    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->validarSesionInactividad();

        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }

        $this->prevalModel = new PrevalidacionesModel();
        $this->secomext    = new SecomextClient();
    }

    // =========================================================
    // INDEX
    // =========================================================
    public function index()
    {
        unset($_SESSION['preval_id'], $_SESSION['preval_cert_number']);

        // SOLO visual / preliminar
        $cert_number = $this->model->obtenerCertNumberPreliminar();
        $_SESSION['preval_cert_number'] = $cert_number;

        $data['title']       = 'Crear Certificado';
        $data['cert_number'] = $cert_number;
        $data['direcciones'] = $this->model->obtenerDirecciones();
        $data['inspectores'] = $this->model->obtenerInspectores();
        $data['clientes'] = $this->model->obtenerClientes();
        $this->views->getView('admin/CrearCertificados', "index", $data);
    }

    // =========================================================
    // PASO 1 — PREVALIDAR DATOS
    // =========================================================
    public function prevalidarDatos()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON('Solicitud inválida', 'error');
            return;
        }

        $id_usuario  = $_SESSION['id_usuario'] ?? 0;

        $vin           = trim($_POST['vin'] ?? '');
        $license_plate = trim($_POST['placa'] ?? '');
        $odometer      = $_POST['odometro'] ?? 0;
        $test_date     = $_POST['fecha'] ?? '';
        $latitude      = $_POST['latitud'] ?? '';
        $longitude     = $_POST['longitud'] ?? '';

        $toP = function ($val): string {
            $v = strtoupper(trim((string)$val));
            return ($v === 'PASA' || $v === 'PASS' || $v === 'P') ? 'P' : 'F';
        };

        $misfire       = $toP($_POST['monitor_fallo_encendido']      ?? '');
        $fuelSystem    = $toP($_POST['monitor_sistema_combustible']  ?? '');
        $compCatalyst  = $toP($_POST['monitor_integral_catalizador'] ?? '');
        $catalyst      = $toP($_POST['monitor_catalizador']          ?? '');
        $oxygenSensor  = $toP($_POST['monitor_sensor_c2']            ?? '');
        $overallResult = $toP($_POST['resultado_prueba']             ?? '');

        if (!$vin || strlen($vin) !== 17) {
            $this->responderJSON('VIN inválido.', 'warning');
            return;
        }

        if (!$test_date) {
            $this->responderJSON('La fecha de prueba es obligatoria.', 'warning');
            return;
        }

        $certExistente = $this->model->vinConCertificadoActivo($vin, $test_date);
        if ($certExistente) {
            $this->responderJSON('Ya existe un certificado vigente para este VIN.', 'warning');
            return;
        }

        $preval_id = $this->prevalModel->crearIntento($vin, $license_plate, $id_usuario);
        if (!$preval_id) {
            $this->responderJSON('Error al registrar el intento. Intenta de nuevo.', 'error');
            return;
        }

        $_SESSION['preval_id'] = $preval_id;

        // ==========================================
        // NUEVO: reservar folio real en BD
        // ==========================================
        $cert_number = $this->model->reservarCertNumberParaPrevalidacion((int)$preval_id, $vin, (int)$id_usuario);
        if (!$cert_number) {
            unset($_SESSION['preval_id']);
            $this->responderJSON('No fue posible reservar el número de certificado.', 'error');
            return;
        }

        $_SESSION['preval_cert_number'] = $cert_number;

        $datos = [
            'vin'                                => $vin,
            'cert_number'                        => $cert_number,
            'dmv_number'                         => $cert_number,
            'misfire_monitoring'                 => $misfire,
            'fuel_system_monitoring'             => $fuelSystem,
            'comprehensive_catalyst_monitoring'  => $compCatalyst,
            'catalyst_monitoring'                => $catalyst,
            'oxygen_sensor_monitoring'           => $oxygenSensor,
            'overall_test_result'                => $overallResult,
            'test_date'                          => $test_date,
            'odometer'                           => $odometer,
            'license_plate'                      => $license_plate,
            'latitude'                           => $latitude,
            'longitude'                          => $longitude,
        ];

        $respuesta = $this->secomext->enviarDatos($datos);

        $this->prevalModel->actualizarPasoDatos(
            $preval_id,
            $respuesta['success'] ? 'success' : 'failed',
            $respuesta['respuesta'],
            $respuesta['success'] ? $cert_number : ''
        );

        if (!$respuesta['success']) {
            $this->model->liberarReservaCertificado(
                $cert_number,
                (int)$preval_id,
                'Fallo en prevalidación de datos'
            );

            unset($_SESSION['preval_id'], $_SESSION['preval_cert_number'], $_SESSION['preval_fotos']);

            $this->responderJSON('Secomext rechazó los datos: ' . $respuesta['respuesta'], 'error');
            return;
        }

        // ==========================================
        // NUEVO: marcar reserva como aprobada
        // ==========================================
        $this->model->aprobarReservaCertificado($cert_number, (int)$preval_id);

        $this->responderJSON([
            'msg' => 'Datos prevalidados correctamente.',
            'cert_number' => $cert_number
        ], 'success');
    }

    // =========================================================
    // PASO 2 — PREVALIDAR FOTOS
    // =========================================================
    public function prevalidarFotos()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON('Solicitud inválida', 'error');
            return;
        }

        $preval_id = $_SESSION['preval_id'] ?? null;
        if (!$preval_id) {
            $this->responderJSON('Debes prevalidar los datos primero.', 'error');
            return;
        }

        if (!$this->prevalModel->datosFueronAprobados($preval_id)) {
            $this->responderJSON('Los datos no fueron aprobados. Recarga el formulario.', 'error');
            return;
        }

        // Recuperar el mismo folio reservado
        $reserva = $this->model->obtenerCertNumberPorPrevalId((int)$preval_id);
        $cert_number = $reserva['cert_number'] ?? null;

        if (!$cert_number) {
            $this->responderJSON('Sesión expirada o folio no disponible. Recarga el formulario.', 'error');
            return;
        }

        $_SESSION['preval_cert_number'] = $cert_number;

        $vin      = trim($_POST['vin'] ?? '');
        $archivos = $_FILES['imagenes'] ?? null;

        if (!$archivos || !is_array($archivos['tmp_name']) || count($archivos['tmp_name']) !== 8) {
            $this->responderJSON('Se requieren exactamente 8 fotos.', 'warning');
            return;
        }

        $fotoMap = [
            0 => 'vindash', // 1) Foto VIN
            1 => 'front',   // 2) Foto Frente
            2 => 'back',    // 3) Foto Atrás
            3 => 'left',    // 4) Foto Piloto
            4 => 'right',   // 5) Foto Pasajero
            5 => 'label',   // 6) Foto Puerta
            6 => 'device',  // 7) Foto Scanner
            7 => 'device2', // 8) Foto Taller
        ];

        $fotosRutas = [];
        $dirTemp    = BASE_PATH . 'uploads/temp/';
        if (!is_dir($dirTemp)) {
            mkdir($dirTemp, 0775, true);
        }

        for ($i = 0; $i < 8; $i++) {
            $tmpName  = $archivos['tmp_name'][$i] ?? '';
            $origName = basename($archivos['name'][$i] ?? '');

            if (!is_uploaded_file($tmpName)) {
                $this->responderJSON("Foto #" . ($i + 1) . " no válida.", 'warning');
                return;
            }

            $destFull = BASE_PATH . 'uploads/temp/' . $cert_number . '_foto_' . $i . '_' . $origName;

            if (!move_uploaded_file($tmpName, $destFull)) {
                $this->responderJSON("No fue posible guardar la foto #" . ($i + 1) . ".", 'error');
                return;
            }

            $fotosRutas[$fotoMap[$i]] = $destFull;
        }

        $_SESSION['preval_fotos'] = $fotosRutas;

        $respuesta = $this->secomext->enviarFotos($cert_number, $vin, $fotosRutas);

        $success  = (bool)($respuesta['success'] ?? false);
        $mensaje  = trim((string)($respuesta['respuesta'] ?? 'Error desconocido al prevalidar fotos.'));
        $mensajeL = mb_strtolower($mensaje, 'UTF-8');

        // Detectar errores de transporte / timeout / red
        $esErrorTemporal =
            str_contains($mensajeL, 'timed out') ||
            str_contains($mensajeL, 'timeout') ||
            str_contains($mensajeL, 'socket read') ||
            str_contains($mensajeL, 'http error') ||
            str_contains($mensajeL, 'could not connect') ||
            str_contains($mensajeL, 'connection refused') ||
            str_contains($mensajeL, 'failed to load external entity') ||
            str_contains($mensajeL, 'error fetching http headers');

        $this->prevalModel->actualizarPasoFotos(
            $preval_id,
            $success ? 'success' : 'failed',
            $mensaje
        );

        if (!$success) {
            // Si fue timeout o error temporal, NO romper el flujo
            if ($esErrorTemporal) {
                $this->responderJSON(
                    'Secomext no respondió a tiempo durante la prevalidación de fotos. Puedes reintentar nuevamente sin volver a prevalidar los datos.',
                    'warning'
                );
                return;
            }

            // Solo si fue rechazo real, aquí sí limpiamos todo
            $this->model->liberarReservaCertificado(
                $cert_number,
                (int)$preval_id,
                'Fallo real en prevalidación de fotos'
            );

            $this->limpiarTemporales($cert_number);
            unset($_SESSION['preval_fotos'], $_SESSION['preval_id'], $_SESSION['preval_cert_number']);

            $this->responderJSON('Secomext rechazó las fotos: ' . $mensaje, 'error');
            return;
        }

        $this->responderJSON('Fotos prevalidadas correctamente.', 'success');
    }

    // =========================================================
    // PASO 3 — CREAR CERTIFICADO
    // =========================================================
    public function crear()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode([
                    'msg'   => 'Solicitud inválida',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $preval_id = $_SESSION['preval_id'] ?? null;
            if (!$preval_id) {
                echo json_encode([
                    'msg'   => 'Debes completar la prevalidación primero.',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            if (!$this->prevalModel->fueronAprobadosAmbos($preval_id)) {
                echo json_encode([
                    'msg'   => 'La prevalidación no está completa.',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $fotosRutas = $_SESSION['preval_fotos'] ?? [];
            if (count($fotosRutas) !== 8) {
                echo json_encode([
                    'msg'   => 'Las fotos prevalidadas no están disponibles. Recarga el formulario.',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $id_usuario    = (int)($_SESSION['id_usuario'] ?? 0);
            $vin           = trim($_POST['vin'] ?? '');
            $year          = trim($_POST['year'] ?? '');
            $make          = trim($_POST['marca'] ?? '');
            $model         = trim($_POST['modelo'] ?? '');
            $mfg_in        = trim($_POST['fabricado_en'] ?? '');
            $license_plate = trim($_POST['placa'] ?? '');
            $owner_name    = trim($_POST['propietario'] ?? '');
            $odometer      = trim($_POST['odometro'] ?? '');
            $test_date     = trim($_POST['fecha'] ?? '');
            $expires       = date('Y-m-d', strtotime('+3 months', strtotime($test_date)));
            $source_file   = 'manual';
            $phone         = trim($_POST['telefono'] ?? '');
            $phone         = $phone !== '' ? $phone : null;
            $cliente_id = isset($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : 0;

            if ($cliente_id <= 0) {
                echo json_encode([
                    'msg'   => 'Debes seleccionar un cliente.',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $ebitn = isset($_POST['ebitn']) && trim($_POST['ebitn']) !== ''
                ? trim($_POST['ebitn'])
                : null;

            $monitoreos = [
                'Fallo Encendido'      => $_POST['monitor_fallo_encendido'] ?? '',
                'Sistema Combustible'  => $_POST['monitor_sistema_combustible'] ?? '',
                'Catalizador Integral' => $_POST['monitor_integral_catalizador'] ?? '',
                'Catalizador'          => $_POST['monitor_catalizador'] ?? '',
                'Sensor C2'            => $_POST['monitor_sensor_c2'] ?? '',
                'Resultado General'    => $_POST['resultado_prueba'] ?? '',
            ];

            $latitud  = $_POST['latitud']  ?? null;
            $longitud = $_POST['longitud'] ?? null;

            if (!is_numeric($_POST['inspector'] ?? null)) {
                echo json_encode([
                    'msg'   => 'Inspector no válido.',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Recuperar el mismo folio reservado
            $reserva = $this->model->obtenerCertNumberPorPrevalId((int)$preval_id);
            $cert_number = $reserva['cert_number'] ?? null;

            if (!$cert_number) {
                echo json_encode([
                    'msg'   => 'No fue posible recuperar el número de certificado reservado.',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Validar que todavía no exista en certificates
            $existe = $this->model->consultarCertificado($cert_number);
            if ($existe) {
                echo json_encode([
                    'msg'   => 'El número de certificado ya existe. Intenta nuevamente.',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Dirección
            if (!empty($_POST['direccion_existente'])) {
                $address_id = (int)$_POST['direccion_existente'];
            } else {
                $direccion = $this->model->consultarDireccionConCoordenadas(
                    $_POST['numero'] ?? null,
                    $_POST['calle'] ?? null,
                    $_POST['ciudad'] ?? null,
                    $_POST['estado'] ?? null,
                    $_POST['zip'] ?? null,
                    $latitud,
                    $longitud
                );

                $address_id = $direccion
                    ? (int)$direccion['id']
                    : (int)$this->model->insertarDireccionConCoordenadas(
                        $_POST['numero'] ?? null,
                        $_POST['calle'] ?? null,
                        $_POST['ciudad'] ?? null,
                        $_POST['estado'] ?? null,
                        $_POST['zip'] ?? null,
                        $latitud,
                        $longitud
                    );
            }

            if (!$address_id) {
                echo json_encode([
                    'msg'   => 'No fue posible resolver la dirección del certificado.',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Normalizar / renombrar fotos temporales con el folio final
            $fotosRutasRenombradas = [];
            foreach ($fotosRutas as $campo => $rutaAntigua) {
                if (!file_exists($rutaAntigua)) {
                    echo json_encode([
                        'msg'   => 'No se encontraron todas las fotos prevalidadas.',
                        'icono' => 'error'
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                $baseName = basename($rutaAntigua);
                $nombreNuevo = preg_replace('/MEX-\d{8}/', $cert_number, $baseName);

                if ($nombreNuevo === $baseName) {
                    $nombreNuevo = $cert_number . '_' . preg_replace('/^MEX-\d+_?/', '', $baseName);
                }

                $rutaNueva = BASE_PATH . 'uploads/temp/' . $nombreNuevo;

                if ($rutaAntigua !== $rutaNueva) {
                    if (!@rename($rutaAntigua, $rutaNueva)) {
                        echo json_encode([
                            'msg'   => 'No fue posible preparar las fotos del certificado.',
                            'icono' => 'error'
                        ], JSON_UNESCAPED_UNICODE);
                        exit;
                    }
                } else {
                    $rutaNueva = $rutaAntigua;
                }

                $fotosRutasRenombradas[$campo] = $rutaNueva;
            }
            $fotosRutas = $fotosRutasRenombradas;

            // Rutas FINALES, no temporales
            $dirCerts = BASE_PATH . 'uploads/certificates/';
            if (!is_dir($dirCerts) && !mkdir($dirCerts, 0775, true)) {
                echo json_encode([
                    'msg'   => 'No fue posible crear la carpeta de certificados.',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $pdfRelPath  = 'uploads/certificates/' . $cert_number . '.pdf';
            $zipRelPath  = 'uploads/certificates/' . $cert_number . '.zip';
            $pdfFullPath = BASE_PATH . $pdfRelPath;
            $zipFullPath = BASE_PATH . $zipRelPath;

            // 1) Generar PDF primero
            $postDataForPdf = $_POST;
            $postDataForPdf['fecha_expiracion'] = $expires;

            try {
                $tempImgs = $this->generarCertificadoPDF(
                    $cert_number,
                    $pdfFullPath,
                    $postDataForPdf,
                    $address_id,
                    $fotosRutas
                );

                if (!file_exists($pdfFullPath) || filesize($pdfFullPath) <= 0) {
                    throw new Exception('El PDF no fue creado correctamente.');
                }
            } catch (\Throwable $e) {
                error_log("❌ generarCertificadoPDF: " . $e->getMessage() . ' L' . $e->getLine());

                echo json_encode([
                    'msg'   => 'Error generando PDF: ' . $e->getMessage(),
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // 2) Generar ZIP después
            try {
                $this->generarArchivoZIP($cert_number, $pdfFullPath, $fotosRutas);

                if (!file_exists($zipFullPath) || filesize($zipFullPath) <= 0) {
                    throw new Exception('El ZIP no fue creado correctamente.');
                }
            } catch (\Throwable $e) {
                error_log("❌ generarArchivoZIP: " . $e->getMessage());

                // Si falla ZIP, también eliminar PDF final para no dejar basura incompleta
                if (file_exists($pdfFullPath)) {
                    @unlink($pdfFullPath);
                }

                echo json_encode([
                    'msg'   => 'Error generando ZIP: ' . $e->getMessage(),
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // 3) Solo si PDF y ZIP ya existen, insertar en BD
            $insert = $this->model->insertarCertificado(
                $cert_number,
                $vin,
                $address_id,
                $phone,
                $year,
                $mfg_in,
                $make,
                $owner_name,
                $model,
                $license_plate,
                $odometer,
                (int)$_POST['inspector'],
                $ebitn,
                $id_usuario,
                $cliente_id,
                $test_date,
                $expires,
                $source_file
            );

            if (!$insert) {
                // Si falla el insert, limpiar archivos finales para no dejar inconsistencias
                if (file_exists($pdfFullPath)) {
                    @unlink($pdfFullPath);
                }
                if (file_exists($zipFullPath)) {
                    @unlink($zipFullPath);
                }

                echo json_encode([
                    'msg'   => 'Error al crear el certificado en base de datos.',
                    'icono' => 'error'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            foreach ($monitoreos as $tipo => $resultado) {
                $this->model->insertarMonitoreo($cert_number, $tipo, $resultado);
            }

            try {
                $this->model->registrarImportacion(
                    'cert_manual_' . date('YmdHis'),
                    'success',
                    '',
                    $id_usuario
                );
            } catch (\Throwable $e) {
                $this->model->registrarImportacionAlternativa(
                    'cert_manual_' . date('YmdHis'),
                    'success',
                    ''
                );
            }

            // API externa: no tumbar la creación si falla
            try {
                $apiResp = $this->enviarCertificadoSmogsBackups(
                    $cert_number,
                    $_POST,
                    $address_id,
                    $pdfFullPath,
                    $tempImgs ?? [],
                    $fotosRutas
                );
            } catch (\Throwable $e) {
                $apiResp = [
                    'ok'      => false,
                    'status'  => 0,
                    'api_msg' => $e->getMessage()
                ];
            }

            $this->prevalModel->vincularCertificado($preval_id, $cert_number);
            $this->model->marcarReservaComoUsada($cert_number, (int)$preval_id);

            $_SESSION['preval_cert_number'] = $cert_number;

            unset($_SESSION['preval_id'], $_SESSION['preval_fotos']);

            // OJO:
            // limpiarTemporales($cert_number) solo debe borrar QR/SVG/barcodes/fotos temp,
            // pero NO el PDF final porque ahora ya está en uploads/certificates/
            $this->limpiarTemporales($cert_number);

            echo json_encode([
                'msg'         => 'Certificado creado exitosamente',
                'icono'       => 'success',
                'pdf_url'     => BASE_URL . $pdfRelPath,
                'zip_url'     => BASE_URL . $zipRelPath,
                'api_ok'      => $apiResp['ok']      ?? false,
                'api_status'  => $apiResp['status']  ?? 0,
                'api_msg'     => $apiResp['api_msg'] ?? '',
                'cert_number' => $cert_number,
            ], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (\Throwable $e) {
            error_log("❌ Error fatal en crear(): " . $e->getMessage() . ' L' . $e->getLine());

            echo json_encode([
                'msg'   => 'Ocurrió un error inesperado al crear el certificado: ' . $e->getMessage(),
                'icono' => 'error'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }






    // =========================================================
    // GENERAR PDF con mPDF
    //
    // INSTALACIÓN (solo una vez):
    //   composer require mpdf/mpdf
    //
    // CAMBIO en los use al inicio del controlador:
    //   Quitar:  use Dompdf\Dompdf;
    //            use Dompdf\Options;
    //   Agregar: use Mpdf\Mpdf;
    // =========================================================
    private function generarCertificadoPDF(
        $cert_number,
        $pdf_path,
        $data,
        $address_id,
        array $fotosRutas = []
    ): array {

        // ── Inspector ─────────────────────────────────────
        $inspector_id   = is_numeric($data['inspector'])
            ? $data['inspector']
            : $this->model->insertarInspector($data['inspector']);
        $inspector      = $this->model->obtenerInspectorPorId($inspector_id);
        $inspector_name = $inspector ? $inspector['name'] : $data['inspector'];

        $firmaRelPath  = $inspector['direccion_firma'] ?? '';
        $firmaFullPath = BASE_PATH . $firmaRelPath;
        $firmaWebPath  = BASE_URL  . $firmaRelPath;
        $firma_path    = file_exists($firmaFullPath) ? $firmaWebPath : '';

        $logoPath = BASE_URL . 'assets/images/logo.png';

        // ── QR ────────────────────────────────────────────
        $qrPathRel      = 'uploads/temp/' . $cert_number . '_qr.svg';
        $qrWebPath      = BASE_URL  . $qrPathRel;
        $qrFileFullPath = BASE_PATH . $qrPathRel;

        $contenidoQR = "{$data['vin']}|{$data['propietario']}|{$data['fabricado_en']}|{$data['year']}|{$data['modelo']}|{$data['marca']}";
        (new QRCode(new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'   => QRCode::ECC_L,
        ])))->render($contenidoQR, $qrFileFullPath);

        // ── Dirección ─────────────────────────────────────
        $dir             = $this->model->obtenerDireccionPorId($address_id);
        $direccion_texto = "{$dir['number']} {$dir['street']}, {$dir['city']}, {$dir['state']} {$dir['zip']}";

        // ── Barcodes SVG ──────────────────────────────────
        $bc = new BarcodeGeneratorSVG();
        file_put_contents(
            BASE_PATH . "uploads/temp/{$cert_number}_vin_barcode.svg",
            $bc->getBarcode($data['vin'],  $bc::TYPE_CODE_128)
        );
        file_put_contents(
            BASE_PATH . "uploads/temp/{$cert_number}_cert_barcode.svg",
            $bc->getBarcode($cert_number, $bc::TYPE_CODE_128)
        );

        $vinBarcodeWeb  = BASE_URL . "uploads/temp/{$cert_number}_vin_barcode.svg";
        $certBarcodeWeb = BASE_URL . "uploads/temp/{$cert_number}_cert_barcode.svg";

        // ── Fotos ─────────────────────────────────────────
        $fotosCeldas = '';
        $tempImgs    = [];
        $i           = 0;
        foreach ($fotosRutas as $campo => $rutaFisica) {
            if (file_exists($rutaFisica)) {
                $rutaWeb      = BASE_URL . 'uploads/temp/' . basename($rutaFisica);
                $fotosCeldas .= '<td style="padding:1mm;width:33mm;">'
                    . '<img src="' . $rutaWeb . '" style="width:32mm;height:24mm;display:block;">'
                    . '</td>';
                $tempImgs[] = ['index' => $i, 'name' => basename($rutaFisica), 'path' => $rutaFisica];
            }
            $i++;
        }

        // ── Firma ─────────────────────────────────────────
        $firmaHtml = $firma_path
            ? '<img src="' . $firma_path . '" style="height:12mm;max-width:40mm;">'
            : '<span style="font-family:Helvetica;font-size:13pt;color:#5500bb;font-style:italic;">'
            . htmlspecialchars($inspector_name) . '</span>';

        // ── Helpers resultado ─────────────────────────────
        $rc = function ($v) {
            return strtolower(trim($v)) === 'aprobado' ? '#1a7a1a' : '#cc0000';
        };
        $rs = function ($v) use ($rc) {
            return 'color:' . $rc($v) . ';font-weight:bold;';
        };

        $km    = is_numeric($data['odometro'])
            ? '&nbsp;&nbsp;' . number_format($data['odometro'] * 1.60934, 2) . ' km'
            : '';
        $ebitn = htmlspecialchars($data['ebitn'] ?? '');

        // ── Mapa real OpenStreetMap (tiles descargados, sin API key) ──
        $lat        = (float)$data['latitud'];
        $lon        = (float)$data['longitud'];
        $mapImgPath = BASE_PATH . "uploads/temp/{$cert_number}_map.png";
        $zoom       = 15;

        // Convertir lat/lon a número de tile
        $tileX = (int)floor(($lon + 180) / 360 * pow(2, $zoom));
        $tileY = (int)floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / M_PI) / 2 * pow(2, $zoom));

        // Descargar 3x3 tiles para tener contexto alrededor del pin
        $tileSize  = 256;
        $gridSize  = 3; // 3x3 tiles
        $imgWidth  = $tileSize * $gridSize; // 768px
        $imgHeight = $tileSize * $gridSize; // 768px

        $mapa = imagecreatetruecolor($imgWidth, $imgHeight);

        for ($dx = -1; $dx <= 1; $dx++) {
            for ($dy = -1; $dy <= 1; $dy++) {
                $tx = $tileX + $dx;
                $ty = $tileY + $dy;

                // Rotar entre servidores a,b,c para no saturar uno solo
                $servers = ['a', 'b', 'c'];
                $srv     = $servers[abs($tx + $ty) % 3];
                $tileUrl = "https://{$srv}.tile.openstreetmap.org/{$zoom}/{$tx}/{$ty}.png";

                $ch = curl_init($tileUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 8);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                // OSM requiere User-Agent identificado
                curl_setopt($ch, CURLOPT_USERAGENT, 'MechanicalEmissionsServices/1.0 (certificados@mecemissions.com)');
                $tileData = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($tileData && $httpCode === 200) {
                    $tileImg = @imagecreatefromstring($tileData);
                    if ($tileImg) {
                        $destX = ($dx + 1) * $tileSize;
                        $destY = ($dy + 1) * $tileSize;
                        imagecopy($mapa, $tileImg, $destX, $destY, 0, 0, $tileSize, $tileSize);
                        imagedestroy($tileImg);
                    }
                }
            }
        }

        // Calcular posición exacta del pin dentro del mapa 3x3
        $centerTilePixelX = ($lon / 360 + 0.5) * pow(2, $zoom) * $tileSize;
        $centerTilePixelY = (1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / M_PI) / 2 * pow(2, $zoom) * $tileSize;

        $originPixelX = $tileX * $tileSize;
        $originPixelY = $tileY * $tileSize;

        $pinX = (int)(($centerTilePixelX - $originPixelX) + $tileSize); // +tileSize por el offset del tile -1
        $pinY = (int)(($centerTilePixelY - $originPixelY) + $tileSize);

        // Dibujar pin rojo
        $red    = imagecolorallocate($mapa, 204, 0, 0);
        $white  = imagecolorallocate($mapa, 255, 255, 255);
        $dark   = imagecolorallocate($mapa, 100, 0, 0);

        // Sombra
        imagefilledellipse($mapa, $pinX + 2, $pinY + 2, 22, 22, imagecolorallocatealpha($mapa, 0, 0, 0, 80));
        // Círculo exterior
        imagefilledellipse($mapa, $pinX, $pinY, 24, 24, $red);
        imageellipse($mapa, $pinX, $pinY, 24, 24, $dark);
        // Punto blanco interior
        imagefilledellipse($mapa, $pinX, $pinY, 10, 10, $white);

        // Barra de coordenadas abajo
        $navy = imagecolorallocatealpha($mapa, 26, 58, 92, 40);
        imagefilledrectangle($mapa, 0, $imgHeight - 28, $imgWidth, $imgHeight, $navy);
        $coordText = number_format($lat, 5) . ', ' . number_format($lon, 5);
        imagestring($mapa, 3, ($imgWidth / 2) - (strlen($coordText) * 4), $imgHeight - 20, $coordText, $white);

        // Recortar al centro para output más pequeño (400x300 centrado en el pin)
        $outputW  = 400;
        $outputH  = 300;
        $cropX    = max(0, $pinX - $outputW / 2);
        $cropY    = max(0, $pinY - $outputH / 2);
        $cropped  = imagecreatetruecolor($outputW, $outputH);
        imagecopy($cropped, $mapa, 0, 0, $cropX, $cropY, $outputW, $outputH);

        imagepng($cropped, $mapImgPath);
        imagedestroy($mapa);
        imagedestroy($cropped);

        $mapHtml = '<img src="' . $mapImgPath . '" style="width:55mm;height:41mm;display:block;border:0.3mm solid #aaa;">';

        // =========================================================
        // HTML
        // =========================================================
        $html = '
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 8pt;
                color: #1a1a1a;
            }
            table { border-collapse: collapse; width: 100%; }
            td    { font-size: 8pt; vertical-align: middle; padding: 2.5mm 3mm; }

            .L {
                background-color: #e8edf2;
                font-weight: bold;
                color: #1a3a5c;
                border: 0.3mm solid #b0bec5;
                white-space: nowrap;
                font-size: 7.5pt;
            }
            .V {
                background-color: #ffffff;
                border: 0.3mm solid #b0bec5;
                color: #222;
            }
            .SEC {
                background-color: #1a3a5c;
                color: #ffffff;
                font-weight: bold;
                font-size: 8.5pt;
                letter-spacing: 0.3pt;
                padding: 2.5mm 4mm;
            }
            .emp-name {
                font-size: 11pt;
                font-weight: bold;
                color: #1a3a5c;
                line-height: 1.5;
            }
            .emp-aval {
                color: #cc0000;
                font-size: 8pt;
                font-style: italic;
                margin-top: 1mm;
            }
        </style>

        <!-- ══════════════════════════════════════════════════
             CABECERA: NIV barcode | Empresa | Cert# barcode
        ═════════════════════════════════════════════════════ -->
        <table style="margin-bottom:3mm;border:none;">
            <tr>
                <td style="width:72mm;border:none;text-align:center;vertical-align:bottom;padding:0 0 1mm 0;">
                    <img src="' . $vinBarcodeWeb . '" style="width:71mm;height:14mm;display:block;margin:0 auto;">
                    <div style="font-size:7pt;font-weight:bold;text-align:left;margin-top:0.5mm;color:#555;">NIV</div>
                </td>
                
                <td style="border:none;text-align:center;vertical-align:middle;padding:0 5mm;">
                    <div class="emp-name" style="text-align:center;">
                        Mechanical Emissions Services LLC
                    </div>
                    <table style="width:100%;border:none;margin-top:1mm;">
                        <tr>
                            <td style="border:none;vertical-align:top;text-align:left;padding:0 2mm 0 0;width:50%;font-size:7.5pt;color:#1a3a5c;line-height:1.6;">
                                <span style="font-weight:bold;font-size:7pt;color:#cc0000;">MX</span><br>
                               Cayetano Pérez 240, Buena Vista, Burocrata Ruiz Cortinez,<br>22406 Tijuana, B.C.
                            </td>
                            <td style="border:none;border-left:0.3mm solid #b0bec5;vertical-align:top;text-align:left;padding:0 0 0 2mm;width:50%;font-size:7.5pt;color:#1a3a5c;line-height:1.6;">
                                <span style="font-weight:bold;font-size:7pt;color:#cc0000;">USA</span><br>
                                910 Highland Avenue,<br>National City, CA 91950
                            </td>
                        </tr>
                    </table>
                    <div class="emp-aval" style="margin-top:1mm;">Avalado por -- Mechanical Emissions Services</div>
                </td>
                    <div class="emp-aval">Avalado por -- Mechanical Emissions Services</div>
                </td>
                <td style="width:72mm;border:none;text-align:center;vertical-align:bottom;padding:0 0 1mm 0;">
                    <img src="' . $certBarcodeWeb . '" style="width:71mm;height:14mm;display:block;margin:0 auto;">
                    <div style="font-size:7pt;font-weight:bold;text-align:right;margin-top:0.5mm;color:#555;">Cert#</div>
                </td>
            </tr>
        </table>

        <!-- ══════════════════════════════════════════════════
             SECCIÓN: Información Del Vehículo
        ═════════════════════════════════════════════════════ -->
        <table style="margin-bottom:0;border:none;">
            <tr>
                <td class="SEC" colspan="6">Información Del Vehículo</td>
            </tr>
            <tr>
                <td class="L" style="width:50mm;">VIN/NIV</td>
                <td class="V" style="width:48mm;">' . htmlspecialchars($data['vin']) . '</td>
                <td class="L" style="width:11mm;">Year/Año</td>
                <td class="V" style="width:20mm;">' . htmlspecialchars($data['year']) . '</td>
                <td class="V" colspan="2" style="border:0.3mm solid #b0bec5;background:#fff;"></td>
            </tr>
            <tr>
                <td class="L">MFG In/ Fabricado en</td>
                <td class="V">' . htmlspecialchars($data['fabricado_en']) . '</td>
                <td class="L">Make/Marca</td>
                <td class="V">' . htmlspecialchars($data['marca']) . '</td>
                <td class="L" style="width:14mm;">Owner/Propietario</td>
                <td class="V">' . htmlspecialchars($data['propietario']) . '</td>
            </tr>
            <tr>
                <td class="L">Model/Modelo</td>
                <td class="V">' . htmlspecialchars($data['modelo']) . '</td>
                <td class="L">License Plate/Placas</td>
                <td class="V">' . htmlspecialchars($data['placa']) . '</td>
                <td class="L">Odometer/Odómetro (mi)</td>
                <td class="V">' . htmlspecialchars($data['odometro']) . ' mi  - ' . $km . '</td>
            </tr>
        </table>

        <!-- ══════════════════════════════════════════════════
             SECCIÓN: Monitoreo Y Certificado
             8 columnas — SIN rowspan, firma y EEI en celdas propias
             CAMBIO 1: rowspan eliminado de firma
             CAMBIO 2: EEI ITN tiene su propio <td class="V">
             CAMBIO 3: Fecha y Vencimiento en filas separadas
        ═════════════════════════════════════════════════════ -->
        <table style="margin-top:2mm;border:none;">
            <tr>
                <td class="SEC" colspan="8">Información De Monitoreo Y Certificado</td>
            </tr>

            <!-- Fila 1: Monitores + Cert Number + Firma -->
            <tr>
                <td class="L" style="width:41mm;">Misfire Monitoring/Monitor Encendido</td>
                <td class="V" style="width:19mm;' . $rs($data['monitor_fallo_encendido']) . '">'
            . htmlspecialchars($data['monitor_fallo_encendido']) . '</td>
                <td class="L" style="width:35mm;">Fuel System Monitoring/Monitor Combustible</td>
                <td class="V" style="width:19mm;' . $rs($data['monitor_sistema_combustible']) . '">'
            . htmlspecialchars($data['monitor_sistema_combustible']) . '</td>
                <td class="L" style="width:38mm;">Cert # /Numero De Certificado</td>
                <td class="V" style="width:32mm;">' . htmlspecialchars($cert_number) . '</td>
                <td class="L" style="width:24mm;">Inspector Signatura/Firma Inspector</td>
                <td class="V" style="text-align:center;vertical-align:middle;background:#fffcf0;border:0.3mm solid #b0bec5;">'
            . $firmaHtml . '</td>
            </tr>

            <!-- Fila 2: Monitores + Nombre Inspector + EEI ITN -->
            <tr>
                <td class="L">Comprehensive Monitor Catalyst/Monitor Exhaustivo Catalizador</td>
                <td class="V" style="' . $rs($data['monitor_integral_catalizador']) . '">'
            . htmlspecialchars($data['monitor_integral_catalizador']) . '</td>
                <td class="L">O2 Sensor Monitor/Monitor Sensor O2</td>
                <td class="V" style="' . $rs($data['monitor_sensor_c2']) . '">'
            . htmlspecialchars($data['monitor_sensor_c2']) . '</td>
                <td class="L">Inspector Name/Nombre Inspector</td>
                <td class="V">' . htmlspecialchars($inspector_name) . '</td>
                <td class="L">EEI ITN</td>
                <td class="V">' . $ebitn . '</td>
            </tr>

            <!-- Fila 3: Monitor Catalizador + Geo + Fecha -->
            <tr>
                <td class="L">Catalyst Monitor/Monitor Catalizador</td>
                <td class="V" style="' . $rs($data['monitor_catalizador']) . '">'
            . htmlspecialchars($data['monitor_catalizador']) . '</td>
                <td class="L">Overall Test Result/Resultado General</td>
                <td class="V" style="' . $rs($data['resultado_prueba']) . '">'
            . htmlspecialchars($data['resultado_prueba']) . '</td>
                <td class="L">Geo Location/Geo Localización</td>
                <td class="V" style="font-size:7.5pt;">
                    Lat: ' . htmlspecialchars($data['latitud']) . '<br>
                    Lon: ' . htmlspecialchars($data['longitud']) . '
                </td>
                <td class="L">Date/Fecha</td>
                <td class="V">' . htmlspecialchars($data['fecha']) . '</td>
            </tr>

            <!-- Fila 4: Vencimiento en su propio cuadro destacado -->
            <tr>
                <td class="L" colspan="6"
                    style="text-align:right;color:#1a3a5c;border:0.3mm solid #b0bec5;background:#e8edf2;">
                    Expires/Fecha de Vencimiento
                </td>
                <td class="V" colspan="2"
                    style="text-align:center;font-weight:bold;font-size:10pt;
                           color:#0c8dd8 ;background:#fff5f5;border:0.5mm solid #0c8dd8;
                           letter-spacing:0.5pt;">
                    ' . htmlspecialchars($data['fecha_expiracion']) . '
                </td>
            </tr>
        </table>

        <!-- ══════════════════════════════════════════════════
             FOTOS
        ═════════════════════════════════════════════════════ -->
        <table style="margin-top:2mm;border-collapse:collapse;">
            <tr>' . $fotosCeldas . '</tr>
        </table>
        <!-- ══════════════════════════════════════════════════
            PIE: QR | Mapa | Texto Semarnat | Logo
        ═════════════════════════════════════════════════════ -->
        <table style="margin-top:3mm;border:none;">
            <tr>
                <td style="width:30mm;text-align:center;vertical-align:bottom;border:none;">
                    <img src="' . $qrWebPath . '" style="width:28mm;height:28mm;">
                </td>
                <td style="width:58mm;text-align:center;vertical-align:bottom;border:none;padding:0 2mm;">
                    ' . $mapHtml . '
                    <div style="font-size:6.5pt;color:#555;margin-top:0.5mm;">
                        Lat: ' . htmlspecialchars($lat) . ' | Lon: ' . htmlspecialchars($lon) . '
                    </div>
                </td>
                <!--<td style="vertical-align:bottom;padding:0 4mm;font-size:7pt;line-height:1.8;color:#444;border:none;">
                    <span style="color:#1a3a5c;font-weight:bold;font-size:7.5pt;">Semarnat Autorization Letter</span><br>
                    No. SRA.600/DPRA/DPMR/377/2022<br>
                    CA BAR 97 GEN3 and/or Drew Technologies IMClean<br>
                    ESP 10400-89 Software Version: 16028007727838<br>
                    To verify this Certificate please go to:<br>
                    <span style="color:#0055aa;">www.mecemissions.com</span>
                </td> -->
                <td style="width:45mm;text-align:right;vertical-align:bottom;border:none;">
                    <img src="' . $logoPath . '" style="height:24mm;">
                </td>
            </tr>
        </table>
        ';

        // ── mPDF ──────────────────────────────────────────
        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => 'A4-L',
            'margin_top'        => 6,
            'margin_bottom'     => 6,
            'margin_left'       => 6,
            'margin_right'      => 6,
            'default_font_size' => 8,
            'default_font'      => 'Arial',
            'tempDir'           => BASE_PATH . 'uploads/temp/',
            'basepath'          => BASE_PATH,
        ]);

        $mpdf->SetTitle('Certificado ' . $cert_number);
        $mpdf->WriteHTML($html);
        $mpdf->Output($pdf_path, 'F');

        return $tempImgs;
    }
    // =========================================================
    // GENERAR ZIP (sin cambios)
    // =========================================================
    private function generarArchivoZIP(string $cert_number, string $pdfFullPath, array $fotosRutas = []): void
    {
        $zipPath = BASE_PATH . 'uploads/certificates/' . $cert_number . '.zip';

        $zip = new ZipArchive();
        $res = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($res !== true) {
            throw new Exception('No se pudo abrir/crear el ZIP. Código: ' . $res);
        }

        if (!file_exists($pdfFullPath)) {
            $zip->close();
            throw new Exception('No existe el PDF para agregar al ZIP.');
        }

        if (!$zip->addFile($pdfFullPath, $cert_number . '.pdf')) {
            $zip->close();
            throw new Exception('No se pudo agregar el PDF al ZIP.');
        }

        foreach ($fotosRutas as $campo => $rutaFoto) {
            if (!file_exists($rutaFoto)) {
                continue;
            }

            $ext = pathinfo($rutaFoto, PATHINFO_EXTENSION);
            $nombreDentroZip = $campo . ($ext ? '.' . $ext : '');

            if (!$zip->addFile($rutaFoto, $nombreDentroZip)) {
                $zip->close();
                throw new Exception('No se pudo agregar la foto "' . $campo . '" al ZIP.');
            }
        }

        if (!$zip->close()) {
            throw new Exception('No se pudo cerrar correctamente el ZIP.');
        }

        if (!file_exists($zipPath) || filesize($zipPath) <= 0) {
            throw new Exception('El ZIP se generó vacío o inválido.');
        }
    }

    // =========================================================
    // ENVIAR A SMOGS BACKUPS (sin cambios)
    // =========================================================
    private function enviarCertificadoSmogsBackups(
        string $cert_number,
        array $post,
        int $address_id,
        string $pdfFullPath,
        array $tempImgs,
        array $fotosRutas = []
    ): array {
        $direccion = $this->model->obtenerDireccionPorId($address_id);

        $toPassFail = function ($val): string {
            $v = strtoupper(trim((string)$val));
            return ($v === 'PASA' || $v === 'PASS' || $v === 'P') ? 'PASS' : 'FAIL';
        };

        $fmtFecha = function ($s): string {
            $s = trim((string)$s);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) {
                [$y, $m, $d] = explode('-', $s);
                return "$d-$m-$y";
            }
            return date('d-m-Y');
        };

        $api       = api_client('smogs_backups');
        $cfg       = api_config('smogs_backups');
        $isTesting = (!empty($cfg['mode']) && $cfg['mode'] === 'testing');
        //ENVIAR CORRECTAMENTE AL ENDPOINT DE SMOGS BACKUPS
        $path      = $isTesting ? '/post' : '/Mechanical/Emissions/';

        if (!$isTesting) {
            if (!file_exists($pdfFullPath))   return ['ok' => false, 'status' => 0, 'api_msg' => 'PDF no encontrado'];
            if (count($tempImgs) < 8)         return ['ok' => false, 'status' => 0, 'api_msg' => 'Faltan imágenes'];
        }

        $payload = array_merge([], api_credentials('smogs_backups'));
        $payload['vin']          = $post['vin']      ?? '';
        $payload['odometer']     = $post['odometro'] ?? '';
        $payload['licensePlate'] = $post['placa']    ?? '';
        $payload['folio']        = $cert_number;
        $payload['testFecha']    = $fmtFecha($post['fecha'] ?? '');
        $payload['testHora']     = date('H:i');

        $lat = $direccion['latitude']  ?? ($post['latitud']  ?? '');
        $lon = $direccion['longitude'] ?? ($post['longitud'] ?? '');
        $payload['geolocalizacion']    = trim((string)$lat) . ', ' . trim((string)$lon);

        $payload['testignicion']       = $toPassFail($post['monitor_fallo_encendido']      ?? '');
        $payload['testSistGasolina']   = $toPassFail($post['monitor_sistema_combustible']  ?? '');
        $payload['testCatalizador']    = $toPassFail($post['monitor_catalizador']           ?? '');
        $payload['testSensorOxigeno']  = $toPassFail($post['monitor_sensor_c2']            ?? '');
        $payload['testCompIntegrales'] = $toPassFail($post['monitor_integral_catalizador'] ?? '');
        $payload['testResultadoFinal'] = $toPassFail($post['resultado_prueba']             ?? '');
        $payload['foto_Extension']     = $post['foto_Extension'] ?? 'jpg';

        $fotoMapSmogs = [
            'vindash' => 'fotoVin',
            'front'   => 'fotoFrente',
            'back'    => 'fotoAtras',
            'left'    => 'fotoPiloto',
            'right'   => 'fotoPasajero',
            'label'   => 'fotoPuerta',
            'device'  => 'fotoScanner',
            'device2' => 'fotoTaller',
        ];

        if ($isTesting) {
            $payload['certificadoPdf'] = 'TEST_PDF_BASE64';
            $i = 0;
            foreach ($fotoMapSmogs as $origen => $destino) {
                $payload[$destino] = 'TEST_IMG_BASE64_' . $i;
                $i++;
            }
        } else {
            $payload['certificadoPdf'] = base64_encode(file_get_contents($pdfFullPath));

            foreach ($fotoMapSmogs as $origen => $destino) {
                $ruta = $fotosRutas[$origen] ?? '';

                if (!empty($ruta) && file_exists($ruta)) {
                    $payload[$destino] = base64_encode(file_get_contents($ruta));
                } else {
                    $payload[$destino] = '';
                }
            }
        }
        // ===== DEBUG CREDENCIALES Y PAYLOAD =====
        $endpointUsed = rtrim((string)api_base_url('smogs_backups'), '/') . $path;

        error_log('[SmogsBackups] endpoint=' . $endpointUsed);
        error_log('[SmogsBackups] usuario=' . ($payload['usuario'] ?? ''));
        error_log('[SmogsBackups] password_len=' . strlen((string)($payload['password'] ?? '')));

        // Validar que realmente se estén enviando assets
        error_log('[SmogsBackups] fotoVin=' . (!empty($payload['fotoVin']) ? 'OK' : 'VACIO'));
        error_log('[SmogsBackups] fotoPasajero=' . (!empty($payload['fotoPasajero']) ? 'OK' : 'VACIO'));
        error_log('[SmogsBackups] pdf=' . (!empty($payload['certificadoPdf']) ? 'OK' : 'VACIO'));
        $resp = $api->postUrlEncoded($path, $payload);


        $pdfSha = (!$isTesting && file_exists($pdfFullPath)) ? hash_file('sha256', $pdfFullPath) : '';
        $photosHashes = [];
        if (!$isTesting) {
            foreach ($tempImgs as $img) {
                $p = $img['path'] ?? '';
                if ($p && file_exists($p))
                    $photosHashes[(string)($img['index'] ?? 0)] = hash_file('sha256', $p);
            }
        }

        $payloadSha   = hash('sha256', http_build_query($payload, '', '&', PHP_QUERY_RFC3986));
        $endpointUsed = rtrim((string)api_base_url('smogs_backups'), '/') . $path;
        $mode         = $cfg['mode'] ?? 'production';
        $attempt      = 1;
        error_log('[SmogsBackups] endpoint=' . $endpointUsed);
        error_log('[SmogsBackups] status=' . ($resp['status'] ?? 0) . ' body=' . ($resp['body'] ?? ''));
        error_log('[SmogsBackups] error=' . (($resp['error'] ?? null) ?: 'none'));
        try {
            $attempt = $this->model->siguienteAttemptApiEmissions($cert_number);
        } catch (Exception $e) {
        }

        $apiResult = null;
        $apiDesc = null;
        if (!empty($resp['json']) && is_array($resp['json'])) {
            $apiResult = $resp['json']['result']      ?? ($resp['json']['Resultado']   ?? null);
            $apiDesc   = $resp['json']['Description'] ?? ($resp['json']['descripcion'] ?? ($resp['json']['message'] ?? null));
        }

        try {
            $this->model->insertarApiEmissionsSyncLog([
                'cert_number'        => $cert_number,
                'vin'                => $payload['vin'] ?? '',
                'mode'               => $mode,
                'endpoint'           => $endpointUsed,
                'payload_sha256'     => $payloadSha,
                'pdf_sha256'         => $pdfSha,
                'photos_sha256_json' => json_encode($photosHashes, JSON_UNESCAPED_UNICODE) ?: '{}',
                'http_status'        => (int)($resp['status'] ?? 0),
                'api_result'         => $apiResult,
                'api_description'    => $apiDesc,
                'response_raw'       => (string)($resp['body'] ?? ''),
                'attempt'            => (int)$attempt,
            ]);
        } catch (Exception $e) {
        }

        $apiMsg = '';
        if (!empty($resp['json']))
            $apiMsg = $resp['json']['Description'] ?? ($resp['json']['descripcion'] ?? ($resp['json']['message'] ?? ''));

        return [
            'ok'      => $resp['ok']     ?? false,
            'status'  => $resp['status'] ?? 0,
            'api_msg' => $apiMsg,
            'raw'     => $resp['body']   ?? '',
            'json'    => $resp['json']   ?? null,
        ];
    }

    // =========================================================
    // MÉTODOS AUXILIARES (sin cambios)
    // =========================================================
    public function obtenerFirmaInspector($id)
    {
        $inspector = $this->model->obtenerInspectorPorId($id);
        echo json_encode([
            'firma' => ($inspector && !empty($inspector['direccion_firma']))
                ? BASE_URL . $inspector['direccion_firma'] : ''
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function obtenerLatLon($id = null)
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)$id;
        if ($id <= 0) {
            echo json_encode([
                'latitude'  => '',
                'longitude' => ''
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $dir = $this->model->obtenerDireccionPorId($id);

        echo json_encode([
            'latitude'  => $dir['latitude']  ?? '',
            'longitude' => $dir['longitude'] ?? ''
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function limpiarTemporales($cert_number)
    {
        foreach (glob(BASE_PATH . "uploads/temp/{$cert_number}_*") as $f)
            if (is_file($f)) unlink($f);
    }

    private function responderJSON($mensaje, $icono)
    {
        echo json_encode(['msg' => $mensaje, 'icono' => $icono], JSON_UNESCAPED_UNICODE);
        exit;
    }
    public function obtenerSiguienteCertNumber()
    {
        // SOLO visual / preliminar
        $cert_number = $this->model->obtenerCertNumberPreliminar();

        // Se guarda en sesión solo para el flujo de prevalidación visual,
        // pero NO será el número definitivo al crear.
        $_SESSION['preval_cert_number'] = $cert_number;

        echo json_encode(['cert_number' => $cert_number], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
