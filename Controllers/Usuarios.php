<?php
class Usuarios extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->validarSesionInactividad();
        $this->validarSesionUnica();
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
            // Validar que sea administrador (rol_id = 1)
    if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] != 1) {
        header('Location: ' . BASE_URL . 'admin');   
        exit;
    }
    }
    public function index()
    {
        $data['title'] = 'usuarios';
        $this->views->getView('admin/usuarios', "index", $data);
    }
    public function listar()
    {
        $data = $this->model->getUsuarios(1);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex">
            <button class="btn btn-primary" type="button" onclick="editUser(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
            <button class="btn btn-danger" type="button" onclick="eliminarUser(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }
    public function registrar()
    {
        if (isset($_POST['nombre'])) {
            $username = $_POST['username'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $correo = $_POST['correo'];
            $clave = $_POST['clave'];
            $phone = $_POST['phone'];
            $role_id = $_POST['rol']; 
            $address_id = $_POST['address_id'];
            $id = $_POST['id'];
    
            if (empty($nombre) || empty($apellido) || empty($role_id)) {
                $respuesta = array('msg' => 'Todos los campos son requeridos', 'icono' => 'warning');
            } else {
                if (empty($id)) {
                    $result = $this->model->verificarCorreo($correo);
                    if (empty($result)) {
                        $hash = password_hash($clave, PASSWORD_DEFAULT);
                        $data = $this->model->registrar($username, $nombre, $apellido, $correo, $hash, $phone, $role_id,$address_id);
                        if ($data > 0) {
                            $respuesta = array('msg' => 'Usuario registrado', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'Error al registrar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'Correo ya existe', 'icono' => 'warning');
                    }
                } else {
                    // Puedes agregar aquí la lógica para modificar el rol también si lo deseas
                    $data = $this->model->modificar($username, $nombre, $apellido, $correo, $phone, $role_id, $address_id, $id);
                    if ($data == 1) {
                        $respuesta = array('msg' => 'Usuario modificado', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'Error al modificar', 'icono' => 'error');
                    }
                }
            }
            echo json_encode($respuesta);
        }
        die();
    }
    
    //eliminar user
    public function delete($idUser)
    {
        if (is_numeric($idUser)) {
            $data = $this->model->eliminar($idUser);
            if ($data == 1) {
                $respuesta = array('msg' => 'usuario dado de baja', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al eliminar', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    //editar user
    public function edit($idUser)
    {
        if (is_numeric($idUser)) {
            $data = $this->model->getUsuario($idUser);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function obtenerDirecciones()
{
    $data = $this->model->getDirecciones();
    echo json_encode($data);
    die();
}
}
