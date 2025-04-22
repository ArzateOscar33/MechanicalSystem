<?php 
class Importaciones extends Controller
{  
    public function __construct() {
        parent::__construct();
        session_start(); 
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