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
    
    public function listar()
    {
        $data = $this->model->obtenerCertificados();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex"> 
            <button class="btn btn-success" type="button" onclick="descargarCertificado(\'' . $data[$i]['cert_number'] . '\')"><i class="fas fa-download"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }
    //descargarCertificado
    public function descargar($cert_number)
    {
        if (!empty($cert_number)) {
            $data = $this->model->descargarCertificado($cert_number);
    
            if (!empty($data)) {
                $ruta = 'uploads/certificates/' . $data['zip_file_path'];
    
                if (file_exists($ruta)) {
                        // Encabezados para forzar descarga
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename="' . basename($ruta) . '"');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($ruta));
                        readfile($ruta);
                    exit;
                } else {
                    http_response_code(404);
                    echo json_encode(['msg' => 'Archivo no encontrado', 'icono' => 'error']);
                    exit;
                }
            } else {
                echo json_encode(['msg' => 'Certificado no encontrado', 'icono' => 'error']);
                exit;
            }
        } else {
            echo json_encode(['msg' => 'Solicitud inválida', 'icono' => 'error']);
            exit;
        }
    }
    
    public function obtenerFiltros()
{
    $ciudades = $this->model->obtenerCiudadesUnicas();
    $estados = $this->model->obtenerEstadosUnicos();

    echo json_encode([
        'ciudades' => $ciudades,
        'estados' => $estados
    ]);
    die();
}
public function verificarArchivo($cert_number)
{
    $data = $this->model->descargarCertificado($cert_number);

    if (!empty($data)) {
        $ruta = 'uploads/certificates/' . $data['zip_file_path'];
        if (file_exists($ruta)) {
            echo json_encode(['existe' => true]);
        } else {
            echo json_encode(['existe' => false, 'msg' => 'Archivo no encontrado']);
        }
    } else {
        echo json_encode(['existe' => false, 'msg' => 'Certificado no encontrado']);
    }
    die();
}

}