<?php
class DepartamentosModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getDepartamentos() {
        return $this->selectAll("SELECT * FROM departments ORDER BY name ASC");
    }

    public function registrar($name, $description) {
        $sql = "INSERT INTO departments (name, description) VALUES (?, ?)";
        return $this->insertar($sql, [$name, $description]);
    }
    
    public function getDepartamento($id) {
        $sql = "SELECT * FROM departments WHERE id = $id";
        return $this->select($sql);
    }
    
    public function modificar($name, $description, $id) {
        $sql = "UPDATE departments SET name = ?, description = ? WHERE id = ?";
        return $this->save($sql, [$name, $description, $id]);
    }
    
    public function eliminar($id) {
        $sql = "DELETE FROM departments WHERE id = ?";
        return $this->save($sql, [$id]);
    }
    
 
 
}
 
?>