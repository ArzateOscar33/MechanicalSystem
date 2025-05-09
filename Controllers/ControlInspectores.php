<?php
class ControlInspectores extends Controller
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

        $data['title'] = 'Control de Inspectores';


        $this->views->getView('admin/ControlInspectores', "index", $data);
    }
    public function listar()
    {
        $data = $this->model->obtenerInspectores();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex"> 
            <button class="btn btn-success" type="button" onclick="editarInspector(\'' . $data[$i]['id'] . '\')"><i class="fas fa-edit"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }

public function actualizar()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $firma_actual = trim($_POST['firma_actual']);
        $firma_nueva = $_FILES['firma']['name'] ?? '';

        if (empty($id) || empty($name)) {
            $res = ['msg' => 'Nombre es obligatorio', 'icono' => 'warning'];
        } else {
            $firma_ruta = $firma_actual;

            if (!empty($firma_nueva)) {
                $ext = pathinfo($firma_nueva, PATHINFO_EXTENSION);
                $nombreFirma = 'firma_' . uniqid() . '.' . $ext;
                $destino = 'uploads/firmas_inspectores/' . $nombreFirma;

                if (move_uploaded_file($_FILES['firma']['tmp_name'], $destino)) {
                    $firma_ruta = $destino;
                } else {
                    $res = ['msg' => 'Error al subir la firma', 'icono' => 'error'];
                    echo json_encode($res, JSON_UNESCAPED_UNICODE);
                    return;
                }
            }

            $result = $this->model->modificar($name, $firma_ruta, $id);
            $res = $result
                ? ['msg' => 'Inspector actualizado correctamente', 'icono' => 'success']
                : ['msg' => 'Error al actualizar', 'icono' => 'error'];
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    die();
}

    public function editar($id)
    {
        if (is_numeric($id)) {
            $data = $this->model->getInspector($id);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
