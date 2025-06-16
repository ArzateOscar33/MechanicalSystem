<?php
class Estadisticas extends Controller
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

        $data['title'] = 'Estadisticas';

        $this->views->getView('admin/Estadisticas', "index", $data);
    }

 
    public function home()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $data['title'] = 'Panel Administrativo';
        $data['certificadosTotales'] = $this->model->certificadosTotales();
        $data['ciudadesTotales'] = $this->model->ciudadesTotales();

        $data['estadosTotales'] = $this->model->estadosTotales();
        $data['fechasPorMes'] = $this->model->certificadosPorMes();

        $data['inspector_name'] = $this->model->inspectoresTotales();
        $data['certificadosPorInspector'] = $this->model->certificadosPorInspector();

        $this->views->getView('admin/estadisticas', "index", $data);
    }

    public function ciudadesCertificados()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $data = $this->model->ciudadesCertificados();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function estadosCertificados()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $data = $this->model->estadosCertificados();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function certificadosPorMes()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $data = $this->model->certificadosPorMes();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }


    public function certificadosPorInspector()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        $data = $this->model->certificadosPorInspector();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function certificadosPorDia()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
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
        session_start();
        require_once 'Models/SesionModel.php';
        $sesionModel = new SesionModel();

        if (isset($_SESSION['id_usuario'])) {
            $sesionModel->limpiarToken($_SESSION['id_usuario']);
        }

        session_unset(); // Limpia datos
        $_SESSION['msg_error'] = 'Has cerrado sesión correctamente.';
        header('Location: ' . BASE_URL . 'admin');
        exit;
    }


    public function inspectoresDisponibles()
    {
        $data = $this->model->inspectoresDisponibles();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function certificadosPorMesInspector()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }

        $anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');
        $inspector = isset($_POST['inspector']) ? $_POST['inspector'] : '';

        $data = $this->model->certificadosPorMesInspector($anio, $inspector);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function lugarInspector()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }

        $inspector = isset($_POST['inspector']) ? $_POST['inspector'] : '';
        $data = $this->model->lugarInspector($inspector);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function certificadosPorCiudadPorInspector()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }

        $inspector = isset($_POST['inspector']) ? $_POST['inspector'] : '';
        $data = $this->model->certificadosPorCiudadPorInspector($inspector);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function certificadosPorInspectorPorFecha()
{
    if (empty($_SESSION['nombre_usuario'])) {
        header('Location: ' . BASE_URL . 'admin');
        exit;
    }

    $fecha = $_POST['fecha'] ?? date('Y-m-d');
    $data = $this->model->certificadosPorInspectorPorFecha($fecha);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}
public function certificadosPorCiudadPorInspectorYFecha()
{
    if (empty($_SESSION['nombre_usuario'])) {
        header('Location: ' . BASE_URL . 'admin');
        exit;
    }

    $inspector = $_POST['inspector'] ?? '';
    $fecha = $_POST['fecha'] ?? '';

    $data = $this->model->certificadosPorCiudadPorInspectorYFecha($inspector, $fecha);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}
public function inspectoresPorRango()
{
    if (empty($_SESSION['nombre_usuario'])) {
        header('Location: ' . BASE_URL . 'admin');
        exit;
    }

    $desde = $_GET['desde'] ?? '';
    $hasta = $_GET['hasta'] ?? '';

    if (empty($desde) || empty($hasta)) {
        echo json_encode(['error' => 'Fechas inválidas'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $data = $this->model->inspectoresPorRango($desde, $hasta);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
public function certificadosPorCiudadPorInspectorYRango()
{
    if (empty($_SESSION['nombre_usuario'])) {
        header('Location: ' . BASE_URL . 'admin');
        exit;
    }

    $inspector = $_POST['inspector'] ?? '';
    $desde = $_POST['desde'] ?? '';
    $hasta = $_POST['hasta'] ?? '';

    $data = $this->model->certificadosPorCiudadPorInspectorYRango($inspector, $desde, $hasta);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}

    public function listarDuplicados()
    {
        $data = $this->model->listarDuplicados();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex gap-2">
            
                <button class="btn btn-danger" type="button" onclick="eliminarEmpleado(' . $data[$i]['cert_number'] . ')"><i class="fas fa-trash-alt"></i></button>
            </div>';
        }
        echo json_encode($data);
        die();
    }

        public function listarCantidadIncidencias()
    {
        $data = $this->model->listarCantidadIncidencias();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex gap-2">
            
                 
            </div>';
        }
        echo json_encode($data);
        die();
    }
    public function duplicadosPorCiudad()
    {
        $data = $this->model->duplicadosPorCiudad();
        $total = array_sum(array_column($data, 'total'));

        foreach ($data as &$row) {
            $row['porcentaje'] = $total > 0 ? ($row['total'] / $total) * 100 : 0;
        }

        echo json_encode($data);
        die();
    }

    public function duplicadosPorEstado()
    {
        $data = $this->model->duplicadosPorEstado();
        $total = array_sum(array_column($data, 'total'));

        foreach ($data as &$row) {
            $row['porcentaje'] = $total > 0 ? ($row['total'] / $total) * 100 : 0;
        }

        echo json_encode($data);
        die();
    }
    public function duplicadosMensualesPorCiudad()
{
    $data = $this->model->duplicadosMensualesPorCiudad();

    $meses = [];
    $ciudades = [];
    $dataset = [];

    foreach ($data as $row) {
        $mes = $row['mes'];
        $ciudad = $row['city'];
        $total = (int)$row['total'];

        if (!in_array($mes, $meses)) {
            $meses[] = $mes;
        }

        if (!isset($dataset[$ciudad])) {
            $dataset[$ciudad] = [];
        }

        $dataset[$ciudad][$mes] = $total;
    }

    // Armar datos por ciudad
    $series = [];
    foreach ($dataset as $ciudad => $valores) {
        $datos = [];
        foreach ($meses as $m) {
            $datos[] = isset($valores[$m]) ? $valores[$m] : 0;
        }

        $color = '#' . substr(md5($ciudad), 0, 6); // Color único por ciudad

        $series[] = [
            'label' => $ciudad,
            'data' => $datos,
            'borderColor' => $color,
            'backgroundColor' => $color,
            'fill' => false,
            'tension' => 0.2
        ];
    }

    echo json_encode([
        'labels' => $meses,
        'datasets' => $series
    ]);
    die();
}


}
