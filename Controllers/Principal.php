<?php


class Principal extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $data['title'] = 'Pagina de Inicio';
        $this->views->getView('principal', 'index', $data);
    }

    public function home()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }

        $data['title'] = 'Bienvenido a Mechanical Emission Services';
        $this->views->getView('principal', 'index', $data);
    }
}
