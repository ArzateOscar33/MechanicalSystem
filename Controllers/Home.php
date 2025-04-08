<?php
class Home extends Controller
{
    public function __construct() {
        parent::__construct();
        session_start();
    }
    public function index()
    {
        $data['title'] = 'Pagina de Inicio';
        $this->views->getView('admin', "login", $data);
    }
    public function home()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
        $data['title'] = 'Panel Administrativo'; 
        $this->views->getView('admin/administracion', "index", $data);
    }
    
}
?>