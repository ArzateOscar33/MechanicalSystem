<?php

use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Picqer\Barcode\BarcodeGeneratorPNG;

require_once __DIR__ . '/../vendor/autoload.php';
class CrearCertificados extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->validarSesionInactividad();
        $this->validarSesionUnica();
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
    }

    public function index()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }

        // Número PRELIMINAR: solo lectura, NO incrementa en BD
        $cert_number = $this->model->obtenerCertNumberPreliminar();

        $data['title'] = 'Crear Certificado';
        $data['cert_number'] = $cert_number; // se muestra en el input readonly
        $data['direcciones'] = $this->model->obtenerDirecciones();
        $data['inspectores'] = $this->model->obtenerInspectores();
        $this->views->getView('admin/CrearCertificados', "index", $data);
    }

    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            $id_usuario = $_SESSION['id_usuario'] ?? 0;

            // 🔹 1) Tomamos todos los datos del POST
            $vin            = $_POST['vin'];
            $year           = $_POST['year'];
            $make           = $_POST['marca'];
            $model          = $_POST['modelo'];
            $mfg_in         = $_POST['fabricado_en'];
            $license_plate  = $_POST['placa'];
            $owner_name     = $_POST['propietario'];
            $odometer       = $_POST['odometro'];
            $inspector_name = $_POST['inspector'];
            $test_date      = $_POST['fecha'];
            $expires        = date('Y-m-d', strtotime('+3 months', strtotime($test_date)));
            $source_file    = 'manual';
            $phone          = $_POST['telefono'] ?? null;

            // 🔹 2) Validar si ya tiene un certificado vigente ANTES de tocar la secuencia
            $certExistente = $this->model->vinConCertificadoActivo($vin, $test_date);
            if ($certExistente) {
                $this->responderJSON(
                    "Ya existe un certificado vigente para este VIN. Solo puedes generar uno nuevo cuando el actual haya expirado.",
                    "warning"
                );
                return;
            }

            // Monitoreos
            $monitoreos = [
                'Fallo Encendido'       => $_POST['monitor_fallo_encendido'],
                'Sistema Combustible'   => $_POST['monitor_sistema_combustible'],
                'Catalizador Integral'  => $_POST['monitor_integral_catalizador'],
                'Catalizador'           => $_POST['monitor_catalizador'],
                'Sensor C2'             => $_POST['monitor_sensor_c2'],
                'Resultado General'     => $_POST['resultado_prueba']
            ];

            // Coordenadas
            $latitud  = $_POST['latitud'] ?? null;
            $longitud = $_POST['longitud'] ?? null;

            // 🔹 3) Resolver dirección (existente o nueva) ANTES del consecutivo
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

                if ($direccion) {
                    $address_id = $direccion['id'];
                } else {
                    $address_id = $this->model->insertarDireccionConCoordenadas(
                        $number,
                        $street,
                        $city,
                        $state,
                        $zip,
                        $latitud,
                        $longitud
                    );
                }
            }

            // 🔹 4) Validar inspector ANTES del consecutivo
            $inspector_input = $_POST['inspector'];

            if (!is_numeric($inspector_input)) {
                $this->responderJSON("Inspector no válido: debe seleccionar uno existente", "error");
                return;
            }

            $inspector_id = intval($inspector_input);

            // 🔹 5) AHORA SÍ: generar número DEFINITIVO (solo si todo lo anterior está OK)
            $cert_number = $this->model->generarCertNumberGlobal(); // 👈 AQUÍ LO MOVEMOS

            // 🔹 6) Insertar certificado
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
                $this->responderJSON("Error al crear el certificado", "error");
                return;
            }

            // 🔹 7) Monitoreos
            foreach ($monitoreos as $tipo => $resultado) {
                $this->model->insertarMonitoreo($cert_number, $tipo, $resultado);
            }

            // 🔹 8) Log importación
            try {
                $this->model->registrarImportacion('cert_manual_' . date('YmdHis'), 'success', '', $id_usuario);
            } catch (Exception $e) {
                $this->model->registrarImportacionAlternativa('cert_manual_' . date('YmdHis'), 'success', '');
            }

            // 🔹 9) Generar PDF y ZIP usando el cert_number definitivo
            $pdfRelPath = 'uploads/temp/' . $cert_number . '.pdf';
            $zipRelPath = 'uploads/certificates/' . $cert_number . '.zip';

            $pdfFullPath = BASE_PATH . $pdfRelPath;

            // 9.1) Generar PDF y mover imágenes a temp (AHORA nos regresa rutas físicas en temp)
            $tempImgs = $this->generarCertificadoPDF($cert_number, $pdfFullPath, $_POST, $address_id);

            // 9.2) ZIP (igual que siempre, sigue tomando de uploads/temp)
            $this->generarArchivoZIP($cert_number, $pdfFullPath, $_FILES['imagenes']);

            // 9.3) Enviar a API externa ANTES de limpiar temporales
            $apiResp = $this->enviarCertificadoSmogsBackups($cert_number, $_POST, $address_id, $pdfFullPath, $tempImgs);

            // 9.4) Limpiar temporales al final
            $this->limpiarTemporales($cert_number);

            // 9.5) Respuesta al frontend (agrego info api por transparencia)
            echo json_encode([
                'msg'       => 'Certificado creado exitosamente',
                'icono'     => 'success',
                'pdf_url'   => BASE_URL . $pdfRelPath,
                'zip_url'   => BASE_URL . $zipRelPath,
                'api_ok'    => $apiResp['ok'] ?? false,
                'api_status' => $apiResp['status'] ?? 0,
                'api_msg'   => $apiResp['api_msg'] ?? ''
            ], JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $this->responderJSON("Solicitud inválida", "error");
        }
    }



    private function generarCertificadoPDF($cert_number, $pdf_path, $data, $address_id): array

    {
        // Obtener el nombre del inspector
        $inspector_id = is_numeric($data['inspector'])
            ? $data['inspector']
            : $this->model->insertarInspector($data['inspector']);
        $inspector = $this->model->obtenerInspectorPorId($inspector_id);
        $inspector_name = $inspector ? $inspector['name'] : $data['inspector'];

        $firmaRelPath   = $inspector['direccion_firma'] ?? '';        // ej: "uploads/firmas/firma_123.png"
        $firmaFullPath  = BASE_PATH . $firmaRelPath;                  // físico
        $firmaWebPath   = BASE_URL  . $firmaRelPath;                  // URL
        $firma_path = (file_exists($firmaFullPath)) ? $firmaWebPath : '';


        // Rutas de logo y QR para web
        $logoPath = BASE_URL . 'assets/images/logo.png';

        // QR
        $qrPathRel      = 'uploads/temp/' . $cert_number . '_qr.png'; // relativo
        $qrWebPath      = BASE_URL  . $qrPathRel;                     // URL
        $qrFileFullPath = BASE_PATH . $qrPathRel;                     // físico

        // Generar QR
        $contenidoQR = "{$data['vin']}|{$data['propietario']}|{$data['fabricado_en']}|{$data['year']}|{$data['modelo']}|{$data['marca']}";
        $optionsQR = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => QRCode::ECC_L,
        ]);
        (new QRCode($optionsQR))->render($contenidoQR, $qrFileFullPath);

        // Dirección
        $direccion = $this->model->obtenerDireccionPorId($address_id);
        $direccion_texto = "{$direccion['number']} {$direccion['street']}, {$direccion['city']}, {$direccion['state']} {$direccion['zip']}";
        $barcodeGenerator = new BarcodeGeneratorPNG();
        file_put_contents(
            BASE_PATH . "uploads/temp/{$cert_number}_vin_barcode.png",
            $barcodeGenerator->getBarcode($data['vin'], $barcodeGenerator::TYPE_CODE_128)
        );

        file_put_contents(
            BASE_PATH . "uploads/temp/{$cert_number}_cert_barcode.png",
            $barcodeGenerator->getBarcode($cert_number, $barcodeGenerator::TYPE_CODE_128)
        );

        // Rutas web para el HTML
        $vinBarcodeWeb  = BASE_URL . 'uploads/temp/' . $cert_number . '_vin_barcode.png';
        $certBarcodeWeb = BASE_URL . 'uploads/temp/' . $cert_number . '_cert_barcode.png';

        // Incluir imágenes subidas al PDF (copiarlas temporalmente en uploads/temp/)
        $imagenesHTML = '';
        $tempImgs = [];

        if (isset($_FILES['imagenes']['tmp_name']) && is_array($_FILES['imagenes']['tmp_name'])) {
            $total = count($_FILES['imagenes']['tmp_name']); // ahora deben ser 8
            for ($i = 0; $i < $total; $i++) {
                $nombreArchivo = basename($_FILES['imagenes']['name'][$i]);
                $rutaTemp      = $_FILES['imagenes']['tmp_name'][$i];

                $rutaDestinoRel = 'uploads/temp/' . $cert_number . '_img_' . $i . '_' . $nombreArchivo;
                $rutaWeb        = BASE_URL  . $rutaDestinoRel;
                $rutaFisica     = BASE_PATH . $rutaDestinoRel;

                // Asegura carpeta
                $dir = dirname($rutaFisica);
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }

                if (is_uploaded_file($rutaTemp)) {
                    move_uploaded_file($rutaTemp, $rutaFisica);

                    // 1) Para el PDF (como ya lo hacías)
                    $imagenesHTML .= '<img src="' . $rutaWeb . '" width="200" height="200" style="margin:5px;">';

                    // 2) Para la API (ruta física para base64)
                    $tempImgs[] = [
                        'index' => $i,
                        'name'  => $nombreArchivo,
                        'path'  => $rutaFisica,
                    ];
                }
            }
        }


        // HTML del PDF
        // HTML del PDF
        $html = '
          <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }

                .header-barcodes {
                    display: table;
                    width: 100%;
                    table-layout: fixed;
                    margin-bottom: 10px;
                    
                }

                .barcode-block {
                    display: table-cell;
                    text-align: center;
                    vertical-align: top;
                    padding: 5px;
                }

                .barcode-label {
                    font-weight: bold;
                    font-size: 12px;
                    margin-bottom: 5px;
                }

                .barcode-block img {
                    width: 315px;
                    height: 40px;
                    object-fit: contain;
                    display: block;
                    margin: 0 auto;
                }

                    .title {
                        font-size: 20px;
                        font-weight: bold;
                        text-align: center;
                        margin-top: 10px;
                        margin-bottom: 10px;
                    }

                    .logo {
                        text-align: center;
                        margin-bottom: 15px;
                    }

                    .section {
                        margin-bottom: 20px;
                    }

                    .label {
                        font-weight: bold;
                        color: #333;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 8px;
                    }

                    table, th, td {
                        border: 1px solid #999;
                    }

                    th {
                        background-color: #f0f0f0;
                        padding: 6px;
                        text-align: left;
                    }

                    td {
                        padding: 6px;
                    }
            </style>

                <div class="header-barcodes">
                    <div class="barcode-block">
                        <div class="barcode-label">VIN</div>
                        <div class="barcode-img">
                            <img src="' . $vinBarcodeWeb . '" alt="VIN Barcode">
                        </div>
                    </div>
                    <div class="barcode-block">
                        <div class="barcode-label">Cert Number</div>
                        <div class="barcode-img">
                            <img src="' . $certBarcodeWeb . '" alt="Cert Barcode">
                        </div>
                    </div>
                </div>




                    <!-- Nombre del Centro -->
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
                                        <td>Firma del Inspector</td>
                    <td style="text-align:center;">
                        ' . ($firma_path ? '<img src="' . $firma_path . '" style="height:40px; max-width:100px;">' : 'Sin firma') . '
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
        
            <div class="section"><b>Imágenes del vehículo</b><br><br><br><br><br>' . $imagenesHTML . '</div>
        
 
            ';

        // Generar PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        file_put_contents($pdf_path, $dompdf->output());
        return $tempImgs;
    }

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


    private function generarArchivoZIP($cert_number, $pdf_path, $imagenes)
    {
        // Ruta relativa y física del ZIP
        $zipRelPath = "uploads/certificates/{$cert_number}.zip";
        $zip_path   = BASE_PATH . $zipRelPath;

        // Asegura carpeta
        $dirZip = dirname($zip_path);
        if (!is_dir($dirZip)) {
            mkdir($dirZip, 0775, true);
        }

        $zip = new ZipArchive();

        if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            // Agregar PDF
            if (file_exists($pdf_path)) {
                $zip->addFile($pdf_path, basename($pdf_path));
            }

            // Agregar imágenes desde uploads/temp
            $total = min(8, count($imagenes['name']));
            for ($i = 0; $i < $total; $i++) {
                $nombreOriginal = basename($imagenes['name'][$i]);
                $rutaTempRel    = 'uploads/temp/' . $cert_number . '_img_' . $i . '_' . $nombreOriginal;
                $rutaTempFis    = BASE_PATH . $rutaTempRel;

                if (file_exists($rutaTempFis)) {
                    $zip->addFile($rutaTempFis, 'imagenes/' . $nombreOriginal);
                }
            }

            $zip->close();
        }
    }

    private function enviarCertificadoSmogsBackups(
        string $cert_number,
        array $post,
        int $address_id,
        string $pdfFullPath,
        array $tempImgs
    ): array {

        // 1) Dirección (para lat/lon)
        $direccion = $this->model->obtenerDireccionPorId($address_id);

        // Helpers
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

        // 2) Config / mode
        $api = api_client('smogs_backups');
        $cfg = api_config('smogs_backups');
        $isTesting = (!empty($cfg['mode']) && $cfg['mode'] === 'testing');

        $path = $isTesting ? '/post' : '/Mechanical/Emissions';

        // 3) Validaciones de archivos SOLO si NO es testing
        if (!$isTesting) {
            if (!file_exists($pdfFullPath)) {
                return ['ok' => false, 'status' => 0, 'api_msg' => 'PDF no encontrado para envío API'];
            }
            if (count($tempImgs) < 8) {
                return ['ok' => false, 'status' => 0, 'api_msg' => 'Faltan imágenes para envío API (se requieren 8)'];
            }
        }

        // 4) Payload base (sin adjuntos)
        $payload = [];
        $payload = array_merge($payload, api_credentials('smogs_backups'));

        $payload['vin']          = $post['vin'] ?? '';
        $payload['odometer']     = $post['odometro'] ?? '';
        $payload['licensePlate'] = $post['placa'] ?? '';
        $payload['folio']        = $cert_number;

        $payload['testFecha'] = $fmtFecha($post['fecha'] ?? '');
        $payload['testHora']  = date('H:i');

        $lat = $direccion['latitude']  ?? ($post['latitud']  ?? '');
        $lon = $direccion['longitude'] ?? ($post['longitud'] ?? '');
        $payload['geolocalizacion'] = trim((string)$lat) . ', ' . trim((string)$lon);

        $payload['testignicion']       = $toPassFail($post['monitor_fallo_encendido'] ?? '');
        $payload['testSistGasolina']   = $toPassFail($post['monitor_sistema_combustible'] ?? '');
        $payload['testCatalizador']    = $toPassFail($post['monitor_catalizador'] ?? '');
        $payload['testSensorOxigeno']  = $toPassFail($post['monitor_sensor_c2'] ?? '');
        $payload['testCompIntegrales'] = $toPassFail($post['monitor_integral_catalizador'] ?? '');
        $payload['testResultadoFinal'] = $toPassFail($post['resultado_prueba'] ?? '');

        $payload['foto_Extension'] = $post['foto_Extension'] ?? 'jpg';

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

        // 5) Adjuntos: MOCK en testing, reales en production
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

        // 6) Enviar
        $resp = $api->postUrlEncoded($path, $payload);

        // 7) LOG A BD (AL FINAL) — usando el payload EXACTO enviado
        $pdfSha = '';
        if (!$isTesting && file_exists($pdfFullPath)) {
            $pdfSha = hash_file('sha256', $pdfFullPath);
        }

        $photosHashes = [];
        if (!$isTesting) {
            foreach ($tempImgs as $img) {
                $i = (int)($img['index'] ?? -1);
                if ($i < 0) continue;
                $p = $img['path'] ?? '';
                if ($p && file_exists($p)) {
                    $photosHashes[(string)$i] = hash_file('sha256', $p);
                }
            }
        }
        $photosJson = json_encode($photosHashes, JSON_UNESCAPED_UNICODE);

        $payloadStr = http_build_query($payload, '', '&', PHP_QUERY_RFC3986);
        $payloadSha = hash('sha256', $payloadStr);

        $baseUrl = api_base_url('smogs_backups');
        $mode    = $cfg['mode'] ?? 'production';
        $endpointUsed = rtrim((string)$baseUrl, '/') . $path;

        $attempt = 1;
        try {
            $attempt = $this->model->siguienteAttemptApiEmissions($cert_number);
        } catch (Exception $e) {
        }

        $apiResult = null;
        $apiDesc   = null;
        if (!empty($resp['json']) && is_array($resp['json'])) {
            $apiResult = $resp['json']['result'] ?? ($resp['json']['Resultado'] ?? null);
            $apiDesc   = $resp['json']['Description'] ?? ($resp['json']['descripcion'] ?? ($resp['json']['message'] ?? null));
        }

        try {
            $this->model->insertarApiEmissionsSyncLog([
                'cert_number'        => $cert_number,
                'vin'                => $payload['vin'] ?? ($post['vin'] ?? ''),
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

        // 8) Mensaje UI
        $apiMsg = '';
        if (!empty($resp['json'])) {
            $apiMsg = $resp['json']['Description'] ?? ($resp['json']['descripcion'] ?? ($resp['json']['message'] ?? ''));
        }

        return [
            'ok'      => $resp['ok'] ?? false,
            'status'  => $resp['status'] ?? 0,
            'api_msg' => $apiMsg,
            'raw'     => $resp['body'] ?? '',
            'json'    => $resp['json'] ?? null,
        ];
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
    public function obtenerLatLon($id)
    {
        $data = $this->model->obtenerDireccionPorId($id);
        echo json_encode([
            'latitude' => $data['latitude'] ?? '',
            'longitude' => $data['longitude'] ?? ''
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function responderJSON($mensaje, $icono)
    {
        echo json_encode(['msg' => $mensaje, 'icono' => $icono], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
