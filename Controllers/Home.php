<?php
include_once __DIR__ . '/../Models/AdminModel.php';  
class Home extends Controller
{ 
    private $adminModel;
    public function __construct() {
        parent::__construct();
        session_start();
        $this->adminModel = new AdminModel(); // Instanciar el modelo de Admin
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
        $data['certificadosTotales'] = $this->adminModel->certificadosTotales();
        $data['ciudadesTotales'] = $this->adminModel->ciudadesTotales();
        $data['estadosTotales'] = $this->adminModel->estadosTotales();
        $data['fechasPorMes'] = $this->adminModel->certificadosPorMes();
        
        $data['inspector_name'] = $this->adminModel->inspectoresTotales();

        $this->views->getView('admin/administracion', "index", $data);

 

}
}
?>