<?php
class EmpleadosModel extends Query {

    public function __construct()
    {
        parent::__construct();
    }

    // Obtener todos los departamentos
    public function getDepartamentos() {
        $sql = "SELECT * FROM departments ORDER BY name ASC";
        return $this->selectAll($sql);
    }

    // Obtener todos los puestos de un departamento
    public function getPuestosPorDepartamento($idDepartamento) {
        $sql = "SELECT * FROM positions WHERE department_id = $idDepartamento";
        return $this->selectAll($sql);
    }

    // Contar cuántos empleados han sido registrados hoy
    public function contarEmpleadosHoy($fecha) {
        $sql = "SELECT COUNT(*) AS total FROM employees WHERE DATE(issue_date) = $fecha";
        $data = $this->select($sql);
        return $data['total'];
    }

    // Insertar nuevo empleado
    public function registrarEmpleado($data) {
        $sql = "INSERT INTO employees (
            first_name, last_name, second_last_name, curp, rfc, employee_number, phone, email, 
            birth_date, gender, photo_path, department_id, position_id, issue_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->insertar($sql, $data);
    }

    // Obtener empleado por ID (para edición futura)
    public function getEmpleado($id) {
        $sql = "SELECT * FROM employees WHERE id = $id";
        return $this->select($sql);
    }
    public function getEmpleados() {
        $sql = "SELECT e.id, e.employee_number, 
                       CONCAT(e.first_name, ' ', e.last_name, ' ', e.second_last_name) AS nombre_completo,
                       e.curp, e.rfc, e.phone, e.email, e.birth_date, e.gender,
                       d.name AS departamento, p.name AS puesto, e.issue_date
                FROM employees e
                INNER JOIN departments d ON e.department_id = d.id
                INNER JOIN positions p ON e.position_id = p.id
                ORDER BY e.id DESC";
        return $this->selectAll($sql);
    }
    

    public function eliminarEmpleado($id) {
        $sql = "DELETE FROM employees WHERE id = ?";
        return $this->save($sql, [$id]);
    }

    // Modificar empleado 
    public function modificarEmpleado($data) {
        $sql = "UPDATE employees SET 
            first_name = ?, last_name = ?, second_last_name = ?, curp = ?, rfc = ?, phone = ?, email = ?, 
            birth_date = ?, gender = ?, photo_path = ?, department_id = ?, position_id = ?
            WHERE id = ?";
        return $this->save($sql, $data);
    }

    public function getEmpleadosPorDepartamento($id)
{
    $sql = "SELECT id, CONCAT(first_name, ' ', last_name, ' ', second_last_name) AS nombre_completo, employee_number 
            FROM employees 
            WHERE department_id = $id";
    return $this->selectAll($sql);
}
public function buscarEmpleadoPorNumero($numero)
{
    $sql = "SELECT * FROM employees WHERE employee_number = '$numero'";
    return $this->select($sql);
}
 

}
