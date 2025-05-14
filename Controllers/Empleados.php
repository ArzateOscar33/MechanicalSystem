<?php
class Empleados extends Controller
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

    public function listar()
    {
        $data = $this->model->getEmpleados();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex gap-2">
            
                <button class="btn btn-danger" type="button" onclick="eliminarEmpleado(' . $data[$i]['id'] . ')"><i class="fas fa-trash-alt"></i></button>
            </div>';
        }
        echo json_encode($data);
        die();
    }

    public function index()
    {
        $data['title'] = 'Control de Departamentos';
        $this->views->getView('admin/empleados', 'index', $data);
    }

    public function registrar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campos = [
                'first_name',
                'last_name',
                'second_last_name',
                'curp',
                'rfc',
                'employee_number',
                'phone',
                'email',
                'birth_date',
                'gender',
                'department_id',
                'position_id'
            ];

            $datos = [];
            foreach ($campos as $campo) {
                $datos[$campo] = isset($_POST[$campo]) ? trim($_POST[$campo]) : null;
            }

            // Procesar foto
            $fotoRuta = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $nombreFoto = 'empleado_' . uniqid() . '.' . $ext;
                $destino = 'assets/empleados/fotografia/' . $nombreFoto;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $destino)) {
                    $fotoRuta = $destino;
                } else {
                    echo json_encode(['msg' => 'Error al subir la fotografía', 'icono' => 'error']);
                    return;
                }
            }

            $registro = [
                $datos['first_name'],
                $datos['last_name'],
                $datos['second_last_name'],
                strtoupper($datos['curp']),
                strtoupper($datos['rfc']),
                $datos['employee_number'],
                $datos['phone'],
                $datos['email'],
                $datos['birth_date'],
                $datos['gender'],
                $fotoRuta,
                $datos['department_id'],
                $datos['position_id'],
                date('Y-m-d')
            ];

            $res = $this->model->registrarEmpleado($registro);
            echo json_encode($res ? ['msg' => 'Empleado registrado correctamente', 'icono' => 'success']
                : ['msg' => 'Error al registrar', 'icono' => 'error']);
        }
        die();
    }


    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
    
            if ($id <= 0) {
                echo json_encode(['msg' => 'ID de empleado inválido', 'icono' => 'warning']);
                return;
            }
    
            $campos = [
                'first_name',
                'last_name',
                'second_last_name',
                'curp',
                'rfc',
                'phone',
                'email',
                'birth_date',
                'gender',
                'department_id',
                'position_id'
            ];
    
            $datos = [];
            foreach ($campos as $campo) {
                $datos[$campo] = isset($_POST[$campo]) ? trim($_POST[$campo]) : null;
            }
    
            // Validación de claves foráneas
            if (!is_numeric($datos['department_id']) || !is_numeric($datos['position_id'])) {
                echo json_encode(['msg' => 'Departamento o puesto inválido', 'icono' => 'warning']);
                return;
            }
    
            // Manejo de foto
            $fotoRuta = $_POST['foto_actual'] ?? null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $nombreFoto = 'empleado_' . uniqid() . '.' . $ext;
                $destino = 'assets/empleados/fotografia/' . $nombreFoto;
    
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $destino)) {
                    $fotoRuta = $destino;
                } else {
                    echo json_encode(['msg' => 'Error al subir la nueva foto', 'icono' => 'error']);
                    return;
                }
            }
    
            $datosParaActualizar = [
                $datos['first_name'],
                $datos['last_name'],
                $datos['second_last_name'],
                strtoupper($datos['curp']),
                strtoupper($datos['rfc']),
                $datos['phone'],
                $datos['email'],
                $datos['birth_date'],
                $datos['gender'],
                $fotoRuta,
                $datos['department_id'],
                $datos['position_id'],
                $id
            ];
    
            $res = $this->model->modificarEmpleado($datosParaActualizar);
    
            echo json_encode($res
                ? ['msg' => 'Empleado actualizado correctamente', 'icono' => 'success']
                : ['msg' => 'Error al actualizar empleado', 'icono' => 'error']);
        }
        die();
    }
    

    public function editar($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->getEmpleado($id);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function eliminar($id)
    {
        if (is_numeric($id)) {
            $res = $this->model->eliminarEmpleado($id);
            echo json_encode($res
                ? ['msg' => 'Empleado eliminado correctamente', 'icono' => 'success']
                : ['msg' => 'Error al eliminar empleado', 'icono' => 'error']);
        }
        die();
    }

    public function generarNumeroEmpleado()
    {
        $fecha = date('Y-m-d');
        $totalHoy = $this->model->contarEmpleadosHoy($fecha) + 1;
        $numero = date('Ymd') . str_pad($totalHoy, 3, '0', STR_PAD_LEFT);
        echo json_encode(['numero' => $numero]);
        die();
    }

    public function obtenerPuestos($id)
    {
        if (is_numeric($id)) {
            $puestos = $this->model->getPuestosPorDepartamento($id);
            echo json_encode($puestos);
        }
        die();
    }
    public function listarPorDepartamento($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->getEmpleadosPorDepartamento($id);
            echo json_encode($data);
        }
        die();
    }

    public function buscarPorNumero($numero)
    {
        if (!empty($numero)) {
            $data = $this->model->buscarEmpleadoPorNumero($numero);
            echo json_encode($data);
        }
        die();
    }

    public function generarCredencial($id)
    {
        if (is_numeric($id)) {
            // Aquí irá la lógica real para generar la credencial (PDF, imagen, etc.)
            echo "Generando credencial para empleado ID: $id";
        }
        die();
    }
}
