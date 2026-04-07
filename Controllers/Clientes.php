<?php
class Clientes extends Controller
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

        // Validar que sea administrador (rol_id = 1)
        if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] != 1) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
    }

    public function index()
    {
        $data['title'] = 'clientes';
        $this->views->getView('admin/clientes', "index", $data);
    }

    public function listar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $data = $this->model->getcustomers();

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm" type="button" onclick="editCliente(' . $data[$i]['id'] . ')">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-danger btn-sm" type="button" onclick="eliminarCliente(' . $data[$i]['id'] . ')">
                <i class="fas fa-trash"></i>
            </button>
        </div>';
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrar()
    {
        if (isset($_POST['nombre_cliente'])) {
            $nombre_cliente = trim($_POST['nombre_cliente']);
            $id = isset($_POST['id']) ? trim($_POST['id']) : '';

            if (empty($nombre_cliente)) {
                $respuesta = array('msg' => 'El nombre del cliente es requerido', 'icono' => 'warning');
            } else {
                if (empty($id)) {
                    $existe = $this->model->verificarCliente($nombre_cliente);

                    if (empty($existe)) {
                        $data = $this->model->registrar($nombre_cliente);

                        if ($data > 0) {
                            $respuesta = array('msg' => 'Cliente registrado correctamente', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'Error al registrar el cliente', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'El cliente ya existe', 'icono' => 'warning');
                    }
                } else {
                    $existe = $this->model->verificarCliente($nombre_cliente);

                    if (!empty($existe) && $existe['id'] != $id) {
                        $respuesta = array('msg' => 'El cliente ya existe', 'icono' => 'warning');
                    } else {
                        $data = $this->model->modificar($nombre_cliente, $id);

                        if ($data == 1) {
                            $respuesta = array('msg' => 'Cliente modificado correctamente', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'Error al modificar el cliente', 'icono' => 'error');
                        }
                    }
                }
            }

            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        }

        die();
    }

    public function delete($idCliente)
    {
        if (is_numeric($idCliente)) {
            $data = $this->model->eliminar($idCliente);

            if ($data == 1) {
                $respuesta = array('msg' => 'Cliente eliminado correctamente', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'Error al eliminar el cliente', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'ID de cliente inválido', 'icono' => 'error');
        }

        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function edit($idCliente)
    {
        if (is_numeric($idCliente)) {
            $data = $this->model->getCliente($idCliente);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        die();
    }
}
