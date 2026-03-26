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

            // OJO: aquí debe ir SOLO la ruta relativa, no BASE_URL
            $archivo = '';
            if (!empty($data[$i]['zip_file_path'])) {
                $archivo = trim((string)$data[$i]['zip_file_path']);
            } elseif (!empty($data[$i]['source_file'])) {
                $archivo = trim((string)$data[$i]['source_file']);
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
        $path = isset($_GET['file']) ? trim((string)$_GET['file']) : '';

        if ($path === '') {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Ruta de archivo no proporcionada']);
            exit;
        }

        // Normalizar separadores
        $path = str_replace(['\\', '//'], '/', $path);

        // Quitar slash inicial para evitar rutas absolutas
        $path = ltrim($path, '/');

        // Bloquear traversal
        if (strpos($path, '..') !== false) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Ruta de archivo inválida']);
            exit;
        }

        // Ruta base real del proyecto
        $basePath = realpath(__DIR__ . '/../');
        $fullPath = realpath($basePath . DIRECTORY_SEPARATOR . $path);

        // Validar que exista y que quede dentro del proyecto
        if (!$fullPath || !is_file($fullPath) || strpos($fullPath, $basePath) !== 0) {
            http_response_code(404);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Archivo no encontrado']);
            exit;
        }

        // Detectar mime
        $mimeType = function_exists('mime_content_type')
            ? mime_content_type($fullPath)
            : 'application/octet-stream';

        if (!$mimeType) {
            $mimeType = 'application/octet-stream';
        }

        // Respuesta para validación HEAD
        if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($fullPath));
            http_response_code(200);
            exit;
        }

        // Descargar archivo
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: public');
        header('Expires: 0');

        readfile($fullPath);
        exit;
    }
}
