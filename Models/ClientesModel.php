<?php
class ClientesModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getcustomers()
    {
        $sql = "SELECT id_customer AS id, nombre_cliente 
                FROM customers 
                WHERE estatus = 1
                ORDER BY id_customer DESC";
        return $this->selectAll($sql);
    }

    public function registrar($nombre_cliente)
    {
        $sql = "INSERT INTO customers (nombre_cliente) VALUES (?)";
        $array = [$nombre_cliente];
        return $this->insertar($sql, $array);
    }

    public function verificarCliente($nombre_cliente)
    {
        $sql = "SELECT id_customer AS id, nombre_cliente 
                FROM customers 
                WHERE nombre_cliente = ?";
        return $this->select($sql, [$nombre_cliente]);
    }

    public function getCliente($idCliente)
    {
        $sql = "SELECT id_customer AS id, nombre_cliente 
                FROM customers 
                WHERE id_customer = ?";
        return $this->select($sql, [$idCliente]);
    }

    public function modificar($nombre_cliente, $idCliente)
    {
        $sql = "UPDATE customers 
                SET nombre_cliente = ?
                WHERE id_customer = ?";
        $array = [$nombre_cliente, $idCliente];
        return $this->save($sql, $array);
    }

    public function eliminar($idCliente)
    {
        $sql = "UPDATE customers SET estatus = ? WHERE id_customer = ?";
        return $this->save($sql, [0, $idCliente]);
    }
}
