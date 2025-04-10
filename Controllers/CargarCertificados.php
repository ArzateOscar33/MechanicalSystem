<?php
class CargarCertificados extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
    }
    
    public function index()
    {
        $data['title'] = 'Cargar Certificados';
        $this->views->getView('admin/CargarCertificados', "index", $data);
    }
    
    public function cargar()
    {
        if (isset($_FILES['fileUpload'])) {
            $archivo = $_FILES['fileUpload'];
            $nombreArchivo = $archivo['name'];
            $tipo = $archivo['type'];
            
            // Verificar tipo de archivo
            if ($tipo != 'application/json') {
                $respuesta = array('msg' => 'El archivo debe ser JSON', 'icono' => 'error');
                echo json_encode($respuesta);
                return;
            }
            
            // Leer contenido del archivo
            $contenidoJson = file_get_contents($archivo['tmp_name']);
            $datos = json_decode($contenidoJson, true);
            
            if ($datos === null) {
                $respuesta = array('msg' => 'Error al decodificar el JSON', 'icono' => 'error');
                echo json_encode($respuesta);
                return;
            }
            
            // Procesar los datos
            $modelo = $this->model;
            
            // Usar nombre_usuario de la sesión en lugar de id_usuario
            // O verificar la estructura correcta de tu sesión
            $usuario_nombre = isset($_SESSION['usuario_nombre']) ? $_SESSION['username'] : 'Sistema';
            
            $data = $modelo->procesarCertificados($datos, $usuario_nombre);
            
            if ($data == 1) {
                $respuesta = array('msg' => 'Datos Cargados correctamente', 'icono' => 'success');
            } else {
                $respuesta = array('msg' => 'error al cargar la informacion', 'icono' => 'error');
            }
        } else {
            $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
        }
        echo json_encode($respuesta);
        die();
    }
}
?>