<?php
class ControlInspectoresModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener todos los certificados con la información de direcciones
    public function obtenerInspectores()
    {
        $sql = "SELECT 
                    id,
                    name,
                    direccion_firma as firma
                    from inspectors
                ORDER BY name ASC";
        return $this->selectAll($sql);
    }
    public function getInspector($id)
    {
        $sql = "SELECT 
                    id,
                    name,
                    direccion_firma as firma
                    
                    from inspectors
                    where id = $id ";
        return $this->select($sql, $id);
    }

  
 

    public function modificar($name, $firma, $id)
    {
        $sql = "UPDATE inspectors SET name=?, direccion_firma=? WHERE id=?";
        $array = array($name, $firma, $id);
        return $this->save($sql, $array);
    }

    public function registrarInspector($nombre, $firma_ruta)
{
    $sql = "INSERT INTO inspectors (name, direccion_firma) VALUES (?, ?)";
    $datos = [$nombre, $firma_ruta];
    return $this->insertar($sql, $datos); // Usa tu método genérico para INSERT
}
    public function registrar($name, $firma)
    {
        $sql = "INSERT INTO inspectors (name, direccion_firma) VALUES (?, ?)";
        $array = array($name, $firma);
        return $this->insertar($sql, $array);
    }
}
