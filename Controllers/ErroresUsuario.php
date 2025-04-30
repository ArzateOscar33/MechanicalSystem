<?php
class ErroresUsuario extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
    }
    public function index()
    {
        $data['title'] = 'Manejo de Errores de Usuario';
        $data['campos'] = $this->model->getCamposPermitidos();  
        $this->views->getView('admin/ErroresUsuario', "index", $data);
        
    }

    public function getCertificados()
    {
        $userId = $_SESSION['id_usuario'];
        $certs = $this->model->getCertificados($userId);
        echo json_encode($certs);
        die();
    }

    public function getCamposPermitidos()
    {
        $campos = $this->model->getCamposPermitidos();
        echo json_encode($campos);
        die();
    }

    public function listar()
    {
        $data = $this->model->getUsuarios(1);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex">
            <button class="btn btn-primary" type="button" onclick="editUser(' . $data[$i]['id'] . ')"><i class="fas fa-edit"></i></button>
            <button class="btn btn-danger" type="button" onclick="eliminarUser(' . $data[$i]['id'] . ')"><i class="fas fa-trash"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }
    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cert_number = $_POST['cert_number'] ?? '';
            $user_id = $_SESSION['id_usuario'];
            $field_name = $_POST['field_name'] ?? '';  // <- Aquí se captura
            $proposed_value = $_POST['proposed_value'] ?? '';
            $reason = $_POST['reason'] ?? '';
    
            // Validación básica de campos obligatorios
            if (empty($cert_number) || empty($field_name) || empty($proposed_value)) {
                echo json_encode(['msg' => 'Todos los campos son obligatorios', 'icono' => 'warning']);
                return;
            }
    
            // ✅ Aquí debes agregar la validación contra la lista blanca
            $permitidos = array_column($this->model->getCamposPermitidos(), 'field_name');
            if (!in_array($field_name, $permitidos)) {
                echo json_encode(['msg' => 'Campo no permitido para corrección', 'icono' => 'error']);
                return;
            }
    
            // Verificación de certificado válido
            $certificado = $this->model->validarCertificadoUsuarioEnPlazo($cert_number, $user_id);
            $esEnPlazo = !empty($certificado) ? 1 : 0;
            
            // Aunque esté fuera de plazo, seguimos permitiendo el registro:
            $certificadoExistente = $certificadoExistente = $this->model->validarCertificadoPorUsuario($cert_number, $user_id); // ahora sin validación de fecha
            if (empty($certificadoExistente)) {
                echo json_encode(['msg' => 'Certificado no válido para este usuario', 'icono' => 'error']);
                return;
            }
            
    
            // Obtener valor actual del campo
            $valorActual = $this->model->getValorActualCampo($cert_number, $field_name);
            $current_value = $valorActual[$field_name] ?? null;
    
            // Registrar corrección
            $idCorreccion = $this->model->registrarCorreccion(
                $cert_number,
                $user_id,
                $field_name,
                $current_value,
                $proposed_value,
                $reason,
                $esEnPlazo // 1 o 0
            );
            
            if ($idCorreccion > 0) {
                echo json_encode(['msg' => 'Error reportado correctamente', 'icono' => 'success']);
            } else {
                echo json_encode(['msg' => 'Error al guardar el reporte', 'icono' => 'error']);
            }
        }
        return;
    }
    


    //eliminar user
    public function delete($idUser)
    {
        if (is_numeric($idUser)) {
            $data = $this->model->eliminar($idUser);
            if ($data == 1) {
                $respuesta = array('msg' => 'usuario dado de baja', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al eliminar', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
    //editar user
    public function edit($idUser)
    {
        if (is_numeric($idUser)) {
            $data = $this->model->getUsuario($idUser);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
