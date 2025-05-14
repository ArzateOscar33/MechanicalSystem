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

            // PRIMERO: Recolectar los datos del formulario
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

            // VALIDACIONES: CURP, RFC y número de empleado duplicado
            if ($this->model->existeCurp($datos['curp'])) {
                echo json_encode(['msg' => 'CURP ya registrada', 'icono' => 'warning']);
                return;
            }

            if ($this->model->existeRfc($datos['rfc'])) {
                echo json_encode(['msg' => 'RFC ya registrado', 'icono' => 'warning']);
                return;
            }

            if ($this->model->existeNumeroEmpleado($datos['employee_number'])) {
                echo json_encode(['msg' => 'Número de empleado ya registrado. Recarga e intenta de nuevo.', 'icono' => 'warning']);
                return;
            }

            // PROCESAR FOTO
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

            // REGISTRO FINAL
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

            echo json_encode($res
                ? ['msg' => 'Empleado registrado correctamente', 'icono' => 'success']
                : ['msg' => 'Error al registrar', 'icono' => 'error']);
        }
        die();
    }




    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

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

            // VALIDAR DUPLICADOS
            if ($this->model->existeCurpExcepto($datos['curp'], $id)) {
                echo json_encode(['msg' => 'CURP ya registrada por otro empleado', 'icono' => 'warning']);
                return;
            }

            if ($this->model->existeRfcExcepto($datos['rfc'], $id)) {
                echo json_encode(['msg' => 'RFC ya registrado por otro empleado', 'icono' => 'warning']);
                return;
            }

            if ($this->model->existeNumeroEmpleadoExcepto($datos['employee_number'], $id)) {
                echo json_encode(['msg' => 'Número de empleado ya registrado por otro', 'icono' => 'warning']);
                return;
            }

            // PROCESAR FOTO
            $fotoRuta = $_POST['foto_actual'];

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $nombreFoto = 'empleado_' . uniqid() . '.' . $ext;
                $destino = 'assets/empleados/fotografia/' . $nombreFoto;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $destino)) {
                    // ✅ Borra la imagen anterior si existe y es diferente de la nueva
                    if (!empty($_POST['foto_actual']) && file_exists($_POST['foto_actual'])) {
                        unlink($_POST['foto_actual']);
                    }

                    // Asigna nueva ruta
                    $fotoRuta = $destino;
                } else {
                    echo json_encode(['msg' => 'Error al subir la nueva fotografía', 'icono' => 'error']);
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
                $id
            ];

            $res = $this->model->actualizarEmpleado($registro);

            echo json_encode($res
                ? ['msg' => 'Empleado actualizado correctamente', 'icono' => 'success']
                : ['msg' => 'Error al actualizar', 'icono' => 'error']);
        }
        die();
    }

    public function validarCurp()
    {
        $curp = strtoupper(trim($_POST['curp']));
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $existe = $this->model->existeCurpExcepto($curp, $id);
        echo json_encode(['existe' => $existe ? true : false]);
    }

    public function validarRfc()
    {
        $rfc = strtoupper(trim($_POST['rfc']));
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $existe = $this->model->existeRfcExcepto($rfc, $id);
        echo json_encode(['existe' => $existe ? true : false]);
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
        // Obtener la ruta de la foto antes de eliminar
        $empleado = $this->model->obtenerFotoEmpleado($id);
        $fotoRuta = $empleado['photo_path'] ?? null;

        // Eliminar el registro
        $res = $this->model->eliminarEmpleado($id);

        if ($res) {
            // Eliminar físicamente la foto si existe
            if (!empty($fotoRuta) && file_exists($fotoRuta)) {
                unlink($fotoRuta);
            }

            echo json_encode(['msg' => 'Empleado eliminado correctamente', 'icono' => 'success']);
        } else {
            echo json_encode(['msg' => 'Error al eliminar empleado', 'icono' => 'error']);
        }
    }
    die();
}


    public function generarNumeroEmpleado()
    {
        $res = $this->model->obtenerUltimoNumeroEmpleadoDelDia(date('Y-m-d'));

        if (!empty($res['ultimo'])) {
            $ultimo = (int)substr($res['ultimo'], 8); // extrae el consecutivo (los últimos 3 dígitos)
            $siguiente = $ultimo + 1;
        } else {
            $siguiente = 1;
        }

        $numero = date('Ymd') . str_pad($siguiente, 3, '0', STR_PAD_LEFT);
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
