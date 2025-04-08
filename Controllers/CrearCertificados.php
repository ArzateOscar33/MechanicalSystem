<?php
class CrearCertificados extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
    }
    public function index()
    {
        $data['title'] = 'CrearCertificados';
        $this->views->getView('admin/CrearCertificados', "index", $data);
    }
   

   
}
