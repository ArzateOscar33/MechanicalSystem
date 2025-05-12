<?php
class DireccionesModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function obtenerDirecciones()
    {
        // Consulta para obtener la cantidad de certificados por ciudad
        $sql = "
          SELECT id,number,street,city,state,zip,latitude,longitude from addresses
        ";

        // Ejecutar la consulta y obtener los resultados
        $result = $this->selectAll($sql);
        return $result;
    }
    public function obtenerDireccion($id)
    {
        $sql = "SELECT id,number,street,city,state,zip,latitude,longitude from addresses WHERE id = $id";
        return $this->select($sql);
    }
    public function modificar($number,$street,$city,$state,$zip, $latitude, $longitude, $id)
    {
        $sql = "UPDATE addresses SET number=?,street=?, city=?, state=?, zip=?, latitude=?, longitude=?  WHERE id = ?";
        $array = array($number,$street,$city,$state,$zip, $latitude, $longitude, $id);
        return $this->save($sql, $array);
    }
     
}
 
