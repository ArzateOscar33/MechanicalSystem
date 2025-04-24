<?php
class Admin extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }
    public function index()
    {
        if (!empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin/home');
            exit;
        }
        $data['title'] = 'Acceso al sistema';
        $this->views->getView('admin', "login", $data);
    }
    public function validar()
    {
        if (isset($_POST['email']) && isset($_POST['clave'])) {
            if (empty($_POST['email']) || empty($_POST['clave'])) {
                $respuesta = array('msg' => 'todo los campos son requeridos', 'icono' => 'warning');
            } else {
                $data = $this->model->getUsuario($_POST['email']);
                if (empty($data)) {
                    $respuesta = array('msg' => 'el correo no existe', 'icono' => 'warning');
                } else {
                    if (password_verify($_POST['clave'], $data['clave'])) {
                        $_SESSION['email'] = $data['correo'];
                        $_SESSION['nombre_usuario'] = $data['first_name'];
                        $_SESSION['apellido_usuario'] = $data['last_name'];
                        $_SESSION['id_usuario'] = $data['id'];
                        $_SESSION['rol_usuario'] = $this->model->getRolUsuario($data['id']);
                        $respuesta = array('msg' => 'datos correcto', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'contraseña incorrecta', 'icono' => 'warning');
                    }
                }
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        die();
    }
    private function verificarRol($rolPermitido)
    {
        if ($_SESSION['rol_usuario'] != $rolPermitido && $_SESSION['rol_usuario'] != 1) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
    }
    public function home()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
        $data['title'] = 'Panel Administrativo';
        $data['certificadosTotales'] = $this->model->certificadosTotales();
        $data['ciudadesTotales'] = $this->model->ciudadesTotales();
        
        $data['estadosTotales'] = $this->model->estadosTotales();
        $data['fechasPorMes'] = $this->model->certificadosPorMes();
        
        $data['inspector_name'] = $this->model->inspectoresTotales();
        $data['certificadosPorInspector'] = $this->model->certificadosPorInspector();
        
        $this->views->getView('admin/administracion', "index", $data);
    }

    public function ciudadesCertificados()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
        $data = $this->model->ciudadesCertificados();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();

    }
    public function estadosCertificados()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
        $data = $this->model->estadosCertificados();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();

    }

    public function certificadosPorMes()
{
    if (empty($_SESSION['nombre_usuario'])) {
        header('Location: '. BASE_URL . 'admin');
        exit;
    }
    $data = $this->model->certificadosPorMes();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}


public function certificadosPorInspector()
{
    if (empty($_SESSION['nombre_usuario'])) {
        header('Location: '. BASE_URL . 'admin');
        exit;
    }
    $data = $this->model->certificadosPorInspector();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();


}

public function certificadosPorDia()
{
    if (empty($_SESSION['nombre_usuario'])) {
        header('Location: '. BASE_URL . 'admin');
        exit;
    }

    $anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');
    $mes = isset($_POST['mes']) ? $_POST['mes'] : date('m');
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
    $ciudad = isset($_POST['ciudad']) ? $_POST['ciudad'] : '';

    $data = $this->model->certificadosPorDia($anio, $mes, $estado, $ciudad);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}

public function getEstados()
{
    $data = $this->model->estadosCertificados();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}

public function getCiudades()
{
    $data = $this->model->ciudadesCertificados();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}
    public function salir()
    {
        session_destroy();
        header('Location: ' . BASE_URL);
    }
}
