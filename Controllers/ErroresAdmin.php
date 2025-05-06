<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ErroresAdmin extends Controller
{

    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
    }
    public function index()
    {
        $data['title'] = 'Manejo de Errores de Usuario';

        $this->views->getView('admin/ErroresAdmin', "index", $data);
    }
    public function listar()
    {
        $data = $this->model->getErrores('pending');
    
        for ($i = 0; $i < count($data); $i++) {
            // Mostrar nombre del inspector si el campo corregido es 'inspector_id'
            if ($data[$i]['field_name'] === 'inspector_id') {
                $data[$i]['current_value'] = $data[$i]['inspector_actual'] ?? '(ID: ' . $data[$i]['current_value'] . ')';
                $data[$i]['proposed_value'] = $data[$i]['inspector_propuesto'] ?? '(ID: ' . $data[$i]['proposed_value'] . ')';
            } elseif ($data[$i]['field_name'] === 'address_id') {
                $data[$i]['current_value'] = $data[$i]['direccion_actual'] ?? '(ID: ' . $data[$i]['current_value'] . ')';
                $data[$i]['proposed_value'] = $data[$i]['direccion_propuesta'] ?? '(ID: ' . $data[$i]['proposed_value'] . ')';
            }
            
    
            $data[$i]['accion'] = '<div class="d-flex">
                <button class="btn btn-primary" type="button" onclick="editCertificate(\'' . $data[$i]['id'] . '\')">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger" type="button" onclick="errorDelete(\'' . $data[$i]['id'] . '\')">
                  <i class="fas fa-trash"></i>
                </button>
            </div>';
        }
    
        echo json_encode($data);
        die();
    }
    
    
    
    public function listarResueltos()
    {
        $data = $this->model->getErroresResueltos('corrected');
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex">
            <button class="btn btn-primary" type="button" onclick="editError(\'' . $data[$i]['certificate_id'] . '\')"><i class="fas fa-edit"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }

    public function listarRechazados()
    {
        $data = $this->model->getErroresResueltos('rejected');
       
        echo json_encode($data);
        die();
    }
    public function editCertificate($error_id)
    {
        if (!is_numeric($error_id)) {
            echo json_encode(['msg' => 'ID inválido', 'icono' => 'error']);
            return;
        }

        $data = $this->model->obtenerCertificadoPorError($error_id);
        echo json_encode($data);
        die();
    }

    //editar user
    public function editError($id_certificate)
    {

        $data = $this->model->getErrores($id_certificate);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        die();
    }

    public function corregirCertificado()
    {
        if (!isset($_POST['id'], $_POST['cert_number'], $_POST['field_name'])) {
            echo json_encode(['msg' => 'Datos incompletos', 'icono' => 'error']);
            return;
        }

        $correction_id = $_POST['id'];
        $cert_number = $_POST['cert_number'];
        $field_name = $_POST['field_name'];
        $new_value = null;

        if ($field_name !== 'images') {
            if (!isset($_POST[$field_name])) {
                echo json_encode(['msg' => 'Valor propuesto no proporcionado', 'icono' => 'error']);
                return;
            }
            $new_value = $_POST[$field_name];
        }
        $user_id = $_SESSION['id_usuario'];

        $campoSanitizado = preg_replace('/[^a-zA-Z0-9_]/', '', $field_name);
        $old_value = null;

        if ($field_name !== 'images') {
            $campoSanitizado = preg_replace('/[^a-zA-Z0-9_]/', '', $field_name);
            $old_value = $this->model->obtenerValorActualCampo($cert_number, $campoSanitizado);
            if ($old_value === false) {
                echo json_encode(['msg' => 'Certificado no encontrado', 'icono' => 'error']);
                return;
            }
        
            if ($field_name === 'address_id') {
                // Usar el método especializado
                $this->model->actualizarDireccionCertificado($cert_number, $new_value);
            } else {
                // Campos normales
                $this->model->actualizarCampoCertificado($cert_number, $campoSanitizado, $new_value);
            }
        
            // Registrar el log como cualquier otro campo
            $this->model->insertarLogCorreccion($correction_id, $cert_number, $field_name, $old_value, $new_value, $user_id);
        }
        
        
        $monitoreos = $this->model->obtenerMonitoreos($cert_number);
        foreach ($monitoreos as $monitor) {
            switch ($monitor['monitor_type']) {
                case 'Fallo Encendido':
                    $data['monitor_fallo_encendido'] = $monitor['result'];
                    break;
                case 'Sistema Combustible':
                    $data['monitor_sistema_combustible'] = $monitor['result'];
                    break;
                case 'Catalizador Integral':
                    $data['monitor_integral_catalizador'] = $monitor['result'];
                    break;
                case 'Catalizador':
                    $data['monitor_catalizador'] = $monitor['result'];
                    break;
                case 'Sensor C2':
                    $data['monitor_sensor_c2'] = $monitor['result'];
                    break;
                case 'Resultado General':
                    $data['resultado_prueba'] = $monitor['result'];
                    break;
            }
        }
        $imagenes = [];

        if ($field_name === 'images') {
            $rutaTemp = 'uploads/temp/' . $cert_number;
            if (is_dir($rutaTemp)) {
                $archivosTemp = glob($rutaTemp . '/*.{jpg,jpeg,png,JPG,JPEG,PNG}', GLOB_BRACE);
                $imagenes = $archivosTemp ?: [];
            }

            // Si se usaron imágenes del input directamente
            if (!empty($_FILES['imagenes']['tmp_name'][0])) {
                $total = min(9, count($_FILES['imagenes']['tmp_name']));
                for ($i = 0; $i < $total; $i++) {
                    $tmpName = $_FILES['imagenes']['tmp_name'][$i];
                    $nombre = $_FILES['imagenes']['name'][$i];
                    $destino = $rutaTemp . '/' . uniqid("img_{$i}_") . '.' . pathinfo($nombre, PATHINFO_EXTENSION);
                    move_uploaded_file($tmpName, $destino);
                    $imagenes[] = $destino;
                }
            }
        }

        // Regenerar ZIP y PDF con nuevas imágenes si existen
        $this->generarPDFyZIP($cert_number, $imagenes);
        $this->model->actualizarEstadoSolicitud($correction_id, $user_id);
        echo json_encode(['msg' => 'Certificado actualizado correctamente', 'icono' => 'success']);
        return;
    }

    private function generarPDFyZIP($cert_number, $imagenes = [])
    {
        // 1. Obtener datos del certificado desde el modelo
        $data = $this->model->obtenerDatosCertificado($cert_number);
        if (!$data) return false;

        // 2. Mapear nombres amigables
        $data['propietario'] = $data['owner_name'];
        $data['fabricado_en'] = $data['mfg_in'];
        $data['modelo'] = $data['model'];
        $data['marca'] = $data['make'];
        $data['placa'] = $data['license_plate'] ?? '';
        $data['odometro'] = $data['odometer'] ?? '';
        $data['firma_inspector'] = $data['firma_inspector'] ?? '';
        $data['fecha'] = $data['test_date'] ?? $data['created_at'] ?? '';
        $data['fecha_expiracion'] = $data['expires'] ?? '';

        // 3. Obtener monitoreos desde la tabla monitoring_results
        $monitoreos = $this->model->obtenerMonitoreos($cert_number);
        foreach ($monitoreos as $m) {
            $key = 'monitor_' . strtolower(str_replace(' ', '_', $m['monitor_type']));
            $data[$key] = $m['result'];
        }

        // 4.  Definir ruta de trabajo del PDF
        $pdf_path = "uploads/temp/{$cert_number}.pdf";

        // 5. Validar rutas de imágenes con path absoluto
        $imagenesValidas = [];

        foreach ($imagenes as $rutaRelativa) {
            $rutaFull = $_SERVER['DOCUMENT_ROOT'] . '/MechanicalSystem/' . $rutaRelativa;
            if (file_exists($rutaFull)) {
                $imagenesValidas[] = $rutaFull;
            }
        }

        if (empty($imagenesValidas)) {
            // No había nuevas, extraemos las viejas del ZIP
            $imagenes = $this->extraerImagenesZIPExistente($cert_number);
        } else {
            // Usamos las rutas absolutas de las nuevas
            $imagenes = $imagenesValidas;
        }

        // 6. Generar PDF con esas imágenes
        $this->generarCertificadoPDF($cert_number, $pdf_path, $data, $data['address_id'], $imagenes);

        // 7. Generar ZIP con PDF e imágenes
        $this->generarArchivoZIP($cert_number, $pdf_path, $imagenes);
        sleep(2);
        // 7. Limpiar archivos temporales
        $this->limpiarTemporales($cert_number);
    }


    private function generarArchivoZIP($cert_number, $pdf_path, $imagenes)
    {
        $zip_path = "uploads/certificates/{$cert_number}.zip";
        $zip = new ZipArchive();

        if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Agregar el PDF
            $zip->addFile($pdf_path, basename($pdf_path));

            foreach ($imagenes as $img) {
                $zip->addFile($img, 'imagenes/' . basename($img));
            }

            $zip->close();
        }
    }

    private function generarCertificadoPDF($cert_number, $pdf_path, $data, $address_id, $imagenes = [])
    {
        // Obtener el nombre del inspector
        $inspector_id = $this->model->insertarInspector($data['inspector_name']);
        $inspector = $this->model->obtenerInspectorPorId($inspector_id);
        $inspector_name = $inspector ? $inspector['name'] : $data['inspector_name'];

        // Rutas de logo y QR para web
        $logoPath = BASE_URL . 'assets/images/logo.png';
        $qrPathRel = 'uploads/temp/' . $cert_number . '_qr.png';
        $qrWebPath = BASE_URL . $qrPathRel;
        $qrFileFullPath = $_SERVER['DOCUMENT_ROOT'] . '/MechanicalSystem/' . $qrPathRel;

        // Generar QR
        $contenidoQR = "{$data['vin']}|{$data['propietario']}|{$data['fabricado_en']}|{$data['year']}|{$data['modelo']}|{$data['marca']}";
        $optionsQR = new \chillerlan\QRCode\QROptions([
            'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
        ]);
        (new \chillerlan\QRCode\QRCode($optionsQR))->render($contenidoQR, $qrFileFullPath);

        // Dirección
        $direccion = $this->model->obtenerDireccionPorId($address_id);
        $direccion_texto = "{$direccion['number']} {$direccion['street']}, {$direccion['city']}, {$direccion['state']} {$direccion['zip']}";
        $barcodeGenerator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        file_put_contents("uploads/temp/{$cert_number}_vin_barcode.png", $barcodeGenerator->getBarcode($data['vin'], $barcodeGenerator::TYPE_CODE_128));
        file_put_contents("uploads/temp/{$cert_number}_cert_barcode.png", $barcodeGenerator->getBarcode($cert_number, $barcodeGenerator::TYPE_CODE_128));

        $vinBarcodeWeb = BASE_URL . 'uploads/temp/' . $cert_number . '_vin_barcode.png';
        $certBarcodeWeb = BASE_URL . 'uploads/temp/' . $cert_number . '_cert_barcode.png';

        $imagenesHTML = '';

        if (!empty($imagenes)) {
            foreach ($imagenes as $rutaFull) {
                // rutaFull ya viene absoluta si aplicaste el paso anterior,
                // si no, conviértela aquí también:
                $rutaFisica = strpos($rutaFull, $_SERVER['DOCUMENT_ROOT']) === 0
                    ? $rutaFull
                    : $_SERVER['DOCUMENT_ROOT'] . '/MechanicalSystem/' . $rutaFull;

                if (file_exists($rutaFisica)) {
                    $ext = pathinfo($rutaFisica, PATHINFO_EXTENSION);
                    $mime = $ext === 'png' ? 'image/png' : 'image/jpeg';
                    $imgData = base64_encode(file_get_contents($rutaFisica));
                    $imagenesHTML .= sprintf(
                        '<img src="data:%s;base64,%s" style="width:180px; margin:5px;">',
                        $mime,
                        $imgData
                    );
                }
            }
        } else {
            $imagenesHTML .= '<p style="color: red;">No se encontraron imágenes para mostrar.</p>';
        }


        // Monitoreos
        $data['monitor_fallo_encendido'] = $data['monitor_fallo_encendido'] ?? '';
        $data['monitor_sistema_combustible'] = $data['monitor_sistema_combustible'] ?? '';
        $data['monitor_integral_catalizador'] = $data['monitor_integral_catalizador'] ?? '';
        $data['monitor_catalizador'] = $data['monitor_catalizador'] ?? '';
        $data['monitor_sensor_c2'] = $data['monitor_sensor_c2'] ?? '';
        $data['resultado_prueba'] = $data['resultado_prueba'] ?? '';
        $data['firma_inspector'] = $data['firma_inspector'] ?? '';
        $data['ebitn'] = $data['ebitn'] ?? '';
        $data['fecha'] = $data['test_date'] ?? $data['created_at'] ?? '';
        $data['fecha_expiracion'] = $data['fecha_expiracion'] ?? '';
        $data['latitud'] = $data['latitud'] ?? '';
        $data['longitud'] = $data['longitud'] ?? '';



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
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        file_put_contents($pdf_path, $dompdf->output());
    }


    private function extraerImagenesZIPExistente($cert_number)
    {
        $zip_path = "uploads/certificates/{$cert_number}.zip";
        $temp_dir = "uploads/temp/{$cert_number}_old";

        if (!file_exists($zip_path)) {
            return [];
        }

        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zip_path) === TRUE) {
            $zip->extractTo($temp_dir); // Extrae TODO el contenido con estructura
            $zip->close();
        } else {
            return [];
        }

        // ✅ Asegúrate de que busque dentro de /imagenes/
        $imagenesExtraidas = glob($temp_dir . '/imagenes/*.{jpg,jpeg,png,JPG,JPEG,PNG}', GLOB_BRACE);

        return $imagenesExtraidas ?: [];
    }




    public function verImagenes($cert_number)
    {
        $imagenes = $this->model->extraerImagenesCertificado($cert_number);

        // Enviar rutas relativas válidas para el frontend
        $imagenesRelativas = array_map(function ($img) {
            return BASE_URL . str_replace('uploads/', 'uploads/', $img);
        }, $imagenes);

        echo json_encode($imagenesRelativas);
        die();
    }

    private function limpiarTemporales($cert_number)
    {
        $temp_dir = "uploads/temp/";
        $prefixes = [
            "{$cert_number}.pdf",
            "{$cert_number}_qr.png",
            "{$cert_number}_vin_barcode.png",
            "{$cert_number}_cert_barcode.png",
        ];

        // 1. Eliminar archivos individuales (PDF, QR, códigos de barra)
        foreach ($prefixes as $filename) {
            $ruta = $temp_dir . $filename;
            if (file_exists($ruta)) {
                unlink($ruta);
            }
        }

        // 2. Eliminar imágenes temporales generadas al subir
        $pattern_imgs = glob($temp_dir . "{$cert_number}_img_*");
        foreach ($pattern_imgs as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        // 3. Eliminar imágenes generadas desde ZIP
        $pattern_zip_imgs = glob($temp_dir . "{$cert_number}_zip_img_*");
        foreach ($pattern_zip_imgs as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        // 4. Eliminar carpeta de imágenes extraídas del ZIP
        $dir_zip_old = $temp_dir . "{$cert_number}_old";
        if (is_dir($dir_zip_old)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir_zip_old, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                ($file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname()));
            }
            rmdir($dir_zip_old);
        }

        // 5. Eliminar carpeta con imágenes sugeridas (uploads/temp/{cert_number}/)
        $carpetaSugeridas = "uploads/temp/{$cert_number}/";
        if (is_dir($carpetaSugeridas)) {
            $archivos = glob($carpetaSugeridas . '*');
            foreach ($archivos as $archivo) {
                if (is_file($archivo)) {
                    unlink($archivo);
                }
            }
            rmdir($carpetaSugeridas);
        }
    }


    public function verImagenesTemporales($cert_number)
    {
        $ruta = 'uploads/temp/' . $cert_number . '/';
        $imagenes = [];

        if (is_dir($ruta)) {
            foreach (glob($ruta . "*.{jpg,jpeg,png,JPG,JPEG,PNG}", GLOB_BRACE) as $img) {
                $imagenes[] = BASE_URL . str_replace('uploads/', 'uploads/', $img);
            }
        }

        echo json_encode($imagenes);
        die();
    }

        //eliminar error
        public function delete($id_error)
        {
            if (is_numeric($id_error)) {
                $data = $this->model->eliminar($id_error);
                if ($data == 1) {
                    $respuesta = array('msg' => 'Error rechazado Correctamente', 'icono' => 'success');
                } else {
                    $respuesta = array('msg' => 'error al eliminar error', 'icono' => 'error');
                }
            } else {
                $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
            }
            echo json_encode($respuesta);
            die();
        }
}
