<?php

use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Picqer\Barcode\BarcodeGeneratorPNG;

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
        //$this->validarSesionUnica();

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
        // Limpiar cualquier prevalidación anterior al abrir el formulario
        unset($_SESSION['preval_id'], $_SESSION['preval_cert_number']);

        // Número PRELIMINAR: solo lectura, NO incrementa en BD
        $cert_number = $this->model->obtenerCertNumberPreliminar();

        // Guardarlo en sesión para usarlo en prevalidarDatos
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

        $id_usuario = $_SESSION['id_usuario'] ?? 0;

        // Recuperar cert_number preliminar de sesión
        $cert_number = $_SESSION['preval_cert_number'] ?? null;
        if (!$cert_number) {
            $this->responderJSON('Sesión expirada. Recarga el formulario.', 'error');
            return;
        }

        // Recoger datos del POST
        $vin           = trim($_POST['vin'] ?? '');
        $license_plate = trim($_POST['placa'] ?? '');
        $odometer      = $_POST['odometro'] ?? 0;
        $test_date     = $_POST['fecha'] ?? '';
        //$dmv_number    = trim($_POST['ebitn'] ?? '');
        $latitude      = $_POST['latitud'] ?? '';
        $longitude     = $_POST['longitud'] ?? '';

        // Monitoreos — Secomext usa 'P' para aprobado
        $toP = function ($val): string {
            $v = strtoupper(trim((string)$val));
            return ($v === 'PASA' || $v === 'PASS' || $v === 'P') ? 'P' : 'F';
        };

        $misfire        = $toP($_POST['monitor_fallo_encendido']      ?? '');
        $fuelSystem     = $toP($_POST['monitor_sistema_combustible']  ?? '');
        $compCatalyst   = $toP($_POST['monitor_integral_catalizador'] ?? '');
        $catalyst       = $toP($_POST['monitor_catalizador']          ?? '');
        $oxygenSensor   = $toP($_POST['monitor_sensor_c2']            ?? '');
        $overallResult  = $toP($_POST['resultado_prueba']             ?? '');

        // Validaciones básicas
        if (!$vin || strlen($vin) !== 17) {
            $this->responderJSON('VIN inválido.', 'warning');
            return;
        }

        if (!$test_date) {
            $this->responderJSON('La fecha de prueba es obligatoria.', 'warning');
            return;
        }

        // Verificar si ya tiene un certificado vigente
        $certExistente = $this->model->vinConCertificadoActivo($vin, $test_date);
        if ($certExistente) {
            $this->responderJSON(
                'Ya existe un certificado vigente para este VIN.',
                'warning'
            );
            return;
        }

        // Crear intento en BD
        $preval_id = $this->prevalModel->crearIntento($vin, $license_plate, $id_usuario);
        if (!$preval_id) {
            $this->responderJSON('Error al registrar el intento. Intenta de nuevo.', 'error');
            return;
        }

        // Guardar en sesión
        $_SESSION['preval_id'] = $preval_id;

        // Llamar a Secomext
        $datos = [
            'vin'                             => $vin,
            'cert_number'                     => $cert_number,
            'dmv_number'                      => $cert_number,
            'misfire_monitoring'              => $misfire,
            'fuel_system_monitoring'          => $fuelSystem,
            'comprehensive_catalyst_monitoring' => $compCatalyst,
            'catalyst_monitoring'             => $catalyst,
            'oxygen_sensor_monitoring'        => $oxygenSensor,
            'overall_test_result'             => $overallResult,
            'test_date'                       => $test_date,
            'odometer'                        => $odometer,
            'license_plate'                   => $license_plate,
            'latitude'                        => $latitude,
            'longitude'                       => $longitude,
        ];

        $respuesta = $this->secomext->enviarDatos($datos);

        // Actualizar BD con resultado
        $this->prevalModel->actualizarPasoDatos(
            $preval_id,
            $respuesta['success'] ? 'success' : 'failed',
            $respuesta['respuesta'],
            $respuesta['success'] ? $cert_number : ''
        );

        if (!$respuesta['success']) {
            // Limpiar sesión para que pueda reintentar
            unset($_SESSION['preval_id']);
            $this->responderJSON(
                'Secomext rechazó los datos: ' . $respuesta['respuesta'],
                'error'
            );
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

        // Verificar que el paso 1 fue completado
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

        $vin = trim($_POST['vin'] ?? '');

        // Validar que llegaron exactamente 8 fotos
        $archivos = $_FILES['imagenes'] ?? null;
        if (!$archivos || !is_array($archivos['tmp_name']) || count($archivos['tmp_name']) !== 8) {
            $this->responderJSON('Se requieren exactamente 8 fotos.', 'warning');
            return;
        }

        // Mapa de índice → nombre de campo Secomext
        $fotoMap = [
            0 => 'front',    // fotoVin
            1 => 'back',     // fotoFrente
            2 => 'left',     // fotoAtras
            3 => 'right',    // fotoPiloto
            4 => 'vindash',  // fotoPasajero
            5 => 'label',    // fotoPuerta
            6 => 'device',   // fotoScanner
            7 => 'device2',  // fotoTaller
        ];

        // Guardar fotos en temp/ y construir array de rutas
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

            $destRel  = 'uploads/temp/' . $cert_number . '_foto_' . $i . '_' . $origName;
            $destFull = BASE_PATH . $destRel;

            move_uploaded_file($tmpName, $destFull);

            $campo = $fotoMap[$i];
            $fotosRutas[$campo] = $destFull;
        }

        // Guardar rutas en sesión para usarlas en crear()
        $_SESSION['preval_fotos'] = $fotosRutas;

        // Llamar a Secomext
        $respuesta = $this->secomext->enviarFotos($cert_number, $vin, $fotosRutas);

        // Actualizar BD con resultado
        $this->prevalModel->actualizarPasoFotos(
            $preval_id,
            $respuesta['success'] ? 'success' : 'failed',
            $respuesta['respuesta']
        );

        if (!$respuesta['success']) {
            // Limpiar fotos temporales si falló
            $this->limpiarTemporales($cert_number);
            unset($_SESSION['preval_fotos']);
            $this->responderJSON(
                'Secomext rechazó las fotos: ' . $respuesta['respuesta'],
                'error'
            );
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

        // Verificar que ambos pasos fueron completados
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

        $id_usuario = $_SESSION['id_usuario'] ?? 0;

        // Datos del POST
        $vin            = $_POST['vin'];
        $year           = $_POST['year'];
        $make           = $_POST['marca'];
        $model          = $_POST['modelo'];
        $mfg_in         = $_POST['fabricado_en'];
        $license_plate  = $_POST['placa'];
        $owner_name     = $_POST['propietario'];
        $odometer       = $_POST['odometro'];
        $test_date      = $_POST['fecha'];
        $expires        = date('Y-m-d', strtotime('+3 months', strtotime($test_date)));
        $source_file    = 'manual';
        $phone          = $_POST['telefono'] ?? null;

        // Monitoreos
        $monitoreos = [
            'Fallo Encendido'      => $_POST['monitor_fallo_encendido'],
            'Sistema Combustible'  => $_POST['monitor_sistema_combustible'],
            'Catalizador Integral' => $_POST['monitor_integral_catalizador'],
            'Catalizador'          => $_POST['monitor_catalizador'],
            'Sensor C2'            => $_POST['monitor_sensor_c2'],
            'Resultado General'    => $_POST['resultado_prueba']
        ];

        $latitud  = $_POST['latitud']  ?? null;
        $longitud = $_POST['longitud'] ?? null;

        // Resolver dirección
        if (!empty($_POST['direccion_existente'])) {
            $address_id = $_POST['direccion_existente'];
        } else {
            $number = $_POST['numero'];
            $street = $_POST['calle'];
            $city   = $_POST['ciudad'];
            $state  = $_POST['estado'];
            $zip    = $_POST['zip'];

            $direccion = $this->model->consultarDireccionConCoordenadas(
                $number,
                $street,
                $city,
                $state,
                $zip,
                $latitud,
                $longitud
            );

            $address_id = $direccion
                ? $direccion['id']
                : $this->model->insertarDireccionConCoordenadas(
                    $number,
                    $street,
                    $city,
                    $state,
                    $zip,
                    $latitud,
                    $longitud
                );
        }

        // Validar inspector
        if (!is_numeric($_POST['inspector'])) {
            $this->responderJSON('Inspector no válido.', 'error');
            return;
        }
        $inspector_id = intval($_POST['inspector']);

        $cert_number = $_SESSION['preval_cert_number'];
        if (!$cert_number) {
            $this->responderJSON('Número de certificado no encontrado en sesión.', 'error');
            return;
        }
        $this->model->generarCertNumberGlobal(); // incrementa la secuencia
        $fotosRutasRenombradas = [];
        foreach ($fotosRutas as $campo => $rutaAntigua) {
            if (file_exists($rutaAntigua)) {
                $nombreNuevo = preg_replace('/MEX-\d+/', $cert_number, basename($rutaAntigua));
                $rutaNueva   = BASE_PATH . 'uploads/temp/' . $nombreNuevo;
                rename($rutaAntigua, $rutaNueva);
                $fotosRutasRenombradas[$campo] = $rutaNueva;
                error_log("📸 Foto renombrada: " . basename($rutaAntigua) . " → " . $nombreNuevo);
            } else {
                error_log("❌ Foto NO encontrada: $rutaAntigua");
            }
        }
        $fotosRutas = $fotosRutasRenombradas;
        error_log("✅ Total fotos renombradas: " . count($fotosRutas));

        // Insertar certificado
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
            $inspector_id,
            $id_usuario,
            $test_date,
            $expires,
            $source_file
        );

        if ($insert === false || $insert === null) {
            error_log("❌ FALLO en insertarCertificado para cert: $cert_number");
            // ✅ CAMBIO 2 — limpiar sesión para que el siguiente intento use un número nuevo
            unset($_SESSION['preval_cert_number'], $_SESSION['preval_id'], $_SESSION['preval_fotos']);
            $this->responderJSON('Error al crear el certificado.', 'error');
            return;
        }

        // Insertar monitoreos
        foreach ($monitoreos as $tipo => $resultado) {
            $this->model->insertarMonitoreo($cert_number, $tipo, $resultado);
        }

        // Log importación
        try {
            $this->model->registrarImportacion(
                'cert_manual_' . date('YmdHis'),
                'success',
                '',
                $id_usuario
            );
        } catch (Exception $e) {
            $this->model->registrarImportacionAlternativa(
                'cert_manual_' . date('YmdHis'),
                'success',
                ''
            );
        }

        // Rutas PDF y ZIP
        $pdfRelPath  = 'uploads/temp/' . $cert_number . '.pdf';
        $zipRelPath  = 'uploads/certificates/' . $cert_number . '.zip';
        $pdfFullPath = BASE_PATH . $pdfRelPath;

        error_log("✅ PASO 4 - PDF path: $pdfFullPath");
        error_log("✅ PASO 4 - Directorio temp existe: " . (is_dir(BASE_PATH . 'uploads/temp/') ? 'SI' : 'NO'));
        error_log("✅ PASO 4 - Directorio temp writable: " . (is_writable(BASE_PATH . 'uploads/temp/') ? 'SI' : 'NO'));


        // Generar PDF
        try {
            error_log("✅ PASO 5 - Iniciando generarCertificadoPDF...");
            $tempImgs = $this->generarCertificadoPDF($cert_number, $pdfFullPath, $_POST, $address_id, $fotosRutas);
            error_log("✅ PASO 5 - PDF generado OK. PDF existe: " . (file_exists($pdfFullPath) ? 'SI' : 'NO'));
            error_log("✅ PASO 5 - tempImgs: " . print_r($tempImgs, true));
        } catch (\Throwable $e) {
            error_log("❌ FALLO en generarCertificadoPDF: " . $e->getMessage() . " linea " . $e->getLine());
            $this->responderJSON('Error generando PDF: ' . $e->getMessage(), 'error');
            return;
        }

        // Generar ZIP
        try {
            error_log("✅ PASO 6 - Iniciando generarArchivoZIP...");
            $this->generarArchivoZIP($cert_number, $pdfFullPath, $fotosRutas);
            error_log("✅ PASO 6 - ZIP generado OK. ZIP existe: " . (file_exists(BASE_PATH . $zipRelPath) ? 'SI' : 'NO'));
        } catch (\Throwable $e) {
            error_log("❌ FALLO en generarArchivoZIP: " . $e->getMessage() . " linea " . $e->getLine());
            $this->responderJSON('Error generando ZIP: ' . $e->getMessage(), 'error');
            return;
        }

        // Enviar a SmogsBackups
        try {
            error_log("✅ PASO 7 - Enviando a SmogsBackups...");
            $apiResp = $this->enviarCertificadoSmogsBackups($cert_number, $_POST, $address_id, $pdfFullPath, $tempImgs);
            error_log("✅ PASO 7 - SmogsBackups respuesta: " . print_r($apiResp, true));
        } catch (\Throwable $e) {
            error_log("❌ FALLO en SmogsBackups: " . $e->getMessage());
            // No detener el flujo, SmogsBackups no es crítico
            $apiResp = ['ok' => false, 'status' => 0, 'api_msg' => $e->getMessage()];
        }

        error_log("✅ PASO 8 - Certificado creado exitosamente: $cert_number");

        // Vincular prevalidación con certificado creado
        $this->prevalModel->vincularCertificado($preval_id, $cert_number);

        // Limpiar sesión y temporales
        unset($_SESSION['preval_id'], $_SESSION['preval_cert_number'], $_SESSION['preval_fotos']);
        $this->limpiarTemporales($cert_number);

        echo json_encode([
            'msg'        => 'Certificado creado exitosamente',
            'icono'      => 'success',
            'pdf_url'    => BASE_URL . $pdfRelPath,
            'zip_url'    => BASE_URL . $zipRelPath,
            'api_ok'     => $apiResp['ok']     ?? false,
            'api_status' => $apiResp['status'] ?? 0,
            'api_msg'    => $apiResp['api_msg'] ?? ''
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =========================================================
    // GENERAR PDF — ahora recibe $fotosRutas en lugar de $_FILES
    // =========================================================
    private function generarCertificadoPDF(
        $cert_number,
        $pdf_path,
        $data,
        $address_id,
        array $fotosRutas = []
    ): array {
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

        // QR
        $qrPathRel      = 'uploads/temp/' . $cert_number . '_qr.png';
        $qrWebPath      = BASE_URL  . $qrPathRel;
        $qrFileFullPath = BASE_PATH . $qrPathRel;

        $contenidoQR = "{$data['vin']}|{$data['propietario']}|{$data['fabricado_en']}|{$data['year']}|{$data['modelo']}|{$data['marca']}";
        $optionsQR   = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_L,
        ]);
        (new QRCode($optionsQR))->render($contenidoQR, $qrFileFullPath);

        // Dirección
        $direccion       = $this->model->obtenerDireccionPorId($address_id);
        $direccion_texto = "{$direccion['number']} {$direccion['street']}, {$direccion['city']}, {$direccion['state']} {$direccion['zip']}";

        // Barcodes
        $barcodeGenerator = new BarcodeGeneratorPNG();
        file_put_contents(
            BASE_PATH . "uploads/temp/{$cert_number}_vin_barcode.png",
            $barcodeGenerator->getBarcode($data['vin'], $barcodeGenerator::TYPE_CODE_128)
        );
        file_put_contents(
            BASE_PATH . "uploads/temp/{$cert_number}_cert_barcode.png",
            $barcodeGenerator->getBarcode($cert_number, $barcodeGenerator::TYPE_CODE_128)
        );

        $vinBarcodeWeb  = BASE_URL . 'uploads/temp/' . $cert_number . '_vin_barcode.png';
        $certBarcodeWeb = BASE_URL . 'uploads/temp/' . $cert_number . '_cert_barcode.png';

        // Construir HTML de imágenes y tempImgs desde $fotosRutas
        $imagenesHTML = '';
        $tempImgs     = [];
        $i            = 0;

        foreach ($fotosRutas as $campo => $rutaFisica) {
            if (file_exists($rutaFisica)) {
                $rutaWeb       = BASE_URL . 'uploads/temp/' . basename($rutaFisica);
                $imagenesHTML .= '<img src="' . $rutaWeb . '" width="200" height="200" style="margin:5px;">';
                $tempImgs[]    = [
                    'index' => $i,
                    'name'  => basename($rutaFisica),
                    'path'  => $rutaFisica,
                ];
            }
            $i++;
        }

        // HTML del PDF (igual que el tuyo actual)
        $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; }
            .header-barcodes { display: table; width: 100%; table-layout: fixed; margin-bottom: 10px; }
            .barcode-block { display: table-cell; text-align: center; vertical-align: top; padding: 5px; }
            .barcode-label { font-weight: bold; font-size: 12px; margin-bottom: 5px; }
            .barcode-block img { width: 315px; height: 40px; object-fit: contain; display: block; margin: 0 auto; }
            .title { font-size: 20px; font-weight: bold; text-align: center; margin-top: 10px; margin-bottom: 10px; }
            .logo { text-align: center; margin-bottom: 15px; }
            .section { margin-bottom: 20px; }
            .label { font-weight: bold; color: #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 8px; }
            table, th, td { border: 1px solid #999; }
            th { background-color: #f0f0f0; padding: 6px; text-align: left; }
            td { padding: 6px; }
        </style>

        <div class="header-barcodes">
            <div class="barcode-block">
                <div class="barcode-label">VIN</div>
                <img src="' . $vinBarcodeWeb . '" alt="VIN Barcode">
            </div>
            <div class="barcode-block">
                <div class="barcode-label">Cert Number</div>
                <img src="' . $certBarcodeWeb . '" alt="Cert Barcode">
            </div>
        </div>

        <div class="title">MECHANICAL EMISSIONS SERVICES LLC</div>
        <div style="text-align:center"><img src="' . $logoPath . '" height="80"></div>
        <div class="section"><b>Dirección:</b> ' . $direccion_texto . '</div>
        <div class="section"><b>Cert Number:</b> ' . $cert_number . '</div>

        <div class="section"><b>Vehicle Information</b>
            <table>
                <tr><th>Campo</th><th>Valor</th></tr>
                <tr><td>VIN</td><td>' . $data['vin'] . '</td></tr>
                <tr><td>Año</td><td>' . $data['year'] . '</td></tr>
                <tr><td>Fabricado en</td><td>' . $data['fabricado_en'] . '</td></tr>
                <tr><td>Marca</td><td>' . $data['marca'] . '</td></tr>
                <tr><td>Modelo</td><td>' . $data['modelo'] . '</td></tr>
                <tr><td>Placa</td><td>' . $data['placa'] . '</td></tr>
                <tr><td>Odómetro</td><td>' . $data['odometro'] . '</td></tr>
                <tr><td>Propietario</td><td>' . $data['propietario'] . '</td></tr>
            </table>
        </div>

        <div class="section"><b>Monitoreo & Certificación</b>
            <table>
                <tr><th>Tipo</th><th>Resultado</th></tr>
                <tr><td>Fallo de Encendido</td><td>' . $data['monitor_fallo_encendido'] . '</td></tr>
                <tr><td>Sistema de Combustible</td><td>' . $data['monitor_sistema_combustible'] . '</td></tr>
                <tr><td>Catalizador Integral</td><td>' . $data['monitor_integral_catalizador'] . '</td></tr>
                <tr><td>Catalizador</td><td>' . $data['monitor_catalizador'] . '</td></tr>
                <tr><td>Sensor C2</td><td>' . $data['monitor_sensor_c2'] . '</td></tr>
                <tr><td>Resultado General</td><td>' . $data['resultado_prueba'] . '</td></tr>
            </table>
        </div>

        <div class="section"><b>Información del Inspector</b>
            <table>
                <tr><th>Campo</th><th>Valor</th></tr>
                <tr><td>Inspector</td><td>' . $inspector_name . '</td></tr>
                <tr>
                    <td>Firma del Inspector</td>
                    <td style="text-align:center;">
                        ' . ($firma_path
            ? '<img src="' . $firma_path . '" style="height:40px; max-width:100px;">'
            : 'Sin firma') . '
                    </td>
                </tr>
                <tr><td>EEI ITN</td><td>' . $data['ebitn'] . '</td></tr>
                <tr><td>Fecha</td><td>' . $data['fecha'] . '</td></tr>
                <tr><td>Fecha Expiración</td><td>' . $data['fecha_expiracion'] . '</td></tr>
            </table>
        </div>

        <div class="section"><b>Ubicación Geográfica</b><br>' . $data['latitud'] . ', ' . $data['longitud'] . '</div>

        <div class="section"><b>Código QR del Certificado</b><br>
            <img src="' . $qrWebPath . '" width="150">
        </div>

        <div class="section"><b>Imágenes del vehículo</b><br><br>' . $imagenesHTML . '</div>
        ';

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf  = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        file_put_contents($pdf_path, $dompdf->output());

        return $tempImgs;
    }

    // =========================================================
    // GENERAR ZIP — ahora recibe $fotosRutas en lugar de $_FILES
    // =========================================================
    private function generarArchivoZIP($cert_number, $pdf_path, array $fotosRutas)
    {
        $zipRelPath = "uploads/certificates/{$cert_number}.zip";
        $zip_path   = BASE_PATH . $zipRelPath;

        $dirZip = dirname($zip_path);
        if (!is_dir($dirZip)) {
            mkdir($dirZip, 0775, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            if (file_exists($pdf_path)) {
                $zip->addFile($pdf_path, basename($pdf_path));
            }

            foreach ($fotosRutas as $campo => $rutaFisica) {
                if (file_exists($rutaFisica)) {
                    $zip->addFile($rutaFisica, 'imagenes/' . basename($rutaFisica));
                }
            }

            $zip->close();
        }
    }

    // =========================================================
    // ENVIAR A SMOGS BACKUPS — sin cambios respecto al original
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

        $fmtFecha = function ($yyyyMmDd): string {
            $s = trim((string)$yyyyMmDd);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) {
                [$y, $m, $d] = explode('-', $s);
                return $d . '-' . $m . '-' . $y;
            }
            return date('d-m-Y');
        };

        $api       = api_client('smogs_backups');
        $cfg       = api_config('smogs_backups');
        $isTesting = (!empty($cfg['mode']) && $cfg['mode'] === 'testing');
        $path      = $isTesting ? '/post' : '/Mechanical/Emissions';

        if (!$isTesting) {
            if (!file_exists($pdfFullPath)) {
                return ['ok' => false, 'status' => 0, 'api_msg' => 'PDF no encontrado'];
            }
            if (count($tempImgs) < 8) {
                return ['ok' => false, 'status' => 0, 'api_msg' => 'Faltan imágenes (se requieren 8)'];
            }
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
        $payload['geolocalizacion'] = trim((string)$lat) . ', ' . trim((string)$lon);

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
            7 => 'fotoTaller',
        ];

        if ($isTesting) {
            $payload['certificadoPdf'] = 'TEST_PDF_BASE64';
            foreach ($fotoMap as $idx => $field) {
                $payload[$field] = 'TEST_IMG_BASE64_' . $idx;
            }
        } else {
            $payload['certificadoPdf'] = base64_encode(file_get_contents($pdfFullPath));
            foreach ($tempImgs as $img) {
                $i = (int)($img['index'] ?? -1);
                if ($i < 0 || !isset($fotoMap[$i])) continue;
                $field = $fotoMap[$i];
                if (!empty($img['path']) && file_exists($img['path'])) {
                    $payload[$field] = base64_encode(file_get_contents($img['path']));
                }
            }
        }

        $resp = $api->postUrlEncoded($path, $payload);

        // Log
        $pdfSha       = (!$isTesting && file_exists($pdfFullPath)) ? hash_file('sha256', $pdfFullPath) : '';
        $photosHashes = [];
        if (!$isTesting) {
            foreach ($tempImgs as $img) {
                $p = $img['path'] ?? '';
                if ($p && file_exists($p)) {
                    $photosHashes[(string)($img['index'] ?? 0)] = hash_file('sha256', $p);
                }
            }
        }

        $payloadStr   = http_build_query($payload, '', '&', PHP_QUERY_RFC3986);
        $payloadSha   = hash('sha256', $payloadStr);
        $photosJson   = json_encode($photosHashes, JSON_UNESCAPED_UNICODE);
        $baseUrl      = api_base_url('smogs_backups');
        $mode         = $cfg['mode'] ?? 'production';
        $endpointUsed = rtrim((string)$baseUrl, '/') . $path;

        $attempt = 1;
        try {
            $attempt = $this->model->siguienteAttemptApiEmissions($cert_number);
        } catch (Exception $e) {
        }

        $apiResult = null;
        $apiDesc   = null;
        if (!empty($resp['json']) && is_array($resp['json'])) {
            $apiResult = $resp['json']['result']      ?? ($resp['json']['Resultado']    ?? null);
            $apiDesc   = $resp['json']['Description'] ?? ($resp['json']['descripcion']  ?? ($resp['json']['message'] ?? null));
        }

        try {
            $this->model->insertarApiEmissionsSyncLog([
                'cert_number'        => $cert_number,
                'vin'                => $payload['vin'] ?? '',
                'mode'               => $mode,
                'endpoint'           => $endpointUsed,
                'payload_sha256'     => $payloadSha,
                'pdf_sha256'         => $pdfSha,
                'photos_sha256_json' => $photosJson ?: '{}',
                'http_status'        => (int)($resp['status'] ?? 0),
                'api_result'         => $apiResult,
                'api_description'    => $apiDesc,
                'response_raw'       => (string)($resp['body'] ?? ''),
                'attempt'            => (int)$attempt,
            ]);
        } catch (Exception $e) {
        }

        $apiMsg = '';
        if (!empty($resp['json'])) {
            $apiMsg = $resp['json']['Description'] ?? ($resp['json']['descripcion'] ?? ($resp['json']['message'] ?? ''));
        }

        return [
            'ok'      => $resp['ok']    ?? false,
            'status'  => $resp['status'] ?? 0,
            'api_msg' => $apiMsg,
            'raw'     => $resp['body']  ?? '',
            'json'    => $resp['json']  ?? null,
        ];
    }

    // =========================================================
    // MÉTODOS AUXILIARES (sin cambios)
    // =========================================================
    public function obtenerFirmaInspector($id)
    {
        $inspector = $this->model->obtenerInspectorPorId($id);
        if ($inspector && !empty($inspector['direccion_firma'])) {
            echo json_encode(['firma' => BASE_URL . $inspector['direccion_firma']], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['firma' => ''], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    public function obtenerLatLon($id)
    {
        $data = $this->model->obtenerDireccionPorId($id);
        echo json_encode([
            'latitude'  => $data['latitude']  ?? '',
            'longitude' => $data['longitude'] ?? ''
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function limpiarTemporales($cert_number)
    {
        $archivos = glob(BASE_PATH . "uploads/temp/{$cert_number}_*");
        foreach ($archivos as $archivo) {
            if (is_file($archivo)) {
                unlink($archivo);
            }
        }
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
