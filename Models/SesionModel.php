<?php
class SesionModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function verificarToken($userId)
    {
        $sql = "SELECT session_token FROM users WHERE id = $userId";
        return $this->select($sql);
    }

    public function guardarToken($userId, $token)
    {
        $sql = "UPDATE users SET session_token = '$token' WHERE id = $userId";
        return $this->save($sql, []);
    }

    public function limpiarToken($userId)
    {
        $sql = "UPDATE users SET session_token = NULL WHERE id = $userId";
        return $this->save($sql, []);
    }
}
