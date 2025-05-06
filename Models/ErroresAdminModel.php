<?php
class ErroresAdminModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getErrores($estado)
    {
        $sql = "SELECT 
                r.id,
                r.certificate_id,
                r.field_name,
                r.current_value,
                r.proposed_value,
                r.reason,
                r.status,
                r.created_at,
                u.first_name AS user_name,
                i1.name AS inspector_actual,
                i2.name AS inspector_propuesto,
                CONCAT(a1.number, ' ', a1.street, ', ', a1.city, ', ', a1.state, ' ', a1.zip) AS direccion_actual,
                CONCAT(a2.number, ' ', a2.street, ', ', a2.city, ', ', a2.state, ' ', a2.zip) AS direccion_propuesta
                FROM correction_requests r
                JOIN users u ON r.user_id = u.id
                LEFT JOIN inspectors i1 ON r.field_name = 'inspector_id' AND r.current_value = i1.id
                LEFT JOIN inspectors i2 ON r.field_name = 'inspector_id' AND r.proposed_value = i2.id
                LEFT JOIN addresses a1 ON r.field_name = 'address_id' AND r.current_value = a1.id
                LEFT JOIN addresses a2 ON r.field_name = 'address_id' AND r.proposed_value = a2.id

                WHERE r.status = '$estado'";
        return $this->selectAll($sql);
    }

    public function getErroresResueltos($estado)
    {
        $sql = "SELECT 
        c.id,
        c.certificate_id,
        CONCAT(u.first_name, ' ', u.last_name) AS user_name,
        c.field_name, 
        c.status,
        c.proposed_value as corregido, 
        c.updated_at,
        c.reviewed_at
        
                FROM  correction_requests c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.status = '$estado'
                ORDER BY updated_at ASC";
        return $this->selectAll($sql);
    }


    public function obtenerCertificados($cert_number)
    {
        $sql = "SELECT 
                    c.cert_number, 
                    c.vin,
                    c.make, 
                    c.owner_name, 
                    i.name AS inspector_name, 
                    c.zip_file_path,
                    a.city, 
                    a.state, 
                    a.zip, 
                    c.mfg_in 
                FROM certificates c 
                LEFT JOIN addresses a ON c.address_id = a.id 
                LEFT JOIN inspectors i ON c.inspector_id = i.id
                WHERE c.cert_number = '$cert_number'
                ";
        return $this->selectAll($sql);
    }
    public function obtenerCertificado($cert_number)
    {
        $sql = "SELECT 
                    c.cert_number, 
                    c.vin,
                    c.make, 
                    c.model,
                    c.owner_name, 
                    i.name AS inspector_name, 
                    c.zip_file_path,
                    a.city, 
                    a.state, 
                    a.zip, 
                    c.mfg_in,
                    c.year,
                    r.id,                  
                    r.field_name           
                FROM certificates c 
                LEFT JOIN addresses a ON c.address_id = a.id 
                LEFT JOIN inspectors i ON c.inspector_id = i.id
                LEFT JOIN correction_requests r ON c.cert_number = r.certificate_id
                WHERE c.cert_number = '$cert_number' AND r.status = 'pending'
                LIMIT 1";

        return $this->select($sql);
    }
    public function obtenerCertificadoPorError($id)
    {
        $sql = "SELECT 
            c.cert_number, 
            c.vin,
            c.make, 
            c.model,
            c.year,
            c.owner_name, 
            i.name AS inspector_name, 
            c.zip_file_path,
            a.city, 
            a.state, 
            a.zip, 
            a.number,
            a.street,
            c.address_id,
            c.mfg_in,
            r.id,
            r.field_name,
            r.proposed_value
        FROM correction_requests r
        LEFT JOIN certificates c ON r.certificate_id = c.cert_number
        LEFT JOIN addresses a ON c.address_id = a.id
        LEFT JOIN inspectors i ON c.inspector_id = i.id
        WHERE r.id = '$id'
        LIMIT 1";
        return $this->select($sql);
    }



    public function obtenerDatosCertificado($cert_number)
    {
        $sql = "SELECT 
                    c.cert_number,
                    c.vin,
                    c.address_id,
                    c.phone,
                    c.year,
                    c.mfg_in,
                    c.make,
                    c.owner_name,
                    c.model,
                    c.license_plate,
                    c.odometer,
                    c.inspector_id,
                    c.test_date,
                    c.expires,
                    c.source_file,
                    c.zip_file_path,
                    c.created_at,
                    c.updated_at,
                    a.number AS address_number,
                    a.street,
                    a.city,
                    a.state,
                    a.zip,
                    i.name AS inspector_name
                FROM certificates c
                LEFT JOIN addresses a ON c.address_id = a.id
                LEFT JOIN inspectors i ON c.inspector_id = i.id
                WHERE c.cert_number = '$cert_number'";
        return $this->select($sql);
    }

    public function obtenerMonitoreos($cert_number)
    {
        $sql = "SELECT monitor_type, result FROM monitoring_results WHERE cert_number = '$cert_number'";
        return $this->selectAll($sql);
    }


    public function obtenerValorActualCampo($cert_number, $campo)
    {
        $sql = "SELECT `$campo` FROM certificates WHERE cert_number = '$cert_number'";
        $resultado = $this->select($sql);
        if (!$resultado) {
            return false;
        }
        return $resultado[$campo] ?? null;
    }

    public function actualizarCampoCertificado($cert_number, $campo, $nuevoValor)
    {
        $sql = "UPDATE certificates 
                SET `$campo` = '$nuevoValor', updated_at = NOW() 
                WHERE cert_number = '$cert_number'";
        return $this->save($sql, []);
    }

    public function insertarLogCorreccion($correction_id, $cert_number, $field_name, $old_value, $new_value, $user_id)
    {
        $sql = "INSERT INTO correction_logs 
                (correction_request_id, certificate_id, field_name, old_value, new_value, corrected_by)
                VALUES (
                    '$correction_id',
                    '$cert_number',
                    '$field_name',
                    " . ($old_value !== null ? "'$old_value'" : "NULL") . ",
                    '$new_value',
                    '$user_id')";
        return $this->insertar($sql, []);
    }
    public function eliminarMonitoreo($cert_number, $monitor_type)
    {
        $sql = "DELETE FROM monitoring_results WHERE cert_number = ? AND monitor_type = ?";
        return $this->save($sql, [$cert_number, $monitor_type]);
    }
    public function actualizarEstadoSolicitud($correction_id, $user_id)
    {
        $sql = "UPDATE correction_requests 
                SET status = 'corrected', reviewed_by = '$user_id', 
                    reviewed_at = NOW(), updated_at = NOW()
                WHERE id = '$correction_id'";
        return $this->save($sql, []);
    }
    public function insertarInspector($nombre)
    {
        // Verificar si ya existe
        $sqlCheck = "SELECT id FROM inspectors WHERE name = '$nombre' LIMIT 1";
        $existe = $this->select($sqlCheck);

        if ($existe) {
            return $existe['id'];
        }

        $sqlInsert = "INSERT INTO inspectors (name) VALUES ('$nombre')";
        return $this->insertar($sqlInsert, []);
    }

    public function obtenerInspectorPorId($id)
    {
        $sql = "SELECT * FROM inspectors WHERE id = '$id' LIMIT 1";
        return $this->select($sql);
    }

    public function obtenerDireccionPorId($id)
    {
        $sql = "SELECT `number`, `street`, `city`, `state`, `zip` FROM addresses WHERE id = {$id}";
        return $this->select($sql);
    }
    public function obtenerResultadosMonitoreo($cert_number)
    {
        $sql = "SELECT monitor_type, result FROM monitoring_results WHERE cert_number = '$cert_number'";
        return $this->selectAll($sql);
    }
    public function actualizarDireccionCertificado($cert_number, $address_id)
    {
        $sql = "UPDATE certificates 
            SET address_id = ?, updated_at = NOW() 
            WHERE cert_number = ?";
        return $this->save($sql, [$address_id, $cert_number]);
    }

    public function eliminar($id_error)
    {
        $sql = "UPDATE correction_requests SET status = ? WHERE id = ?";
        $array = array('rejected', $id_error);
        return $this->save($sql, $array);
    }
}
