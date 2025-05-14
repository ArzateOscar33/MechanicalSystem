<?php
class DepartamentosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDepartamentos()
    {
        $sql = "SELECT * FROM departments ORDER BY name ASC";
        return $this->selectAll($sql);
    }

    public function registrar($name, $description)
    {
        $sql = "INSERT INTO departments (name, description) VALUES (?, ?)";
        return $this->insertar($sql, [$name, $description]);
    }

    public function getDepartamento($id)
    {
        $sql = "SELECT * FROM departments WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    public function modificar($name, $description, $id)
    {
        $sql = "UPDATE departments SET name = ?, description = ? WHERE id = ?";
        return $this->save($sql, [$name, $description, $id]);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM departments WHERE id = ?";
        return $this->save($sql, [$id]);
    }
}
?>
