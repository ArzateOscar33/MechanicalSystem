<?php
class ErroresUsuarioModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener los certificados del día del usuario actual (solo los creados hoy por él)
    public function getCertificados($userId)
    {
        $hoy = date('Y-m-d');
        $sql = "SELECT cert_number 
                FROM certificates 
                WHERE   
                  cert_number LIKE 'MEX$userId-%'";
        return $this->selectAll($sql);
    }

    // Verificar si el certificado pertenece al usuario y fue creado hoy
    public function validarCertificadoUsuarioEnPlazo($certNumber, $userId)
    {
        $sql = "SELECT * 
                FROM certificates 
                WHERE cert_number = '$certNumber' 
                AND cert_number LIKE 'MEX$userId-%' 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 5 DAY)";
        return $this->select($sql);
    }

    // Obtener el valor actual del campo indicado para ese certificado
    public function getValorActualCampo($certNumber, $campo)
    {
        $campo = preg_replace('/[^a-zA-Z0-9_]/', '', $campo); // sanitizar
        $sql = "SELECT `$campo` FROM certificates WHERE cert_number = '$certNumber'";
        return $this->select($sql);
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
    public function validarCertificadoPorUsuario($certNumber, $userId)
    {
        $sql = "SELECT * 
                FROM certificates 
                WHERE cert_number = '$certNumber' 
                AND cert_number LIKE 'MEX$userId-%'";
        return $this->select($sql);
    }
    
    // Obtener todos los campos corregibles desde la tabla correction_fields
    public function getCamposPermitidos()
    {
        $sql = "SELECT * FROM correction_fields";
        return $this->selectAll($sql);
    }
    public function getInspectores()
{
    $sql = "SELECT id, name FROM inspectors ORDER BY name ASC";
    return $this->selectAll($sql);
}

}
