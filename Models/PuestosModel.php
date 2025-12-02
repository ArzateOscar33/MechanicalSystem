<?php
class PuestosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Listar todos los departamentos
     * (para llenar el select en el modal de puestos)
     */
    public function getDepartamentos()
    {
        $sql = "SELECT id, name, description FROM departments ORDER BY name ASC";
        return $this->selectAll($sql);
    }

    /**
     * Listar todos los puestos con su departamento
     * (ideal para el DataTable)
     */
    public function getPuestos()
    {
        $sql = "SELECT p.id, p.name, p.description, d.name AS departamento
                FROM positions p
                INNER JOIN departments d ON p.department_id = d.id
                ORDER BY p.id DESC";
        return $this->selectAll($sql);
    }

    /**
     * Registrar un nuevo puesto
     */
    public function registrar($name, $description, $department_id)
    {
        $sql = "INSERT INTO positions (name, description, department_id) VALUES (?, ?, ?)";
        $datos = [$name, $description, $department_id];
        return $this->insertar($sql, $datos);
    }

    /**
     * Obtener un puesto por id (para editar)
     */
    public function getPuesto($id)
    {
        $sql = "SELECT * FROM positions WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    /**
     * Modificar un puesto existente
     */
    public function modificar($name, $description, $department_id, $id)
    {
        $sql = "UPDATE positions 
                SET name = ?, description = ?, department_id = ?
                WHERE id = ?";
        $datos = [$name, $description, $department_id, $id];
        return $this->save($sql, $datos);
    }

    /**
     * Eliminar un puesto
     * OJO: por la FK con employees (ON DELETE CASCADE),
     * si hay empleados ligados a este puesto, también se borrarán.
     */
    public function eliminar($id)
    {
        $sql = "DELETE FROM positions WHERE id = ?";
        return $this->save($sql, [$id]);
    }
}
?>
