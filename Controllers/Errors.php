<?php
class Errors extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
            $data['title'] = 'Error';
    $data['mensaje'] = $_GET['msg'] ?? 'Ha ocurrido un error inesperado.';
   
        $this->views->getView('errors', "index");
    }
}