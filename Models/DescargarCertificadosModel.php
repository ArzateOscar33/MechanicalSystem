<?php
class DescargarCertificadosModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function obtenerCertificados()
    {
        $sql = "SELECT cert_number,vin,zip_file_path
                FROM certificates 
                ORDER BY cert_number ASC";
        return $this->selectAll($sql);
    }
 
    
 
 

    public function eliminar($cert_number)
    {
        $sql = "DELETE from certificates where cert_number= ?";
        $array = array($cert_number);
        return $this->save($sql, $array);
    }
    

}
 
?>