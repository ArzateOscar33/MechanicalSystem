<?php
class ErroresUsuarioModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener los certificados del día del usuario actual (solo los creados por él)
public function getCertificados($userId)
{
    $sql = "SELECT cert_number 
            FROM certificates 
            WHERE created_by = ?
              AND DATE(created_at) = CURDATE()
            ORDER BY created_at DESC";
    return $this->selectAll($sql, [$userId]);
}


    // Verificar si el certificado pertenece al usuario y fue creado en los últimos 5 días
public function validarCertificadoUsuarioEnPlazo($certNumber, $userId)
{
    $sql = "SELECT * 
            FROM certificates 
            WHERE cert_number = ? 
              AND created_by = ?
              AND created_at >= DATE_SUB(NOW(), INTERVAL 5 DAY)";
    return $this->select($sql, [$certNumber, $userId]);
}


    // Obtener el valor actual del campo indicado para ese certificado
public function getValorActualCampo($certNumber, $campo)
{
    $campo = preg_replace('/[^a-zA-Z0-9_]/', '', $campo);
    $sql = "SELECT `$campo` FROM certificates WHERE cert_number = ?";
    $res = $this->select($sql, [$certNumber]);

    if ($res && array_key_exists($campo, $res)) {
        return $res[$campo];
    }

    return null;
}


    // Guardar solicitud de corrección
    public function registrarCorreccion($certificate_id, $user_id, $field_name, $current_value, $proposed_value, $reason, $timely)
    {
        $sql = "INSERT INTO correction_requests 
                (certificate_id, user_id, field_name, current_value, proposed_value, reason, status, timely, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW())";
        $datos = [$certificate_id, $user_id, $field_name, $current_value, $proposed_value, $reason, $timely];
        return $this->insertar($sql, $datos);
    }

    // Verificar si el certificado pertenece al usuario (sin importar la fecha)
public function validarCertificadoPorUsuario($certNumber, $userId)
{
    $sql = "SELECT * 
            FROM certificates 
            WHERE cert_number = ? 
              AND created_by = ?";
    return $this->select($sql, [$certNumber, $userId]);
}


    // Obtener todos los campos corregibles desde la tabla correction_fields
    public function getCamposPermitidos()
    {
        $sql = "SELECT * FROM correction_fields";
        return $this->selectAll($sql);
    }

    // Obtener lista de inspectores
    public function getInspectores()
    {
        $sql = "SELECT id, name FROM inspectors ORDER BY name ASC";
        return $this->selectAll($sql);
    }

    // Obtener lista de direcciones
    public function getDirecciones()
    {
        $sql = "SELECT id, CONCAT(`number`, ' ', street, ', ', city, ', ', state, ' ', zip) AS direccion
                FROM addresses
                ORDER BY city, state";
        return $this->selectAll($sql);
    }
}
