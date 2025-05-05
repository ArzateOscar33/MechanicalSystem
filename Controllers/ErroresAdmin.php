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
            $data[$i]['accion'] = '<div class="d-flex">
            <button class="btn btn-primary" type="button" onclick="editCertificate(\'' . $data[$i]['id'] . '\')"><i class="fas fa-edit"></i></button>
            <button class="btn btn-info" type="button" onclick="verImagenes(\'' . $data[$i]['certificate_id'] . '\')">
  <i class="fas fa-image"></i>
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
    /* public function registrar()
    {
        if (isset($_POST['nombre'])) {
            $username = $_POST['username'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $correo = $_POST['correo'];
            $clave = $_POST['clave'];
            $phone = $_POST['phone'];
            $role_id = $_POST['rol']; // <-- ¡nuevo!
            $id = $_POST['id'];
    
            if (empty($nombre) || empty($apellido) || empty($role_id)) {
                $respuesta = array('msg' => 'Todos los campos son requeridos', 'icono' => 'warning');
            } else {
                if (empty($id)) {
                    $result = $this->model->verificarCorreo($correo);
                    if (empty($result)) {
                        $hash = password_hash($clave, PASSWORD_DEFAULT);
                        $data = $this->model->registrar($username, $nombre, $apellido, $correo, $hash, $phone, $role_id);
                        if ($data > 0) {
                            $respuesta = array('msg' => 'Usuario registrado', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'Error al registrar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'Correo ya existe', 'icono' => 'warning');
                    }
                } else {
                    // Puedes agregar aquí la lógica para modificar el rol también si lo deseas
                    $data = $this->model->modificar($username, $nombre, $apellido, $correo, $phone, $role_id, $id);
                    if ($data == 1) {
                        $respuesta = array('msg' => 'Usuario modificado', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'Error al modificar', 'icono' => 'error');
                    }
                }
            }
            echo json_encode($respuesta);
        }
        die();
    }
    
    //eliminar user
    public function delete($idUser)
    {
        if (is_numeric($idUser)) {
            $data = $this->model->eliminar($idUser);
            if ($data == 1) {
                $respuesta = array('msg' => 'usuario dado de baja', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al eliminar', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }*/
    //editar user
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
        $new_value = $_POST[$field_name];
        $user_id = $_SESSION['id_usuario'];

        $campoSanitizado = preg_replace('/[^a-zA-Z0-9_]/', '', $field_name);

        $old_value = $this->model->obtenerValorActualCampo($cert_number, $campoSanitizado);
        if ($old_value === false) {
            echo json_encode(['msg' => 'Certificado no encontrado', 'icono' => 'error']);
            return;
        }

        $this->model->actualizarCampoCertificado($cert_number, $campoSanitizado, $new_value);
        $this->model->insertarLogCorreccion($correction_id, $cert_number, $field_name, $old_value, $new_value, $user_id);
        $this->model->actualizarEstadoSolicitud($correction_id, $user_id);
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

        // Regenerar ZIP y PDF
        $this->generarPDFyZIP($cert_number);

        echo json_encode(['msg' => 'Certificado actualizado correctamente', 'icono' => 'success']);
        return;
    }

    private function generarPDFyZIP($cert_number)
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

        // 4. Generar PDF
        $pdf_path = "uploads/temp/{$cert_number}.pdf";
        $this->generarCertificadoPDF($cert_number, $pdf_path, $data, $data['address_id']);

        // 5. Extraer imágenes originales desde ZIP (si no hay nuevas)
        $imagenes = $this->extraerImagenesZIPExistente($cert_number);

        // ✅ Agrega las imágenes al array $data ANTES de generar el PDF
        $data['imagenes_zip'] = $imagenes;

        // 6. Generar PDF con esas imágenes
        $this->generarCertificadoPDF($cert_number, $pdf_path, $data, $data['address_id']);

        // 7. Generar ZIP con PDF e imágenes
        $this->generarArchivoZIP($cert_number, $pdf_path, $imagenes);

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

            // Incluir imágenes
            if (isset($_FILES['imagenes']['tmp_name']) && is_array($_FILES['imagenes']['tmp_name']) && count($_FILES['imagenes']['tmp_name']) > 0) {
                // Se subieron nuevas imágenes, agregar esas
                $total = min(9, count($_FILES['imagenes']['tmp_name']));
                for ($i = 0; $i < $total; $i++) {
                    if (is_uploaded_file($_FILES['imagenes']['tmp_name'][$i])) {
                        $nombreArchivo = basename($_FILES['imagenes']['name'][$i]);
                        $zip->addFile($_FILES['imagenes']['tmp_name'][$i], "imagenes/{$nombreArchivo}");
                    }
                }
            } else {
                // No se subieron nuevas imágenes, usar las extraídas
                foreach ($imagenes as $img) {
                    $zip->addFile($img, 'imagenes/' . basename($img));
                }
            }

            $zip->close();
        }
    }

    private function generarCertificadoPDF($cert_number, $pdf_path, $data, $address_id)
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
        $nuevasImagenes = false;

        // Revisar si se subieron nuevas imágenes
        if (isset($_FILES['imagenes']['tmp_name']) && is_array($_FILES['imagenes']['tmp_name']) && count($_FILES['imagenes']['tmp_name']) > 0 && $_FILES['imagenes']['tmp_name'][0] !== '') {
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
                    $nuevasImagenes = true;
                }
            }
        }

        // Si no se subieron nuevas imágenes, usar las del ZIP
        if (!$nuevasImagenes && isset($data['imagenes_zip']) && is_array($data['imagenes_zip'])) {
            foreach ($data['imagenes_zip'] as $i => $imgRuta) {
                $ext = pathinfo($imgRuta, PATHINFO_EXTENSION);
                $nombreTemp = "{$cert_number}_zip_img_{$i}." . $ext;
                $rutaDestino = 'uploads/temp/' . $nombreTemp;
                $rutaFisica = $_SERVER['DOCUMENT_ROOT'] . '/MechanicalSystem/' . $rutaDestino;
                copy($imgRuta, $rutaFisica);
                $rutaWeb = BASE_URL . $rutaDestino;
                $imagenesHTML .= '<img src="' . $rutaWeb . '" width="150" style="margin:5px;">';
            }
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
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header-barcodes { display: flex; justify-content: space-between; margin-bottom: 10px; }
                .barcode-block { text-align: center; flex: 1; }
                .barcode-block img { max-height: 50px; }
                .title { font-size: 20px; font-weight: bold; text-align: center; margin: 10px 0; }
                .logo { text-align: center; margin-bottom: 15px; }
                .section { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 8px; }
                table, th, td { border: 1px solid #999; }
                th { background-color: #f0f0f0; padding: 6px; text-align: left; }
                td { padding: 6px; }
            </style>
    
            <div class="header-barcodes">
                <div class="barcode-block"><div>VIN</div><img src="' . $vinBarcodeWeb . '"></div>
                <div class="barcode-block"><div>Cert Number</div><img src="' . $certBarcodeWeb . '"></div>
            </div>
    
            <div class="title">MECHANICAL EMISSIONS SERVICES LLC</div>
            <div class="logo"><img src="' . $logoPath . '" height="80"></div>
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
    
            <div class="section"><b>Código QR del Certificado</b><br><img src="' . $qrWebPath . '" width="150"></div>
    
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
}

}
