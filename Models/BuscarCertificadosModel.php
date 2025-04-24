<?php
class BuscarCertificadosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    
    // Obtener todos los certificados con la información de direcciones
    public function obtenerCertificados()
    {
        $sql = "SELECT c.cert_number, c.vin,c.make, c.owner_name, c.inspector_name, c.zip_file_path,
                       a.city, a.state, a.zip, c.mfg_in 
                FROM certificates c 
                LEFT JOIN addresses a ON c.address_id = a.id 
                ORDER BY c.cert_number ASC";
        return $this->selectAll($sql);
    }
    
 
    public function eliminar($cert_number)
    {
        $sql = "DELETE from certificates where cert_number= ?";
        $array = array($cert_number);
        return $this->save($sql, $array);
    }

    public function obtenerCiudadesUnicas()
{
    $sql = "SELECT DISTINCT city FROM addresses WHERE city IS NOT NULL AND city != '' ORDER BY city ASC";
    return $this->selectAll($sql);
}

public function obtenerEstadosUnicos()
{
    $sql = "SELECT DISTINCT state FROM addresses WHERE state IS NOT NULL AND state != '' ORDER BY state ASC";
    return $this->selectAll($sql);
}
public function descargarCertificado($cert_number)
{
    $sql = "SELECT zip_file_path from certificates where cert_number='$cert_number'";
    return $this->select($sql);
}
public function obtenerPropietariosUnicos()
{
    $sql = "SELECT DISTINCT owner_name FROM certificates WHERE owner_name IS NOT NULL AND owner_name != '' ORDER BY owner_name ASC";
    return $this->selectAll($sql);
}

public function obtenerInspectoresUnicos()
{
    $sql = "SELECT DISTINCT inspector_name FROM certificates WHERE inspector_name IS NOT NULL AND inspector_name != '' ORDER BY inspector_name ASC";
    return $this->selectAll($sql);
}

public function obtenerOrigenesUnicos()
{
    $sql = "SELECT DISTINCT mfg_in FROM certificates WHERE mfg_in IS NOT NULL AND mfg_in != '' ORDER BY mfg_in ASC";
    return $this->selectAll($sql);
}
}
?>