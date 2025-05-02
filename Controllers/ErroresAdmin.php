<?php
class ErroresAdmin extends Controller
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

        $this->views->getView('admin/ErroresAdmin', "index", $data);
    }
    public function listar()
    {
        $data = $this->model->getErrores('pending');
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex">
            <button class="btn btn-primary" type="button" onclick="editCertificate(\'' . $data[$i]['id'] . '\')"><i class="fas fa-edit"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }
    public function listarResueltos()
    {
        $data = $this->model->getErroresResueltos('corrected');
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['accion'] = '<div class="d-flex">
            <button class="btn btn-primary" type="button" onclick="editError(\'' . $data[$i]['certificate_id'] . '\')"><i class="fas fa-edit"></i></button>
        </div>';
        }
        echo json_encode($data);
        die();
    }
    /* public function registrar()
    {
        if (isset($_POST['nombre'])) {
            $username = $_POST['username'];
            $nombre = $_POST['nombre'];
            $apellido = $_POST['apellido'];
            $correo = $_POST['correo'];
            $clave = $_POST['clave'];
            $phone = $_POST['phone'];
            $role_id = $_POST['rol']; // <-- ¡nuevo!
            $id = $_POST['id'];
    
            if (empty($nombre) || empty($apellido) || empty($role_id)) {
                $respuesta = array('msg' => 'Todos los campos son requeridos', 'icono' => 'warning');
            } else {
                if (empty($id)) {
                    $result = $this->model->verificarCorreo($correo);
                    if (empty($result)) {
                        $hash = password_hash($clave, PASSWORD_DEFAULT);
                        $data = $this->model->registrar($username, $nombre, $apellido, $correo, $hash, $phone, $role_id);
                        if ($data > 0) {
                            $respuesta = array('msg' => 'Usuario registrado', 'icono' => 'success');
                        } else {
                            $respuesta = array('msg' => 'Error al registrar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'Correo ya existe', 'icono' => 'warning');
                    }
                } else {
                    // Puedes agregar aquí la lógica para modificar el rol también si lo deseas
                    $data = $this->model->modificar($username, $nombre, $apellido, $correo, $phone, $role_id, $id);
                    if ($data == 1) {
                        $respuesta = array('msg' => 'Usuario modificado', 'icono' => 'success');
                    } else {
                        $respuesta = array('msg' => 'Error al modificar', 'icono' => 'error');
                    }
                }
            }
            echo json_encode($respuesta);
        }
        die();
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
    }*/
    //editar user
    public function editCertificate($error_id)
    {
        if (!is_numeric($error_id)) {
            echo json_encode(['msg' => 'ID inválido', 'icono' => 'error']);
            return;
        }
    
        $data = $this->model->obtenerCertificadoPorError($error_id);
        echo json_encode($data);
        die();
    }
    
    //editar user
    public function editError($id_certificate)
    {

        $data = $this->model->getErrores($id_certificate);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);

        die();
    }

    public function corregirCertificado()
    {
        // Verifica que los datos básicos estén presentes
        if (!isset($_POST['id'], $_POST['cert_number'], $_POST['field_name'])) {
            echo json_encode(['msg' => 'Datos incompletos', 'icono' => 'error']);
            return;
        }
    
        $correction_id = $_POST['id'];
        $cert_number = $_POST['cert_number'];
        $field_name = $_POST['field_name'];
        $new_value = $_POST[$field_name];
        $user_id = $_SESSION['id_usuario'];
    
        // 1. Obtener el valor actual antes del cambio
        $cert = $this->model->obtenerCertificado($cert_number);
        if (!$cert) {
            echo json_encode(['msg' => 'Certificado no encontrado', 'icono' => 'error']);
            return;
        }
    
        $old_value = $cert[$field_name] ?? null;
    
        // 2. Actualizar el campo en certificates
        $sql = "UPDATE certificates SET `$field_name` = ?, updated_at = NOW() WHERE cert_number = ?";
        $this->model->query($sql, [$new_value, $cert_number]);
    
        // 3. Registrar el cambio en correction_logs
        $sqlLog = "INSERT INTO correction_logs (correction_request_id, certificate_id, field_name, old_value, new_value, corrected_by)
                   VALUES (?, ?, ?, ?, ?, ?)";
        $this->model->query($sqlLog, [$correction_id, $cert_number, $field_name, $old_value, $new_value, $user_id]);
    
        // 4. Actualizar el estado en correction_requests
        $sqlUpdate = "UPDATE correction_requests SET status = 'corrected', reviewed_by = ?, reviewed_at = NOW(), updated_at = NOW()
                      WHERE id = ?";
        $this->model->query($sqlUpdate, [$user_id, $correction_id]);
    
        // 5. Regenerar PDF y ZIP
        require_once "CrearCertificados.php"; // o el controlador que contiene el generador
        //$certGen = new CrearCertificados();   // instancia del generador que tú usas
        //$certGen->generarPDFyZIP($cert_number); // tú ya dijiste que tienes este método
    
        echo json_encode(['msg' => 'Certificado actualizado correctamente', 'icono' => 'success']);
        die();
    }
    
}
