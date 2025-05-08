<?php
class BuscarCertificados extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->validarSesionInactividad();
        $this->validarSesionUnica(); 
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
                $rutaFisica = BASE_PATH . $data['zip_file_path'];
                $rutaWeb = BASE_URL . $data['zip_file_path'];
    
                if (file_exists($rutaFisica)) {
                    echo json_encode(['url' => $rutaWeb, 'icono' => 'success']);
                    exit;
                } else {
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
        $propietarios = $this->model->obtenerPropietariosUnicos();
        $inspectores = $this->model->obtenerInspectoresUnicos();
        $origenes = $this->model->obtenerOrigenesUnicos();
    
        echo json_encode([
            'ciudades' => $ciudades,
            'estados' => $estados,
            'propietarios' => $propietarios,
            'inspectores' => $inspectores,
            'origenes' => $origenes
        ]);
        die();
    }
    
public function verificarArchivo($cert_number)
{
    $data = $this->model->descargarCertificado($cert_number);

    if (!empty($data)) {
        $ruta = BASE_PATH . $data['zip_file_path'];
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