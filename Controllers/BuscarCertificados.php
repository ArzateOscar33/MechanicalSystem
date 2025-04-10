<?php
class BuscarCertificados extends Controller
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
        $data['title'] = 'BuscarCertificados';
        $data['certificados'] = $this->model->obtenerCertificados();
        $this->views->getView('admin/BuscarCertificados', "index", $data);
    }
    
    // Método para buscar certificados mediante AJAX
    public function buscar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $filtros = [
                'cert_number' => $_POST['cert_number'] ?? '',
                'city' => $_POST['city'] ?? '',
                'state' => $_POST['state'] ?? '',
                'make' => $_POST['make'] ?? '',
                'owner_name' => $_POST['owner_name'] ?? '',
                'inspector_name' => $_POST['inspector_name'] ?? ''
            ];
            
            // Si se implementa el método buscarCertificados en el modelo
            $certificados = $this->model->buscarCertificados($filtros);
            
            echo json_encode($certificados);
        } else {
            echo json_encode(['error' => 'Método no permitido']);
        }
        die();
    }
}