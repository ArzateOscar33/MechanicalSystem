<?php
class ErroresAdminModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getErrores($estado)
    {
        $sql = "SELECT id,certificate_id,user_id,field_name,current_value,proposed_value,reason,created_at
        
                FROM  correction_requests
                WHERE status = '$estado' 
                ORDER BY created_at DESC";
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
}
