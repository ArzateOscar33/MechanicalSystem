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
        // Número DEFINITIVO: incrementa secuencia en BD de forma segura
        $cert_number = $this->model->generarCertNumberGlobal();
            $vin = $_POST['vin'];
            $year = $_POST['year'];
            $make = $_POST['marca'];
            $model = $_POST['modelo'];
            $mfg_in = $_POST['fabricado_en'];
            $license_plate = $_POST['placa'];
            $owner_name = $_POST['propietario'];
            $odometer = $_POST['odometro'];
            $inspector_name = $_POST['inspector'];
            $test_date = $_POST['fecha']; 
            $expires = date('Y-m-d', strtotime('+3 months', strtotime($test_date)));
            $source_file = 'manual';
            $phone = $_POST['telefono'] ?? null;
            // VALIDAR si tiene un certificado vigente
            $certExistente = $this->model->vinConCertificadoActivo($vin, $test_date);

            if ($certExistente) {
                $this->responderJSON(
                    "Ya existe un certificado vigente para este VIN. Solo puedes generar uno nuevo cuando el actual haya expirado.",
                    "warning"
                );
                return;
            }
            $monitoreos = [
                'Fallo Encendido' => $_POST['monitor_fallo_encendido'],
                'Sistema Combustible' => $_POST['monitor_sistema_combustible'],
                'Catalizador Integral' => $_POST['monitor_integral_catalizador'],
                'Catalizador' => $_POST['monitor_catalizador'],
                'Sensor C2' => $_POST['monitor_sensor_c2'],
                'Resultado General' => $_POST['resultado_prueba']
            ];

            $latitud = $_POST['latitud'] ?? null;
            $longitud = $_POST['longitud'] ?? null;

            if (!empty($_POST['direccion_existente'])) {
                $address_id = $_POST['direccion_existente'];
            } else {
                $number = $_POST['numero'];
                $street = $_POST['calle'];
                $city = $_POST['ciudad'];
                $state = $_POST['estado'];
                $zip = $_POST['zip'];

                $direccion = $this->model->consultarDireccionConCoordenadas($number, $street, $city, $state, $zip, $latitud, $longitud);
                if ($direccion) {
                    $address_id = $direccion['id'];
                } else {
                    $address_id = $this->model->insertarDireccionConCoordenadas($number, $street, $city, $state, $zip, $latitud, $longitud);
                }
            }


            // Verificar si existe el inspector o crear uno nuevo
            $inspector_input = $_POST['inspector'];

            if (!is_numeric($inspector_input)) {
                $this->responderJSON("Inspector no válido: debe seleccionar uno existente", "error");
                return;
            }

            $inspector_id = intval($inspector_input);


            // Insertar certificado con inspector_id
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
                $inspector_id, // Usar inspector_id en lugar de inspector_name
                $id_usuario,
                $test_date,
                $expires,
                $source_file
            );

            if ($insert === false || $insert === null) {
                $this->responderJSON("Error al crear el certificado", "error");
                return;
            }

            // Monitoreos
            foreach ($monitoreos as $tipo => $resultado) {
                $this->model->insertarMonitoreo($cert_number, $tipo, $resultado);
            }

            // Log importación
            try {
                $this->model->registrarImportacion('cert_manual_' . date('YmdHis'), 'success', '', $id_usuario);
            } catch (Exception $e) {
                $this->model->registrarImportacionAlternativa('cert_manual_' . date('YmdHis'), 'success', '');
            }

            // PDF + ZIP
            $pdf_path = 'uploads/temp/' . $cert_number . '.pdf';
            $zip_path = 'uploads/certificates/' . $cert_number . '.zip';

            // Generar los certificados - Pasamos la información del inspector
            $this->generarCertificadoPDF($cert_number, $pdf_path, $_POST, $address_id, $inspector_id);

            // Generar el archivo zip 
            $this->generarArchivoZIP($cert_number, $pdf_path, $_FILES['imagenes']);

            // Limpieza de temporales
            $this->limpiarTemporales($cert_number);

            // Respuesta final
            echo json_encode([
                'msg' => 'Certificado creado exitosamente',
                'icono' => 'success',
                'pdf_url' => BASE_URL . 'uploads/temp/' . $cert_number . '.pdf',
                'zip_url' => BASE_URL . 'uploads/certificates/' . $cert_number . '.zip'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            $this->responderJSON("Solicitud inválida", "error");
        }
    }


    private function generarCertificadoPDF($cert_number, $pdf_path, $data, $address_id)
    {
        // Obtener el nombre del inspector
        $inspector_id = is_numeric($data['inspector'])
            ? $data['inspector']
            : $this->model->insertarInspector($data['inspector']);
        $inspector = $this->model->obtenerInspectorPorId($inspector_id);
        $inspector_name = $inspector ? $inspector['name'] : $data['inspector'];
        $firmaRelPath = $inspector['direccion_firma'] ?? '';
        $firmaFullPath = $_SERVER['DOCUMENT_ROOT'] . '/MechanicalSystem/' . $firmaRelPath;
        $firmaWebPath = BASE_URL . $firmaRelPath;

        $firma_path = (file_exists($firmaFullPath)) ? $firmaWebPath : '';


        // Rutas de logo y QR para web
        $logoPath = BASE_URL . 'assets/images/logo.png';
        $qrPathRel = 'uploads/temp/' . $cert_number . '_qr.png';
        $qrWebPath = BASE_URL . $qrPathRel;
        $qrFileFullPath = $_SERVER['DOCUMENT_ROOT'] . '/MechanicalSystem/' . $qrPathRel;

        // Generar QR
        $contenidoQR = "{$data['vin']}|{$data['propietario']}|{$data['fabricado_en']}|{$data['year']}|{$data['modelo']}|{$data['marca']}";
        $optionsQR = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
        ]);
        (new QRCode($optionsQR))->render($contenidoQR, $qrFileFullPath);

        // Dirección
        $direccion = $this->model->obtenerDireccionPorId($address_id);
        $direccion_texto = "{$direccion['number']} {$direccion['street']}, {$direccion['city']}, {$direccion['state']} {$direccion['zip']}";
        $barcodeGenerator = new BarcodeGeneratorPNG();

        file_put_contents("uploads/temp/{$cert_number}_vin_barcode.png", $barcodeGenerator->getBarcode($data['vin'], $barcodeGenerator::TYPE_CODE_128));
        file_put_contents("uploads/temp/{$cert_number}_cert_barcode.png", $barcodeGenerator->getBarcode($cert_number, $barcodeGenerator::TYPE_CODE_128));

        // Rutas web para mostrar en el PDF
        $vinBarcodeWeb = BASE_URL . 'uploads/temp/' . $cert_number . '_vin_barcode.png';
        $certBarcodeWeb = BASE_URL . 'uploads/temp/' . $cert_number . '_cert_barcode.png';
        // Incluir imágenes subidas al PDF (copiarlas temporalmente en uploads/temp/)
        $imagenesHTML = '';
        if (isset($_FILES['imagenes']['tmp_name']) && is_array($_FILES['imagenes']['tmp_name'])) {
            $total = min(9, count($_FILES['imagenes']['tmp_name']));
            for ($i = 0; $i < $total; $i++) {
                $nombreArchivo = basename($_FILES['imagenes']['name'][$i]);
                $rutaTemp = $_FILES['imagenes']['tmp_name'][$i];
                $rutaDestino = 'uploads/temp/' . $cert_number . '_img_' . $i . '_' . $nombreArchivo;
                $rutaWeb = BASE_URL . $rutaDestino;
                $rutaFisica = $_SERVER['DOCUMENT_ROOT'] . '/MechanicalSystem/' . $rutaDestino;

                if (is_uploaded_file($rutaTemp)) {
                    move_uploaded_file($rutaTemp, $rutaFisica);
                    $imagenesHTML .= '<img src="' . $rutaWeb . '" width="200" height="200" style="margin:5px;">';
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
                    <tr><td>EBITN</td><td>' . $data['ebitn'] . '</td></tr>
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
        $zip_path = "uploads/certificates/{$cert_number}.zip";
        $zip = new ZipArchive();

        if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

            // Agregar PDF
            if (file_exists($pdf_path)) {
                $zip->addFile($pdf_path, basename($pdf_path));
            }

            // Agregar imágenes desde uploads/temp
            $total = min(9, count($imagenes['name']));
            for ($i = 0; $i < $total; $i++) {
                $nombreOriginal = basename($imagenes['name'][$i]);
                $rutaTemp = 'uploads/temp/' . $cert_number . '_img_' . $i . '_' . $nombreOriginal;

                if (file_exists($rutaTemp)) {
                    $zip->addFile($rutaTemp, 'imagenes/' . $nombreOriginal);
                }
            }

            $zip->close();
        }
    }


    private function limpiarTemporales($cert_number)
    {
        $archivos = glob("uploads/temp/{$cert_number}_*");
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
