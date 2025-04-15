<?php
class CrearCertificadosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    // Consultar si existe un certificado
    public function consultarCertificado($cert_number)
    {
        $sql = "SELECT cert_number FROM certificates WHERE cert_number = '{$cert_number}'";
        return $this->select($sql);
    }

    // Verificar si existe una dirección
    public function consultarDireccion($number, $street, $city, $state, $zip)
    {
        $sql = "SELECT id FROM addresses WHERE `number` = '{$number}' AND `street` = '{$street}' AND `city` = '{$city}' AND `state` = '{$state}' AND `zip` = '{$zip}'";
        return $this->select($sql);
    }

    // Insertar nueva dirección
    public function insertarDireccion($number, $street, $city, $state, $zip)
    {
        $sql = "INSERT INTO addresses (`number`, `street`, `city`, `state`, `zip`) VALUES (?, ?, ?, ?, ?)";
        return $this->insertar($sql, [$number, $street, $city, $state, $zip]);
    }

    // Insertar certificado en tabla real
    public function insertarCertificado(
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
    ) {
        $zip_path = 'uploads/certificates/' . $cert_number . '.zip';

        $sql = "INSERT INTO certificates (
            cert_number, vin, address_id, phone, year, mfg_in, make,
            owner_name, model, license_plate, odometer, inspector_name,
            test_date, expires, source_file, zip_file_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
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
            $source_file,
            $zip_path
        ];

        return $this->insertar($sql, $params);
    }

    // Insertar registros de monitoreo
    public function insertarMonitoreo($cert_number, $tipo, $resultado)
    {
        $sql = "INSERT INTO monitoring_results (cert_number, monitor_type, result) VALUES (?, ?, ?)";
        return $this->insertar($sql, [$cert_number, $tipo, $resultado]);
    }

    // Registrar log de importación (manual en este caso)
    public function registrarImportacion($file_name, $status, $error_message, $id_usuario)
    {
        $sql = "INSERT INTO import_logs (file_name, status, error_message, imported_by) VALUES (?, ?, ?, ?)";
        return $this->insertar($sql, [$file_name, $status, $error_message, $id_usuario]);
    }

    public function registrarImportacionAlternativa($file_name, $status, $error_message)
    {
        $sql = "INSERT INTO import_logs (file_name, status, error_message) VALUES (?, ?, ?)";
        return $this->insertar($sql, [$file_name, $status, $error_message]);
    }

    public function actualizarImportacion($import_id, $records_imported, $records_failed, $status, $error_message)
    {
        $sql = "UPDATE import_logs SET records_imported = ?, records_failed = ?, status = ?, error_message = ? WHERE id = ?";
        return $this->save($sql, [$records_imported, $records_failed, $status, $error_message, $import_id]);
    }

    public function obtenerImportLogs()
    {
        $sql = "SELECT * FROM import_logs ORDER BY created_at DESC";
        return $this->selectAll($sql);
    }

    public function obtenerIdUsuarioPorNombre($nombre_usuario)
    {
        $sql = "SELECT id FROM usuarios WHERE nombre_usuario = '{$nombre_usuario}'";
        $resultado = $this->select($sql);
        return $resultado ? $resultado['id'] : 0;
    }

    public function obtenerDirecciones()
    {
        $sql = "SELECT id, CONCAT(`number`, ' ', `street`, ', ', `city`, ', ', `state`, ' ', `zip`) AS nombre
            FROM addresses
            ORDER BY id DESC";
        return $this->selectAll($sql);
    }
    public function contarCertificadosPorUsuario($id_usuario)
    {
        $sql = "SELECT COUNT(*) as total FROM certificates WHERE cert_number LIKE 'MEX{$id_usuario}-%'";
        $res = $this->select($sql);
        return $res ? $res['total'] : 0;
    }

    public function obtenerDireccionPorId($id)
    {
        $sql = "SELECT `number`, `street`, `city`, `state`, `zip` FROM addresses WHERE id = {$id}";
        return $this->select($sql); 
    }
    
}
