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

        $cert_number = $this->model->obtenerCertNumberPreliminar();
        $_SESSION['preval_cert_number'] = $cert_number;

        $data['title']       = 'Crear Certificado';
        $data['cert_number'] = $cert_number;
        $data['direcciones'] = $this->model->obtenerDirecciones();
        $data['inspectores'] = $this->model->obtenerInspectores();
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
        $cert_number = $_SESSION['preval_cert_number'] ?? null;
        if (!$cert_number) {
            $this->responderJSON('Sesión expirada. Recarga el formulario.', 'error');
            return;
        }

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

        $datos = [
            'vin'                               => $vin,
            'cert_number'                       => $cert_number,
            'dmv_number'                        => $cert_number,
            'misfire_monitoring'                => $misfire,
            'fuel_system_monitoring'            => $fuelSystem,
            'comprehensive_catalyst_monitoring' => $compCatalyst,
            'catalyst_monitoring'               => $catalyst,
            'oxygen_sensor_monitoring'          => $oxygenSensor,
            'overall_test_result'               => $overallResult,
            'test_date'                         => $test_date,
            'odometer'                          => $odometer,
            'license_plate'                     => $license_plate,
            'latitude'                          => $latitude,
            'longitude'                         => $longitude,
        ];

        $respuesta = $this->secomext->enviarDatos($datos);

        $this->prevalModel->actualizarPasoDatos(
            $preval_id,
            $respuesta['success'] ? 'success' : 'failed',
            $respuesta['respuesta'],
            $respuesta['success'] ? $cert_number : ''
        );

        if (!$respuesta['success']) {
            unset($_SESSION['preval_id']);
            $this->responderJSON('Secomext rechazó los datos: ' . $respuesta['respuesta'], 'error');
            return;
        }

        $this->responderJSON('Datos prevalidados correctamente.', 'success');
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

        $cert_number = $_SESSION['preval_cert_number'] ?? null;
        if (!$cert_number) {
            $this->responderJSON('Sesión expirada. Recarga el formulario.', 'error');
            return;
        }

        $vin      = trim($_POST['vin'] ?? '');
        $archivos = $_FILES['imagenes'] ?? null;

        if (!$archivos || !is_array($archivos['tmp_name']) || count($archivos['tmp_name']) !== 8) {
            $this->responderJSON('Se requieren exactamente 8 fotos.', 'warning');
            return;
        }

        $fotoMap = [
            0 => 'front',
            1 => 'back',
            2 => 'left',
            3 => 'right',
            4 => 'vindash',
            5 => 'label',
            6 => 'device',
            7 => 'device2',
        ];

        $fotosRutas = [];
        $dirTemp    = BASE_PATH . 'uploads/temp/';
        if (!is_dir($dirTemp)) mkdir($dirTemp, 0775, true);

        for ($i = 0; $i < 8; $i++) {
            $tmpName  = $archivos['tmp_name'][$i] ?? '';
            $origName = basename($archivos['name'][$i] ?? '');

            if (!is_uploaded_file($tmpName)) {
                $this->responderJSON("Foto #" . ($i + 1) . " no válida.", 'warning');
                return;
            }

            $destFull = BASE_PATH . 'uploads/temp/' . $cert_number . '_foto_' . $i . '_' . $origName;
            move_uploaded_file($tmpName, $destFull);
            $fotosRutas[$fotoMap[$i]] = $destFull;
        }

        $_SESSION['preval_fotos'] = $fotosRutas;

        $respuesta = $this->secomext->enviarFotos($cert_number, $vin, $fotosRutas);

        $this->prevalModel->actualizarPasoFotos(
            $preval_id,
            $respuesta['success'] ? 'success' : 'failed',
            $respuesta['respuesta']
        );

        if (!$respuesta['success']) {
            $this->limpiarTemporales($cert_number);
            unset($_SESSION['preval_fotos']);
            $this->responderJSON('Secomext rechazó las fotos: ' . $respuesta['respuesta'], 'error');
            return;
        }

        $this->responderJSON('Fotos prevalidadas correctamente.', 'success');
    }

    // =========================================================
    // PASO 3 — CREAR CERTIFICADO
    // =========================================================
    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responderJSON('Solicitud inválida', 'error');
            return;
        }

        $preval_id = $_SESSION['preval_id'] ?? null;
        if (!$preval_id) {
            $this->responderJSON('Debes completar la prevalidación primero.', 'error');
            return;
        }

        if (!$this->prevalModel->fueronAprobadosAmbos($preval_id)) {
            $this->responderJSON('La prevalidación no está completa.', 'error');
            return;
        }

        $fotosRutas = $_SESSION['preval_fotos'] ?? [];
        if (count($fotosRutas) !== 8) {
            $this->responderJSON('Las fotos prevalidadas no están disponibles. Recarga el formulario.', 'error');
            return;
        }

        $id_usuario    = $_SESSION['id_usuario'] ?? 0;
        $vin           = $_POST['vin'];
        $year          = $_POST['year'];
        $make          = $_POST['marca'];
        $model         = $_POST['modelo'];
        $mfg_in        = $_POST['fabricado_en'];
        $license_plate = $_POST['placa'];
        $owner_name    = $_POST['propietario'];
        $odometer      = $_POST['odometro'];
        $test_date     = $_POST['fecha'];
        $expires       = date('Y-m-d', strtotime('+3 months', strtotime($test_date)));
        $source_file   = 'manual';
        $phone         = $_POST['telefono'] ?? null;

        $monitoreos = [
            'Fallo Encendido'      => $_POST['monitor_fallo_encendido'],
            'Sistema Combustible'  => $_POST['monitor_sistema_combustible'],
            'Catalizador Integral' => $_POST['monitor_integral_catalizador'],
            'Catalizador'          => $_POST['monitor_catalizador'],
            'Sensor C2'            => $_POST['monitor_sensor_c2'],
            'Resultado General'    => $_POST['resultado_prueba'],
        ];

        $latitud  = $_POST['latitud']  ?? null;
        $longitud = $_POST['longitud'] ?? null;

        if (!empty($_POST['direccion_existente'])) {
            $address_id = $_POST['direccion_existente'];
        } else {
            $direccion  = $this->model->consultarDireccionConCoordenadas(
                $_POST['numero'],
                $_POST['calle'],
                $_POST['ciudad'],
                $_POST['estado'],
                $_POST['zip'],
                $latitud,
                $longitud
            );
            $address_id = $direccion
                ? $direccion['id']
                : $this->model->insertarDireccionConCoordenadas(
                    $_POST['numero'],
                    $_POST['calle'],
                    $_POST['ciudad'],
                    $_POST['estado'],
                    $_POST['zip'],
                    $latitud,
                    $longitud
                );
        }

        if (!is_numeric($_POST['inspector'])) {
            $this->responderJSON('Inspector no válido.', 'error');
            return;
        }

        $cert_number = $_SESSION['preval_cert_number'];
        if (!$cert_number) {
            $this->responderJSON('Número de certificado no encontrado en sesión.', 'error');
            return;
        }

        $this->model->generarCertNumberGlobal();

        $fotosRutasRenombradas = [];
        foreach ($fotosRutas as $campo => $rutaAntigua) {
            if (file_exists($rutaAntigua)) {
                $nombreNuevo = preg_replace('/MEX-\d+/', $cert_number, basename($rutaAntigua));
                $rutaNueva   = BASE_PATH . 'uploads/temp/' . $nombreNuevo;
                rename($rutaAntigua, $rutaNueva);
                $fotosRutasRenombradas[$campo] = $rutaNueva;
            }
        }
        $fotosRutas = $fotosRutasRenombradas;

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
            intval($_POST['inspector']),
            $id_usuario,
            $test_date,
            $expires,
            $source_file
        );

        if ($insert === false || $insert === null) {
            unset($_SESSION['preval_cert_number'], $_SESSION['preval_id'], $_SESSION['preval_fotos']);
            $this->responderJSON('Error al crear el certificado.', 'error');
            return;
        }

        foreach ($monitoreos as $tipo => $resultado) {
            $this->model->insertarMonitoreo($cert_number, $tipo, $resultado);
        }

        try {
            $this->model->registrarImportacion('cert_manual_' . date('YmdHis'), 'success', '', $id_usuario);
        } catch (Exception $e) {
            $this->model->registrarImportacionAlternativa('cert_manual_' . date('YmdHis'), 'success', '');
        }

        $pdfRelPath  = 'uploads/temp/' . $cert_number . '.pdf';
        $zipRelPath  = 'uploads/certificates/' . $cert_number . '.zip';
        $pdfFullPath = BASE_PATH . $pdfRelPath;

        try {
            $tempImgs = $this->generarCertificadoPDF($cert_number, $pdfFullPath, $_POST, $address_id, $fotosRutas);
        } catch (\Throwable $e) {
            error_log("❌ generarCertificadoPDF: " . $e->getMessage() . " L" . $e->getLine());
            $this->responderJSON('Error generando PDF: ' . $e->getMessage(), 'error');
            return;
        }

        try {
            $this->generarArchivoZIP($cert_number, $pdfFullPath, $fotosRutas);
        } catch (\Throwable $e) {
            $this->responderJSON('Error generando ZIP: ' . $e->getMessage(), 'error');
            return;
        }

        try {
            $apiResp = $this->enviarCertificadoSmogsBackups($cert_number, $_POST, $address_id, $pdfFullPath, $tempImgs);
        } catch (\Throwable $e) {
            $apiResp = ['ok' => false, 'status' => 0, 'api_msg' => $e->getMessage()];
        }

        $this->prevalModel->vincularCertificado($preval_id, $cert_number);
        unset($_SESSION['preval_id'], $_SESSION['preval_cert_number'], $_SESSION['preval_fotos']);
        $this->limpiarTemporales($cert_number);

        echo json_encode([
            'msg'        => 'Certificado creado exitosamente',
            'icono'      => 'success',
            'pdf_url'    => BASE_URL . $pdfRelPath,
            'zip_url'    => BASE_URL . $zipRelPath,
            'api_ok'     => $apiResp['ok']     ?? false,
            'api_status' => $apiResp['status'] ?? 0,
            'api_msg'    => $apiResp['api_msg'] ?? '',
        ], JSON_UNESCAPED_UNICODE);
        exit;
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
                    <div class="emp-name">
                        Mechanical Emissions Services LLC<br>
                        ' . htmlspecialchars($direccion_texto) . '
                    </div>
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
                <td class="L" style="width:50mm;">Número de Identificación Vehicular</td>
                <td class="V" style="width:48mm;">' . htmlspecialchars($data['vin']) . '</td>
                <td class="L" style="width:11mm;">Año</td>
                <td class="V" style="width:20mm;">' . htmlspecialchars($data['year']) . '</td>
                <td class="V" colspan="2" style="border:0.3mm solid #b0bec5;background:#fff;"></td>
            </tr>
            <tr>
                <td class="L">Fabricado en</td>
                <td class="V">' . htmlspecialchars($data['fabricado_en']) . '</td>
                <td class="L">Marca</td>
                <td class="V">' . htmlspecialchars($data['marca']) . '</td>
                <td class="L" style="width:14mm;">Dueño</td>
                <td class="V">' . htmlspecialchars($data['propietario']) . '</td>
            </tr>
            <tr>
                <td class="L">Modelo</td>
                <td class="V">' . htmlspecialchars($data['modelo']) . '</td>
                <td class="L">Placa</td>
                <td class="V">' . htmlspecialchars($data['placa']) . '</td>
                <td class="L">Odómetro (mi)</td>
                <td class="V">' . htmlspecialchars($data['odometro']) . $km . '</td>
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
                <td class="L" style="width:41mm;">Monitor Encendido</td>
                <td class="V" style="width:19mm;' . $rs($data['monitor_fallo_encendido']) . '">'
            . htmlspecialchars($data['monitor_fallo_encendido']) . '</td>
                <td class="L" style="width:35mm;">Monitor Combustible</td>
                <td class="V" style="width:19mm;' . $rs($data['monitor_sistema_combustible']) . '">'
            . htmlspecialchars($data['monitor_sistema_combustible']) . '</td>
                <td class="L" style="width:38mm;">Numero De Certificado</td>
                <td class="V" style="width:32mm;">' . htmlspecialchars($cert_number) . '</td>
                <td class="L" style="width:24mm;">Firma Inspector</td>
                <td class="V" style="text-align:center;vertical-align:middle;background:#fffcf0;border:0.3mm solid #b0bec5;">'
            . $firmaHtml . '</td>
            </tr>

            <!-- Fila 2: Monitores + Nombre Inspector + EEI ITN -->
            <tr>
                <td class="L">Monitor Exhaustivo Catalizador</td>
                <td class="V" style="' . $rs($data['monitor_integral_catalizador']) . '">'
            . htmlspecialchars($data['monitor_integral_catalizador']) . '</td>
                <td class="L">Monitor Sensor 02</td>
                <td class="V" style="' . $rs($data['monitor_sensor_c2']) . '">'
            . htmlspecialchars($data['monitor_sensor_c2']) . '</td>
                <td class="L">Nombre Inspector</td>
                <td class="V">' . htmlspecialchars($inspector_name) . '</td>
                <td class="L">EEI ITN</td>
                <td class="V">' . $ebitn . '</td>
            </tr>

            <!-- Fila 3: Monitor Catalizador + Geo + Fecha -->
            <tr>
                <td class="L">Monitor Catalizador</td>
                <td class="V" style="' . $rs($data['monitor_catalizador']) . '">'
            . htmlspecialchars($data['monitor_catalizador']) . '</td>
                <td class="L">Resultado General</td>
                <td class="V" style="' . $rs($data['resultado_prueba']) . '">'
            . htmlspecialchars($data['resultado_prueba']) . '</td>
                <td class="L">Geo Localización</td>
                <td class="V" style="font-size:7.5pt;">
                    Lat: ' . htmlspecialchars($data['latitud']) . '<br>
                    Lon: ' . htmlspecialchars($data['longitud']) . '
                </td>
                <td class="L">Fecha</td>
                <td class="V">' . htmlspecialchars($data['fecha']) . '</td>
            </tr>

            <!-- Fila 4: Vencimiento en su propio cuadro destacado -->
            <tr>
                <td class="L" colspan="6"
                    style="text-align:right;color:#1a3a5c;border:0.3mm solid #b0bec5;background:#e8edf2;">
                    Fecha de Vencimiento
                </td>
                <td class="V" colspan="2"
                    style="text-align:center;font-weight:bold;font-size:10pt;
                           color:#cc0000;background:#fff5f5;border:0.5mm solid #cc0000;
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
             PIE: QR | Texto Semarnat | Logo
        ═════════════════════════════════════════════════════ -->
        <table style="margin-top:3mm;border:none;">
            <tr>
                <td style="width:30mm;text-align:center;vertical-align:bottom;border:none;">
                    <img src="' . $qrWebPath . '" style="width:28mm;height:28mm;">
                </td>
              <!--  <td style="vertical-align:bottom;padding:0 5mm;font-size:7pt;line-height:1.8;color:#444;border:none;">
                    <span style="color:#1a3a5c;font-weight:bold;font-size:7.5pt;">Carta de Autorización de Semarnat</span><br>
                    No. SRA.600/DPRA/DPMR/377/2022<br>
                    CA BAR 97 GEN3 and/or Drew Technologies IMClean<br>
                    ESP 10400-89 Versión del Software: 16028007727838<br>
                    Verificar en: <span style="color:#0055aa;">www.mecemissionsmx.com</span>
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
        ]);

        $mpdf->SetTitle('Certificado ' . $cert_number);
        $mpdf->WriteHTML($html);
        $mpdf->Output($pdf_path, 'F');

        return $tempImgs;
    }
    // =========================================================
    // GENERAR ZIP (sin cambios)
    // =========================================================
    private function generarArchivoZIP($cert_number, $pdf_path, array $fotosRutas)
    {
        $zip_path = BASE_PATH . "uploads/certificates/{$cert_number}.zip";
        $dirZip   = dirname($zip_path);
        if (!is_dir($dirZip)) mkdir($dirZip, 0775, true);

        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            if (file_exists($pdf_path)) $zip->addFile($pdf_path, basename($pdf_path));
            foreach ($fotosRutas as $campo => $ruta) {
                if (file_exists($ruta)) $zip->addFile($ruta, 'imagenes/' . basename($ruta));
            }
            $zip->close();
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
        array $tempImgs
    ): array {
        $direccion = $this->model->obtenerDireccionPorId($address_id);

        $toPassFail = function ($val): string {
            $v = strtoupper(trim((string)$val));
            return ($v === 'PASA' || $v === 'PASS') ? 'PASS' : 'FAIL';
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
        $path      = $isTesting ? '/post' : '/Mechanical/Emissions';

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

        $fotoMap = [
            0 => 'fotoVin',
            1 => 'fotoFrente',
            2 => 'fotoAtras',
            3 => 'fotoPiloto',
            4 => 'fotoPasajero',
            5 => 'fotoPuerta',
            6 => 'fotoScanner',
            7 => 'fotoTaller'
        ];

        if ($isTesting) {
            $payload['certificadoPdf'] = 'TEST_PDF_BASE64';
            foreach ($fotoMap as $idx => $field) $payload[$field] = 'TEST_IMG_BASE64_' . $idx;
        } else {
            $payload['certificadoPdf'] = base64_encode(file_get_contents($pdfFullPath));
            foreach ($tempImgs as $img) {
                $ix = (int)($img['index'] ?? -1);
                if ($ix < 0 || !isset($fotoMap[$ix])) continue;
                if (!empty($img['path']) && file_exists($img['path']))
                    $payload[$fotoMap[$ix]] = base64_encode(file_get_contents($img['path']));
            }
        }

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

    public function obtenerLatLon($id)
    {
        $data = $this->model->obtenerDireccionPorId($id);
        echo json_encode([
            'latitude'  => $data['latitude']  ?? '',
            'longitude' => $data['longitude'] ?? '',
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
        $cert_number = $this->model->obtenerCertNumberPreliminar();
        // Actualizar sesión con el nuevo número
        $_SESSION['preval_cert_number'] = $cert_number;
        echo json_encode(['cert_number' => $cert_number], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
