<?php
class DescargarCertificados extends Controller
{

    public function __construct()
    {
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

        $data['title'] = 'Descarga de Certificados';

        $this->views->getView('admin/DescargarCertificados', "index", $data);
    }


    public function listar()
    {
        $data = $this->model->obtenerCertificados();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex"> 
            <button class="btn btn-danger" type="button" onclick="eliminarCertificado(\'' . $data[$i]['cert_number'] . '\')"><i class="fas fa-trash"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }
    //eliminar certificado y zip
    public function delete($cert_number)
    {
        if (!empty($cert_number)) {
            $certificado = $this->model->obtenerZipPath($cert_number);
    
            if ($certificado && !empty($certificado['zip_file_path'])) {
                $filePath = $certificado['zip_file_path'];
    
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
    
            $data = $this->model->eliminar($cert_number);
            if ($data == 1) {
                $respuesta = array('msg' => 'certificado eliminado', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al eliminar', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    
}
