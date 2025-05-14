<?php
class UsuariosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUsuarios($estado)
    {
        $sql = "SELECT u.id, u.username, u.first_name, u.last_name, u.correo, u.phone, r.name AS rol,
                       CONCAT(a.number,' ',a.street,' ',a.city,' ',a.state,' ',a.zip) AS address_user
                FROM users u
                LEFT JOIN addresses a ON a.id = u.address_id
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE u.active = ?";
        return $this->selectAll($sql, [$estado]);
    }

    public function registrar($username, $nombre, $apellido, $correo, $hash, $phone, $role_id, $address_id)
    {
        // Insertar en tabla users
        $sql = "INSERT INTO users (username, first_name, last_name, correo, clave, phone, address_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $array = [$username, $nombre, $apellido, $correo, $hash, $phone, $address_id];
        $userId = $this->insertar($sql, $array);

        // Si se insertó correctamente
        if ($userId > 0) {
            $sql_rol = "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)";
            $array_rol = [$userId, $role_id];
            $this->insertar($sql_rol, $array_rol);
            return $userId;
        } else {
            return 0;
        }
    }

    public function verificarCorreo($correo)
    {
        $sql = "SELECT correo FROM users WHERE correo = ? AND active = 1";
        return $this->select($sql, [$correo]);
    }

    public function eliminar($idUser)
    {
        $sql = "UPDATE users SET active = ? WHERE id = ?";
        return $this->save($sql, [0, $idUser]);
    }

    public function getUsuario($idUser)
    {
        $sql = "SELECT u.id, u.username, u.first_name, u.last_name, u.correo, u.phone, ur.role_id, u.address_id,
                       CONCAT(a.number,' ',a.street,' ',a.city,' ',a.state,' ',a.zip) AS address_user
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                RIGHT JOIN addresses a ON a.id = u.address_id
                WHERE u.id = ?";
        return $this->select($sql, [$idUser]);
    }

    public function modificar($username, $nombre, $apellido, $correo, $phone, $role_id, $address_id, $id)
    {
        // Actualizar datos del usuario
        $sql = "UPDATE users SET username = ?, first_name = ?, last_name = ?, correo = ?, phone = ?, address_id = ?
                WHERE id = ?";
        $result = $this->save($sql, [$username, $nombre, $apellido, $correo, $phone, $address_id, $id]);

        // Actualizar rol
        $sql_rol = "UPDATE user_roles SET role_id = ? WHERE user_id = ?";
        $this->save($sql_rol, [$role_id, $id]);

        return $result;
    }

    public function getDirecciones()
    {
        $sql = "SELECT id, CONCAT(number, ' ', street, ', ', city, ', ', state, ', ', zip) AS full_address FROM addresses";
        return $this->selectAll($sql);
    }
}
