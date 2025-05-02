<?php
class ErroresAdminModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getErrores($estado)
    {
        $sql = "SELECT c.id,
        c.certificate_id,
        CONCAT(u.first_name, ' ', u.last_name) AS user_name,
        c.field_name,
        c.current_value,
        c.proposed_value,
        c.reason,
        c.status,
        c.created_at
        
                FROM  correction_requests c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.status = 'pending' 
                ORDER BY created_at ASC";
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
                WHERE c.status = 'corrected'
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

    
}
