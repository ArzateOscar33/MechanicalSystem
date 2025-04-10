<?php
class CargarCertificadosModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    
    public function procesarCertificados($datos, $usuario_nombre)
    {
        $importados = 0;
        $fallidos = 0;
        $status = 'success';
        $error_msg = '';
        
        // Crear registro de importación
        $sql_import = "INSERT INTO import_logs (file_name, status, error_message) VALUES (?, ?, ?)";
        $import_id = $this->insertar($sql_import, array('import_json_'.date('YmdHis'), 'partial', 'Importado por: '.$usuario_nombre));
        
        // Procesar cada certificado
        foreach ($datos as $certificado) {
            try {
                // Procesar dirección
                $address_id = $this->procesarDireccion($certificado['address']);
                
                // Verificar si ya existe el certificado
                $sql_check = "SELECT cert_number FROM certificates WHERE cert_number = '{$certificado['cert_number']}'";
                $cert_exists = $this->select($sql_check);
                
                if (!empty($cert_exists)) {
                    // Actualizar certificado existente
                    $this->actualizarCertificado($certificado, $address_id);
                } else {
                    // Insertar nuevo certificado
                    $this->insertarCertificado($certificado, $address_id);
                }
                
                // Procesar monitoreo si existe
                if (isset($certificado['monitoring']) && !empty($certificado['monitoring'])) {
                    $this->procesarMonitoreo($certificado['cert_number'], $certificado['monitoring']);
                }
                
                $importados++;
            } catch (Exception $e) {
                $fallidos++;
                $error_msg .= $e->getMessage() . ' | ';
                $status = 'partial';
            }
        }
        
        // Actualizar registro de importación
        $estado_final = ($fallidos > 0) ? 'partial' : 'success';
        $sql_update = "UPDATE import_logs SET records_imported = ?, records_failed = ?, status = ?, error_message = ? WHERE id = ?";
        $this->save($sql_update, array($importados, $fallidos, $estado_final, $error_msg, $import_id));
        
        return array(
            'importados' => $importados, 
            'fallidos' => $fallidos, 
            'status' => $status
        );
    }
    
    private function procesarDireccion($address)
    {
        // Verificar si la dirección ya existe
        $number = isset($address['number']) ? $address['number'] : '';
        $street = isset($address['street']) ? $address['street'] : '';
        $city = isset($address['city']) ? $address['city'] : '';
        $state = isset($address['state']) ? $address['state'] : '';
        $zip = isset($address['zip']) ? $address['zip'] : '';
        
        $sql_check = "SELECT id FROM addresses WHERE 
                     number = '$number' AND street = '$street' AND 
                     city = '$city' AND state = '$state' AND zip = '$zip'";
        
        $existente = $this->select($sql_check);
        
        if (!empty($existente)) {
            return $existente['id'];
        }
        
        // Insertar nueva dirección
        $sql_insert = "INSERT INTO addresses (number, street, city, state, zip) VALUES (?, ?, ?, ?, ?)";
        $params = array($number, $street, $city, $state, $zip);
        return $this->insertar($sql_insert, $params);
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
        
        $zip_path = 'uploads/certificates/' . $cert_number . '.zip';
        
        $sql = "INSERT INTO certificates (
                cert_number, vin, address_id, phone, year, mfg_in, make, 
                owner_name, model, license_plate, odometer, inspector_name, 
                test_date, expires, source_file, zip_file_path
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = array(
            $cert_number, $vin, $address_id, $phone, $year, $mfg_in, $make, 
            $owner_name, $model, $license_plate, $odometer, $inspector_name, 
            $test_date, $expires, $source_file, $zip_path
        );
        
        return $this->insertar($sql, $params);
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
        
        $sql = "UPDATE certificates SET 
                vin = ?, address_id = ?, phone = ?, year = ?, mfg_in = ?, make = ?, 
                owner_name = ?, model = ?, license_plate = ?, odometer = ?, 
                inspector_name = ?, test_date = ?, expires = ?, source_file = ?
                WHERE cert_number = ?";
        
        $params = array(
            $vin, $address_id, $phone, $year, $mfg_in, $make, 
            $owner_name, $model, $license_plate, $odometer, 
            $inspector_name, $test_date, $expires, $source_file,
            $cert_number
        );
        
        return $this->save($sql, $params);
    }
    
    private function procesarMonitoreo($cert_number, $monitoring)
    {
        // Eliminar los registros anteriores de monitoreo para este certificado
        $sql_delete = "DELETE FROM monitoring_results WHERE cert_number = ?";
        $this->save($sql_delete, array($cert_number));
        
        // Insertar los nuevos valores de monitoreo
        foreach ($monitoring as $tipo => $resultado) {
            $sql = "INSERT INTO monitoring_results (cert_number, monitor_type, result) VALUES (?, ?, ?)";
            $this->insertar($sql, array($cert_number, $tipo, $resultado));
        }
        
        return true;
    }
}
?>