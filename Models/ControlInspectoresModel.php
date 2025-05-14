<?php
class ControlInspectoresModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerInspectores()
    {
        $sql = "SELECT 
                    id,
                    name,
                    direccion_firma AS firma
                FROM inspectors
                ORDER BY name ASC";
        return $this->selectAll($sql);
    }

    public function getInspector($id)
    {
        $sql = "SELECT 
                    id,
                    name,
                    direccion_firma AS firma
                FROM inspectors
                WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    public function modificar($name, $firma, $id)
    {
        $sql = "UPDATE inspectors SET name = ?, direccion_firma = ? WHERE id = ?";
        return $this->save($sql, [$name, $firma, $id]);
    }

    public function registrarInspector($nombre, $firma_ruta)
    {
        $sql = "INSERT INTO inspectors (name, direccion_firma) VALUES (?, ?)";
        return $this->insertar($sql, [$nombre, $firma_ruta]);
    }

    public function registrar($name, $firma)
    {
        $sql = "INSERT INTO inspectors (name, direccion_firma) VALUES (?, ?)";
        return $this->insertar($sql, [$name, $firma]);
    }
}
