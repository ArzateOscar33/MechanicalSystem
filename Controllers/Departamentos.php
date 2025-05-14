<?php
class Departamentos extends Controller {
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

    public function listar()
    {
        $data = $this->model->getDepartamentos();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex gap-2"> 
            <button class="btn btn-primary" type="button" onclick="editarDepartamento(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
            <button class="btn btn-danger" type="button" onclick="eliminarDepartamento(' . $data[$i]['id'] . ')"><i class="fas fa-trash-alt"></i></button>
        </div>';
        
        }
        echo json_encode($data);
        die();
    }

    public function index() { 
        $data['title']='Control de Departamentos';
        $this->views->getView('admin/departamentos', 'index', $data);
    }

    public function registrar() {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);

        if (empty($nombre)) {
            echo json_encode(['msg' => 'El nombre es obligatorio', 'icono' => 'warning']);
            return;
        }

        $res = $this->model->registrar($nombre, $descripcion);
        echo json_encode($res ? ['msg' => 'Departamento registrado', 'icono' => 'success'] : ['msg' => 'Error al registrar', 'icono' => 'error']);
    }

    public function actualizar()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = intval($_POST['id']);
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);

        if (empty($id) || empty($nombre)) {
            $res = ['msg' => 'El nombre es obligatorio', 'icono' => 'warning'];
        } else {
            $result = $this->model->modificar($nombre, $descripcion, $id);
            $res = $result
                ? ['msg' => 'Departamento actualizado correctamente', 'icono' => 'success']
                : ['msg' => 'Error al actualizar', 'icono' => 'error'];
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    die();
}
public function editar($id)
{
    if (is_numeric($id)) {
        $data = $this->model->getDepartamento($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    die();
}
public function eliminar($id)
{
    if (is_numeric($id)) {
        $res = $this->model->eliminar($id);
        $respuesta = $res
            ? ['msg' => 'Departamento eliminado correctamente', 'icono' => 'success']
            : ['msg' => 'Error al eliminar', 'icono' => 'error'];
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    }
    die();
}


}
?>