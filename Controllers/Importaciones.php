<?php 
class Importaciones extends Controller
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

        $data['title'] = 'Registro de Importaciones';

        $this->views->getView('admin/Importaciones', "index", $data);
    }

    public function listar()
    {
        $data = $this->model->obtenerImportaciones();
        
        echo json_encode($data);
        die();
    }
}
?>