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

    public function vistaCertificados()
    {

        $data['title'] = 'Bienvenido a Mechanical Emission Services';
        $this->views->getView('principal', 'listarCertificados', $data);
    }
    public function listarCertificados()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPageRaw = isset($_GET['per_page']) ? trim((string)$_GET['per_page']) : '25';

        if ($perPageRaw === 'all') {
            $perPage = 10000000;
        } else {
            $perPage = (int)$perPageRaw;
        }

        $buscar = isset($_GET['buscar']) ? trim((string)$_GET['buscar']) : '';

        $totalRow = $this->model->certificadosTotales($buscar);
        $total = (int)($totalRow['total'] ?? 0);

        $data = $this->model->listarCertificadosPaginado($page, $perPage, $buscar);

        for ($i = 0; $i < count($data); $i++) {
            $fecha = !empty($data[$i]['test_date']) ? $data[$i]['test_date'] : $data[$i]['created_at'];

            $archivo = '';
            if (!empty($data[$i]['zip_file_path'])) {
                $archivo = BASE_URL . $data[$i]['zip_file_path'];
            } elseif (!empty($data[$i]['source_file'])) {
                $archivo = BASE_URL . $data[$i]['source_file'];
            }

            $data[$i]['fecha_mostrar'] = $fecha;
            $data[$i]['archivo_url'] = $archivo;
        }

        $isAll = ($perPage >= 10000000);
        $totalPages = $isAll ? 1 : (int)ceil($total / max(1, $perPage));

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'rows' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $isAll ? 'all' : $perPage,
            'total_pages' => $totalPages
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    public function descargarCertificado()
    {
        $path = $_GET['file'] ?? '';

        $fullPath = __DIR__ . "/../" . $path;

        if (!file_exists($fullPath)) {
            http_response_code(404);
            echo json_encode(['error' => 'Archivo no encontrado']);
            exit;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
        readfile($fullPath);
        exit;
    }
}
