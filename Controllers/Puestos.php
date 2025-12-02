<?php
class Puestos extends Controller {
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

        // Validar que sea administrador (rol_id = 1)
        if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] != 1) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
    }

    public function index()
    { 
        $data['title'] = 'Control de Puestos';
        $this->views->getView('admin/puestos', 'index', $data);
    }

    /**
     * Listar todos los puestos (para DataTable)
     */
    public function listar()
    {
        // Ahora usamos getPuestos() del modelo
        $data = $this->model->getPuestos();

        for ($i = 0; $i < count($data); $i++) {
            // Botones para acciones (editar / eliminar)
            $data[$i]['accion'] = '<div class="d-flex gap-2"> 
                <button class="btn btn-primary btn-sm" type="button" onclick="editarPuesto(' . $data[$i]['id'] . ')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm" type="button" onclick="eliminarPuesto(' . $data[$i]['id'] . ')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>';
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Listar departamentos para llenar el <select> del modal de puestos
     */
    public function getDepartamentos()
    {
        $data = $this->model->getDepartamentos();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Registrar un nuevo puesto
     */
    public function registrar()
    {
        $nombre       = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $descripcion  = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        $departamento = isset($_POST['departamento']) ? intval($_POST['departamento']) : 0;

        if (empty($nombre)) {
            echo json_encode(['msg' => 'El nombre del puesto es obligatorio', 'icono' => 'warning'], JSON_UNESCAPED_UNICODE);
            die();
        }

        if (empty($departamento)) {
            echo json_encode(['msg' => 'Debe seleccionar un departamento', 'icono' => 'warning'], JSON_UNESCAPED_UNICODE);
            die();
        }

        $res = $this->model->registrar($nombre, $descripcion, $departamento);

        if ($res > 0) {
            $respuesta = ['msg' => 'Puesto registrado correctamente', 'icono' => 'success'];
        } else {
            $respuesta = ['msg' => 'Error al registrar el puesto', 'icono' => 'error'];
        }

        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        die();
    }

    /**
     * Actualizar un puesto existente
     */
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id           = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $nombre       = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $descripcion  = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $departamento = isset($_POST['departamento']) ? intval($_POST['departamento']) : 0;

            if (empty($id) || empty($nombre)) {
                $res = ['msg' => 'El nombre del puesto es obligatorio', 'icono' => 'warning'];
            } elseif (empty($departamento)) {
                $res = ['msg' => 'Debe seleccionar un departamento', 'icono' => 'warning'];
            } else {
                $result = $this->model->modificar($nombre, $descripcion, $departamento, $id);
                $res = $result
                    ? ['msg' => 'Puesto actualizado correctamente', 'icono' => 'success']
                    : ['msg' => 'Error al actualizar el puesto', 'icono' => 'error'];
            }

            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    /**
     * Obtener un puesto por ID (para cargar datos en el modal de edición)
     */
    public function editar($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->getPuesto($id);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    /**
     * Eliminar un puesto
     * OJO: por la FK con employees (ON DELETE CASCADE) se podrían eliminar empleados relacionados
     */
    public function eliminar($id)
    {
        if (is_numeric($id)) {
            $res = $this->model->eliminar($id);
            $respuesta = $res
                ? ['msg' => 'Puesto eliminado correctamente', 'icono' => 'success']
                : ['msg' => 'Error al eliminar el puesto', 'icono' => 'error'];

            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
?>
