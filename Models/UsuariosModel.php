<?php
class UsuariosModel extends Query{
 
    public function __construct()
    {
        parent::__construct();
    }
    public function getUsuarios($estado)
    {
        $sql = "SELECT id,username,first_name,last_name,correo,phone FROM users WHERE active = $estado";
        return $this->selectAll($sql);
    }
    public function registrar($username,$nombre, $apellido, $correo, $hash,$phone)
    {
        $sql = "INSERT INTO users (username,first_name,last_name,correo,clave,phone) VALUES (?,?,?,?,?,?)";
        $array = array($username,$nombre, $apellido, $correo, $hash,$phone);
        return $this->insertar($sql, $array);
    }
   public function verificarCorreo($correo)
    {
        $sql = "SELECT correo FROM users WHERE correo = '$correo' AND active = 1";
        return $this->select($sql);
    }

    public function eliminar($idUser)
    {
        $sql = "UPDATE users SET active = ? WHERE id = ?";
        $array = array(0, $idUser);
        return $this->save($sql, $array);
    }

    public function getUsuario($idUser)
    {
        $sql = "SELECT id,username,first_name,last_name,correo,phone FROM users WHERE id = $idUser";
        return $this->select($sql);
    }

    public function modificar($username,$nombre, $apellido, $correo, $id)
    {
        $sql = "UPDATE users SET username=? first_name=?, last_name=?, correo=? WHERE id = ?";
        $array = array($username,$nombre, $apellido, $correo, $id);
        return $this->save($sql, $array);
    }
}
 
?>