<?php
class CargarCertificados extends Controller
{
    public function __construct()
    {

        parent::__construct();
        session_start();
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        } else {
        }
    }
    private function verificarRol($rolPermitido)
    {
        if ($_SESSION['rol_usuario'] != $rolPermitido && $_SESSION['rol_usuario'] != 1) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
    }

    public function index()
    {
        $data['title'] = 'Cargar Certificados';
        $this->views->getView('admin/CargarCertificados', "index", $data);
    }

    public function cargar()
    {
        $this->verificarRol(3);
        if (!isset($_FILES['fileUpload'])) {
            $this->responderJSON('Error: No se recibió ningún archivo', 'error');
            return;
        }

        $archivo = $_FILES['fileUpload'];
        $nombreArchivo = $archivo['name'];
        $tipo = $archivo['type'];

        // Verificar tipo de archivo
        if ($tipo != 'application/json') {
            $this->responderJSON('El archivo debe ser JSON', 'error');
            return;
        }

        // Leer contenido del archivo
        $contenidoJson = file_get_contents($archivo['tmp_name']);
        $datos = json_decode($contenidoJson, true);

        if ($datos === null) {
            $this->responderJSON('Error al decodificar el JSON', 'error');
            return;
        }

        // Procesar los datos
        $importados = $this->procesarCertificados($datos);

        if ($importados > 0) {
            $this->responderJSON('Datos cargados correctamente: ' . $importados . ' certificados procesados', 'success');
        } else {
            $this->responderJSON('Error al cargar la información', 'error');
        }
    }

    private function procesarCertificados($datos)
     {
        $importados = 0;
        $fallidos = 0;
        $error_msg = '';
        $id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 0;
        $usuario_nombre = isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : 'Sistema';
    
        // Crear registro de importación (intenta con la versión que incluye usuario)
        try {
            $import_id = $this->model->registrarImportacion(
                'import_json_' . date('YmdHis'),
                'partial',
                '',
                $id_usuario
            );
        } catch (Exception $e) {
            // Si falla, intenta con la versión alternativa
            $import_id = $this->model->registrarImportacionAlternativa(
                'import_json_' . date('YmdHis'),
                'partial',
                $id_usuario
            );
        }
    
        if (!$import_id) {
            return 0; // Si no se pudo crear el registro de importación, salimos
        }
    
        // 💡 Divide el array de certificados en bloques de 500
        $bloques = array_chunk($datos, 500);
    
        // Procesar por bloques
        foreach ($bloques as $bloque) {
            foreach ($bloque as $certificado) {
                try {
                    if (!isset($certificado['cert_number'])) {
                        throw new Exception('El certificado no tiene número');
                    }
    
                    // Si no viene dirección o está vacía, usamos la dirección por defecto
                    $direccionGenericaId = 2866;
    
                    if (isset($certificado['address']) && is_array($certificado['address'])) {
                        $address_id = $this->procesarDireccion($certificado['address']);
                        if (!$address_id) {
                            $address_id = $direccionGenericaId;
                        }
                    } else {
                        $address_id = $direccionGenericaId;
                    }
    
                    // Verificar si ya existe el certificado
                    $cert_exists = $this->model->consultarCertificado($certificado['cert_number']);
    
                    if (!empty($cert_exists)) {
                        // Actualizar certificado existente
                        $result = $this->actualizarCertificado($certificado, $address_id);
                    } else {
                        // Insertar nuevo certificado
                        $result = $this->insertarCertificado($certificado, $address_id);
                    }
    
                    // Procesar monitoreo si existe
                    if (isset($certificado['monitoring']) && !empty($certificado['monitoring'])) {
                        $this->procesarMonitoreo($certificado['cert_number'], $certificado['monitoring']);
                    }
    
                    if ($result) {
                        $importados++;
                    } else {
                        $fallidos++;
                        $error_msg .= 'Error al procesar certificado ' . $certificado['cert_number'] . ' | ';
                    }
                } catch (Exception $e) {
                    $fallidos++;
                    $error_msg .= $e->getMessage() . ' | ';
                }
            }
    
            // ✅ Opcional: da un respiro al servidor y libera el buffer de salida
            flush();
        }
    
        // Actualizar registro de importación
        $estado_final = ($fallidos > 0) ? 'partial' : 'success';
        $mensaje_final = "Importado por: " . $usuario_nombre . " | Errores: " . $error_msg;
        $this->model->actualizarImportacion($import_id, $importados, $fallidos, $estado_final, $mensaje_final);
    
        return $importados;
     }
    

    private function procesarDireccion($address)
    {
        if (!is_array($address)) {
            throw new Exception('La dirección no es válida');
        }

        $number = isset($address['number']) ? $address['number'] : '';
        $street = isset($address['street']) ? $address['street'] : '';
        $city = isset($address['city']) ? $address['city'] : '';
        $state = isset($address['state']) ? $address['state'] : '';
        $zip = isset($address['zip']) ? $address['zip'] : '';

        // Verificar si la dirección ya existe
        $existente = $this->model->consultarDireccion($number, $street, $city, $state, $zip);

        if (!empty($existente)) {
            return $existente['id'];
        }

        // Insertar nueva dirección si no existe
        return $this->model->insertarDireccion($number, $street, $city, $state, $zip);
    }

    private function insertarCertificado($certificado, $address_id)
    {
        $cert_number = $certificado['cert_number'];
        $vin = isset($certificado['vin']) ? $certificado['vin'] : NULL;
        $phone = isset($certificado['phone']) ? $certificado['phone'] : NULL;
        $year = isset($certificado['year']) ? $certificado['year'] : NULL;
        $mfg_in = isset($certificado['mfg_in']) ? $certificado['mfg_in'] : NULL;
        $make = isset($certificado['make']) ? $certificado['make'] : NULL;
        $owner_name = isset($certificado['owner_name']) ? $certificado['owner_name'] : NULL;
        $model = isset($certificado['model']) ? $certificado['model'] : NULL;
        $license_plate = isset($certificado['license_plate']) ? $certificado['license_plate'] : NULL;
        $odometer = isset($certificado['odometer']) ? $certificado['odometer'] : NULL;
        $inspector_name = isset($certificado['inspector_name']) ? $certificado['inspector_name'] : NULL;
        $test_date = isset($certificado['test_date']) ? $certificado['test_date'] : NULL;
        $expires = isset($certificado['expires']) ? $certificado['expires'] : NULL;
        $source_file = isset($certificado['source_file']) ? $certificado['source_file'] : NULL;

        return $this->model->insertarCertificado(
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
            $inspector_name,
            $test_date,
            $expires,
            $source_file
        );
    }

    private function actualizarCertificado($certificado, $address_id)
    {
        $cert_number = $certificado['cert_number'];
        $vin = isset($certificado['vin']) ? $certificado['vin'] : NULL;
        $phone = isset($certificado['phone']) ? $certificado['phone'] : NULL;
        $year = isset($certificado['year']) ? $certificado['year'] : NULL;
        $mfg_in = isset($certificado['mfg_in']) ? $certificado['mfg_in'] : NULL;
        $make = isset($certificado['make']) ? $certificado['make'] : NULL;
        $owner_name = isset($certificado['owner_name']) ? $certificado['owner_name'] : NULL;
        $model = isset($certificado['model']) ? $certificado['model'] : NULL;
        $license_plate = isset($certificado['license_plate']) ? $certificado['license_plate'] : NULL;
        $odometer = isset($certificado['odometer']) ? $certificado['odometer'] : NULL;
        $inspector_name = isset($certificado['inspector_name']) ? $certificado['inspector_name'] : NULL;
        $test_date = isset($certificado['test_date']) ? $certificado['test_date'] : NULL;
        $expires = isset($certificado['expires']) ? $certificado['expires'] : NULL;
        $source_file = isset($certificado['source_file']) ? $certificado['source_file'] : NULL;

        $result = $this->model->actualizarCertificado(
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
            $inspector_name,
            $test_date,
            $expires,
            $source_file
        );

        return ($result > 0);
    }

    private function procesarMonitoreo($cert_number, $monitoring)
    {
        // Eliminar los registros anteriores de monitoreo para este certificado
        $this->model->eliminarMonitoreo($cert_number);

        // Insertar los nuevos valores de monitoreo
        foreach ($monitoring as $tipo => $resultado) {
            $this->model->insertarMonitoreo($cert_number, $tipo, $resultado);
        }

        return true;
    }

    private function responderJSON($mensaje, $icono)
    {
        $respuesta = array('msg' => $mensaje, 'icono' => $icono);
        echo json_encode($respuesta);
        die();
    }

    // Método para ver logs de importación (opcional)
    public function logs()
    {
        $data['title'] = 'Logs de Importación';
        $data['logs'] = $this->model->obtenerImportLogs();
        $this->views->getView('admin/CargarCertificados', "logs", $data);
    }
}
