<?php
class DireccionesModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerDirecciones()
    {
        $sql = "SELECT id, number, street, city, state, zip, latitude, longitude FROM addresses";
        return $this->selectAll($sql);
    }

    public function obtenerDireccion($id)
    {
        $sql = "SELECT id, number, street, city, state, zip, latitude, longitude 
                FROM addresses 
                WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    public function modificar($number, $street, $city, $state, $zip, $latitude, $longitude, $id)
    {
        $sql = "UPDATE addresses 
                SET number = ?, street = ?, city = ?, state = ?, zip = ?, latitude = ?, longitude = ? 
                WHERE id = ?";
        return $this->save($sql, [$number, $street, $city, $state, $zip, $latitude, $longitude, $id]);
    }
}
