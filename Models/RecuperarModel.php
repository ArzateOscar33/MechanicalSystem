<?php
class RecuperarModel extends Query
{
    public function verificarCorreo($correo)
    {
        $sql = "SELECT * FROM users WHERE correo = ?";
        return $this->select($sql, [$correo]);
    }

    public function guardarToken($correo, $token, $expira)
    {
        $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
        return $this->save($sql, [$correo, $token, $expira]);
    }

    public function verificarToken($token)
    {
        $sql = "SELECT * FROM password_resets WHERE token = ? AND expires_at >= NOW()";
        return $this->select($sql, [$token]);
    }

    public function actualizarPassword($correo, $password)
    {
        $sql = "UPDATE users SET clave = ? WHERE correo = ?";
        return $this->save($sql, [$password, $correo]);
    }

    public function eliminarToken($token)
    {
        $sql = "DELETE FROM password_resets WHERE token = ?";
        return $this->save($sql, [$token]);
    }
}
?>