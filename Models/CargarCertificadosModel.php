<?php
class CargarCertificadosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function consultarCertificado($cert_number)
    {
        $sql = "SELECT cert_number FROM certificates WHERE cert_number = ?";
        return $this->select($sql, [$cert_number]);
    }

    public function consultarDireccion($number, $street, $city, $state, $zip)
    {
        $sql = "SELECT id FROM addresses 
                WHERE `number` = ? AND `street` = ? AND `city` = ? AND `state` = ? AND `zip` = ?";
        return $this->select($sql, [$number, $street, $city, $state, $zip]);
    }

    public function insertarDireccion($number, $street, $city, $state, $zip)
    {
        $sql = "INSERT INTO addresses (`number`, `street`, `city`, `state`, `zip`) 
                VALUES (?, ?, ?, ?, ?)";
        return $this->insertar($sql, [$number, $street, $city, $state, $zip]);
    }

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
        $inspector_id,
        $test_date,
        $expires,
        $source_file
    ) {
        $zip_path = 'uploads/certificates/' . $cert_number . '.zip';

        $sql = "INSERT INTO certificates (
            cert_number, vin, address_id, phone, year, mfg_in, make, 
            owner_name, model, license_plate, odometer, inspector_id, 
            test_date, expires, source_file, zip_file_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $cert_number, $vin, $address_id, $phone, $year, $mfg_in, $make,
            $owner_name, $model, $license_plate, $odometer, $inspector_id,
            $test_date, $expires, $source_file, $zip_path
        ];

        return $this->insertar($sql, $params);
    }

    public function actualizarCertificado(
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
        $test_date,
        $expires,
        $source_file
    ) {
        $sql = "UPDATE certificates SET 
            vin = ?, address_id = ?, phone = ?, year = ?, mfg_in = ?, make = ?, 
            owner_name = ?, model = ?, license_plate = ?, odometer = ?, 
            inspector_id = ?, test_date = ?, expires = ?, source_file = ?
            WHERE cert_number = ?";

        $params = [
            $vin, $address_id, $phone, $year, $mfg_in, $make,
            $owner_name, $model, $license_plate, $odometer,
            $inspector_id, $test_date, $expires, $source_file, $cert_number
        ];

        return $this->save($sql, $params);
    }

    public function eliminarMonitoreo($cert_number)
    {
        $sql = "DELETE FROM monitoring_results WHERE cert_number = ?";
        return $this->save($sql, [$cert_number]);
    }

    public function insertarMonitoreo($cert_number, $tipo, $resultado)
    {
        $sql = "INSERT INTO monitoring_results (cert_number, monitor_type, result) 
                VALUES (?, ?, ?)";
        return $this->insertar($sql, [$cert_number, $tipo, $resultado]);
    }

    public function registrarImportacion($file_name, $status, $error_message, $id_usuario)
    {
        $sql = "INSERT INTO import_logs (file_name, status, error_message, imported_by) 
                VALUES (?, ?, ?, ?)";
        return $this->insertar($sql, [$file_name, $status, $error_message, $id_usuario]);
    }

    public function registrarImportacionAlternativa($file_name, $status, $error_message)
    {
        $sql = "INSERT INTO import_logs (file_name, status, error_message) 
                VALUES (?, ?, ?)";
        return $this->insertar($sql, [$file_name, $status, $error_message]);
    }

    public function actualizarImportacion($import_id, $records_imported, $records_failed, $status, $error_message)
    {
        $sql = "UPDATE import_logs 
                SET records_imported = ?, records_failed = ?, status = ?, error_message = ? 
                WHERE id = ?";
        return $this->save($sql, [$records_imported, $records_failed, $status, $error_message, $import_id]);
    }

    public function obtenerImportLogs()
    {
        $sql = "SELECT * FROM import_logs ORDER BY created_at DESC";
        return $this->selectAll($sql);
    }

    public function obtenerIdUsuarioPorNombre($nombre_usuario)
    {
        $sql = "SELECT id FROM usuarios WHERE nombre_usuario = ?";
        $resultado = $this->select($sql, [$nombre_usuario]);
        return $resultado ? $resultado['id'] : 0;
    }

    public function consultarInspector($nombre)
    {
        $sql = "SELECT id FROM inspectors WHERE name = ?";
        return $this->select($sql, [$nombre]);
    }

    public function insertarInspector($nombre)
    {
        $sql = "INSERT INTO inspectors (name) VALUES (?)";
        return $this->insertar($sql, [$nombre]);
    }
}
