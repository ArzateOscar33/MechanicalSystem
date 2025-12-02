<?php
class CrearCertificadosModel extends Query
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
    $created_by,    
    $test_date,
    $expires,
    $source_file
) {
    $zip_path = 'uploads/certificates/' . $cert_number . '.zip';

    $sql = "INSERT INTO certificates (
        cert_number, vin, address_id, phone, year, mfg_in, make,
        owner_name, model, license_plate, odometer, inspector_id,
        created_by, test_date, expires, source_file, zip_file_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    return $this->insertar($sql, [
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
        $created_by,   
        $test_date,
        $expires,
        $source_file,
        $zip_path
    ]);
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
        $res = $this->select($sql, [$nombre_usuario]);
        return $res ? $res['id'] : 0;
    }

    public function obtenerDirecciones()
    {
        $sql = "SELECT id, CONCAT(`number`, ' ', `street`, ', ', `city`, ', ', `state`, ' ', `zip`) AS nombre
                FROM addresses
                ORDER BY id DESC";
        return $this->selectAll($sql);
    }

   /* public function contarCertificadosPorUsuario($id_usuario)
    {
        $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(cert_number, '-', -1) AS UNSIGNED)) AS ultimo 
            FROM certificates 
            WHERE cert_number LIKE ?";

        $like = 'MEX' . $id_usuario . '-%';
        $res = $this->select($sql, [$like]);

        return $res && $res['ultimo'] !== null ? intval($res['ultimo']) + 1 : 1;
    }
*/

    public function obtenerDireccionPorId($id)
    {
        $sql = "SELECT `number`, `street`, `city`, `state`, `zip`, `latitude`, `longitude` 
                FROM addresses WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    public function obtenerInspectores()
    {
        $sql = "SELECT id, name FROM inspectors ORDER BY name ASC";
        return $this->selectAll($sql);
    }

    public function obtenerInspectorPorId($id)
    {
        $sql = "SELECT * FROM inspectors WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    public function obtenerInspectorPorNombre($nombre)
    {
        $sql = "SELECT id FROM inspectors WHERE name = ?";
        return $this->select($sql, [$nombre]);
    }

    public function insertarInspector($nombre)
    {
        $inspector = $this->obtenerInspectorPorNombre($nombre);
        if ($inspector) {
            return $inspector['id'];
        }

        $sql = "INSERT INTO inspectors (name) VALUES (?)";
        return $this->insertar($sql, [$nombre]);
    }

    public function insertarDireccionConCoordenadas($number, $street, $city, $state, $zip, $lat, $lon)
    {
        $sql = "INSERT INTO addresses (`number`, `street`, `city`, `state`, `zip`, `latitude`, `longitude`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->insertar($sql, [$number, $street, $city, $state, $zip, $lat, $lon]);
    }

    public function consultarDireccionConCoordenadas($number, $street, $city, $state, $zip, $lat, $lon)
    {
        $sql = "SELECT id FROM addresses 
                WHERE `number` = ? AND `street` = ? AND `city` = ? 
                  AND `state` = ? AND `zip` = ? AND `latitude` = ? AND `longitude` = ?";
        return $this->select($sql, [$number, $street, $city, $state, $zip, $lat, $lon]);
    }

    public function vinConCertificadoActivo($vin, $fechaReferencia)
{
    // Busca si existe algún certificado cuyo VIN coincida
    // y cuya fecha de expiración sea igual o posterior a la nueva prueba.
    $sql = "SELECT cert_number 
            FROM certificates 
            WHERE vin = ? 
              AND expires >= ?
            LIMIT 1";

    return $this->select($sql, [$vin, $fechaReferencia]);
}
 
 public function obtenerCertNumberPreliminar()
{
    $sql = "SELECT numero 
            FROM secuencias_certificado 
            WHERE certificado = 'CERTIFICADO'
            LIMIT 1";
    $res = $this->select($sql, []);

    $actual = $res && isset($res['numero']) ? intval($res['numero']) : 0;
    $siguiente = $actual + 1;

    $consecutivo = str_pad($siguiente, 8, "0", STR_PAD_LEFT);
    return 'MEX-' . $consecutivo;//AUQI SE PUEDE CAMBIAR EL NOMBRE DEL CERTIFICADO
}

/**
 * 2) Número definitivo (actualiza + lee).
 *    Este se usa SOLO al guardar el certificado.
 */
public function generarCertNumberGlobal()
{
    // Incrementar el número en BD
    $sqlUpdate = "UPDATE secuencias_certificado 
                  SET numero = numero + 1 
                  WHERE certificado = 'CERTIFICADO'";
    $this->save($sqlUpdate, []); // UPDATE

    // Obtener el nuevo valor
    $sqlSelect = "SELECT numero 
                  FROM secuencias_certificado 
                  WHERE certificado = 'CERTIFICADO'
                  LIMIT 1";
    $res = $this->select($sqlSelect, []);

    $numero = $res && isset($res['numero']) ? intval($res['numero']) : 1;

    $consecutivo = str_pad($numero, 8, "0", STR_PAD_LEFT);
    return 'MEX-' . $consecutivo;
}
}
