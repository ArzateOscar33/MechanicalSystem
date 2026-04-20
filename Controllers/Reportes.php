<?php
class Reportes extends Controller
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
        }
    }

    public function index()
    {
        $data['title'] = 'Reporte de Certificados';
        $this->views->getView('admin/Reportes', "index", $data);
    }

    public function listar()
    {
        $data = $this->model->obtenerReporteCertificados();

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['comentario'] = !empty($data[$i]['comentario']) ? $data[$i]['comentario'] : '';
            $data[$i]['cliente'] = !empty($data[$i]['cliente']) ? $data[$i]['cliente'] : '';
            $data[$i]['realizado_por'] = !empty($data[$i]['realizado_por']) ? $data[$i]['realizado_por'] : '';
            $data[$i]['secomext'] = !empty($data[$i]['secomext']) ? $data[$i]['secomext'] : 'NO';
            $data[$i]['bdd_m'] = !empty($data[$i]['bdd_m']) ? $data[$i]['bdd_m'] : 'NO';
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function filtrar()
    {
        $filtros = [
            'cert_number'   => isset($_GET['cert_number']) ? trim($_GET['cert_number']) : '',
            'vin'           => isset($_GET['vin']) ? trim($_GET['vin']) : '',
            'cliente'       => isset($_GET['cliente']) ? trim($_GET['cliente']) : '',
            'realizado_por' => isset($_GET['realizado_por']) ? trim($_GET['realizado_por']) : '',
            'secomext'      => isset($_GET['secomext']) ? trim($_GET['secomext']) : '',
            'bdd_m'         => isset($_GET['bdd_m']) ? trim($_GET['bdd_m']) : '',
            'fecha_inicio'  => isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : '',
            'fecha_fin'     => isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : '',
            'comentario'    => isset($_GET['comentario']) ? trim($_GET['comentario']) : ''
        ];

        $data = $this->model->obtenerReporteCertificadosFiltrado($filtros);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['comentario'] = !empty($data[$i]['comentario']) ? $data[$i]['comentario'] : '';
            $data[$i]['cliente'] = !empty($data[$i]['cliente']) ? $data[$i]['cliente'] : '';
            $data[$i]['realizado_por'] = !empty($data[$i]['realizado_por']) ? $data[$i]['realizado_por'] : '';
            $data[$i]['secomext'] = !empty($data[$i]['secomext']) ? $data[$i]['secomext'] : 'NO';
            $data[$i]['bdd_m'] = !empty($data[$i]['bdd_m']) ? $data[$i]['bdd_m'] : 'NO';
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function obtenerFiltros()
    {
        $clientes = $this->model->obtenerClientesUnicos();
        $realizadosPor = $this->model->obtenerRealizadosPorUnicos();
        $secomext = $this->model->obtenerSecomextUnicos();
        $bddm = $this->model->obtenerBddMUnicos();

        echo json_encode([
            'clientes' => $clientes,
            'realizados_por' => $realizadosPor,
            'secomext' => $secomext,
            'bdd_m' => $bddm
        ], JSON_UNESCAPED_UNICODE);
        die();
    }
}
