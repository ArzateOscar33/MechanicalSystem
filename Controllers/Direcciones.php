<?php 
class Direcciones extends Controller
{  
    public function __construct() {
        parent::__construct();
        session_start(); 
        $this->validarSesionInactividad();
        $this->validarSesionUnica();
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }             // Validar que sea administrador (rol_id = 1)
        if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] != 1) {
            header('Location: ' . BASE_URL . 'admin');   
            exit;
        }
    }


    public function index()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }

        $data['title'] = 'Control de Direcciones';

        $this->views->getView('admin/Direcciones', "index", $data);
    }
    public function listar()
    {
        $data = $this->model->obtenerDirecciones();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex"> 
             <button class="btn btn-primary" type="button" onclick="editarDireccion(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }
    public function edit($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->obtenerDireccion($id);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function actualizar()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = intval($_POST['id']);
        $number = trim($_POST['number']);
        $street = trim($_POST['street']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $zip = trim($_POST['zip']);

        if (empty($id) || empty($number) ||  empty($street) || empty($city) || empty($state) || empty($zip)) {
            $res = ['msg' => 'Todos los campos son obligatorios', 'icono' => 'warning'];
        } else {
            $result = $this->model->modificar($number,$street, $city, $state, $zip, $id);
            if ($result) {
                $res = ['msg' => 'Dirección actualizada correctamente', 'icono' => 'success'];
            } else {
                $res = ['msg' => 'Error al actualizar la dirección', 'icono' => 'error'];
            }
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    die();
}



 
}
?>