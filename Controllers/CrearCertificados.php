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

        $id_usuario = $_SESSION['id_usuario'] ?? 0;
        $consecutivo = $this->model->contarCertificadosPorUsuario($id_usuario) + 1;
        $cert_number = 'MEX' . $id_usuario . '-' . str_pad($consecutivo, 8, "0", STR_PAD_LEFT);

        $data['title'] = 'Crear Certificado';
        $data['cert_number'] = $cert_number;
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
            $consecutivo = $this->model->contarCertificadosPorUsuario($id_usuario) + 1;
            $cert_number = 'MEX' . $id_usuario . '-' . str_pad($consecutivo, 8, "0", STR_PAD_LEFT);

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
            $expires = $_POST['fecha_expiracion'];
            $source_file = 'manual';
            $phone = $_POST['telefono'] ?? null;

            $monitoreos = [
                'Fallo Encendido' => $_POST['monitor_fallo_encendido'],
                'Sistema Combustible' => $_POST['monitor_sistema_combustible'],
                'Catalizador Integral' => $_POST['monitor_integral_catalizador'],
                'Catalizador' => $_POST['monitor_catalizador'],
                'Sensor C2' => $_POST['monitor_sensor_c2'],
                'Resultado General' => $_POST['resultado_prueba']
            ];

            // Dirección
            if (!empty($_POST['direccion_existente'])) {
                $address_id = $_POST['direccion_existente'];
            } else {
                $number = $_POST['numero'];
                $street = $_POST['calle'];
                $city = $_POST['ciudad'];
                $state = $_POST['estado'];
                $zip = $_POST['zip'];

                $direccion = $this->model->consultarDireccion($number, $street, $city, $state, $zip);
                $address_id = $direccion ? $direccion['id'] : $this->model->insertarDireccion($number, $street, $city, $state, $zip);
            }

            // Verificar si existe el inspector o crear uno nuevo
            $inspector_id = $this->model->insertarInspector($inspector_name);

            if (!$inspector_id) {
                $this->responderJSON("Error al registrar el inspector", "error");
                return;
            }

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
        $inspector_id = $this->model->insertarInspector($data['inspector']);
        $inspector = $this->model->obtenerInspectorPorId($inspector_id);
        $inspector_name = $inspector ? $inspector['name'] : $data['inspector'];

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
                    $imagenesHTML .= '<img src="' . $rutaWeb . '" width="150" style="margin:5px;">';
                }
            }
        }

        // HTML del PDF
        $html = '
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                    }

                    .header-barcodes {
                        width: 100%;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 10px;
                    }

                    .barcode-block {
                        text-align: center;
                        flex: 1;
                    }

                    .barcode-block img {
                        max-width: 100%;
                        height: auto;
                        max-height: 50px;
                    }

                    .barcode-label {
                        font-weight: bold;
                        margin-bottom: 5px;
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

                <div style="display: flex; justify-content: center; align-items: flex-start; gap: 40px; margin-bottom: 10px;">
                    <div style="text-align: center; display: inline-block;">
                        <div style="font-weight: bold; margin-bottom: 4px;">VIN</div>
                        <img src="' . $vinBarcodeWeb . '" alt="VIN Barcode" style="max-height: 50px;">
                    </div>
                    <div style="text-align: center; display: inline-block;">
                        <div style="font-weight: bold; margin-bottom: 4px;">Cert Number</div>
                        <img src="' . $certBarcodeWeb . '" alt="Cert Barcode" style="max-height: 50px;">
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
                    <tr><td>Firma del Inspector</td><td>' . $data['firma_inspector'] . '</td></tr>
                    <tr><td>EBITN</td><td>' . $data['ebitn'] . '</td></tr>
                    <tr><td>Fecha</td><td>' . $data['fecha'] . '</td></tr>
                    <tr><td>Fecha Expiración</td><td>' . $data['fecha_expiracion'] . '</td></tr>
                </table>
            </div>
        
            <div class="section"><b>Ubicación Geográfica</b><br>' . $data['latitud'] . ', ' . $data['longitud'] . '</div>
        
            <div class="section"><b>Código QR del Certificado</b><br>
                <img src="' . $qrWebPath . '" width="150">
            </div>
        
            <div class="section"><b>Imágenes del vehículo</b><br>' . $imagenesHTML . '</div>
        
            <div class="section" style="text-align:center">
                <small>Powered by -- Formula 1 Auto Repair -- (BAR NBR RC 00305923)<br>
                To verify this certificate, go to www.mecemissions.com</small>
            </div>
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

    private function responderJSON($mensaje, $icono)
    {
        echo json_encode(['msg' => $mensaje, 'icono' => $icono], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
