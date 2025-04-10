<?php
class CrearCertificados extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    public function index()
    {
        if (empty($_SESSION['nombre_usuario'])) {
            header('Location: ' . BASE_URL . 'admin');
            exit;
        }
        
        $data['title'] = 'Crear Certificado';
        $this->views->getView('admin/CrearCertificados', "index", $data);
    }

    public function crear()
    {
        // Validación básica del formulario
        if (isset($_POST['vin']) && isset($_POST['year']) && isset($_POST['marca'])) {
            $vin = $_POST['vin'];
            $year = $_POST['year'];
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $fabricado_en = $_POST['fabricado_en'];
            $placa = $_POST['placa'];
            $propietario = $_POST['propietario'];
            $odometro = $_POST['odometro'];
            $monitor_fallo_encendido = $_POST['monitor_fallo_encendido'];
            $monitor_sistema_combustible = $_POST['monitor_sistema_combustible'];
            $numero_certificado = $_POST['numero_certificado'];
            $latitud = $_POST['latitud'];
            $longitud = $_POST['longitud'];
            $monitor_integral_catalizador = $_POST['monitor_integral_catalizador'];
            $monitor_catalizador = $_POST['monitor_catalizador'];
            $monitor_sensor_c2 = $_POST['monitor_sensor_c2'];
            $resultado_prueba = $_POST['resultado_prueba'];
            $ebitn = $_POST['ebitn'];
            $inspector = $_POST['inspector'];
            $firma_inspector = $_POST['firma_inspector'];
            $fecha = $_POST['fecha'];
            $fecha_expiracion = $_POST['fecha_expiracion'];

            // Aquí validamos si se han subido imágenes
            if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'])) {
                $imagenes = $_FILES['imagenes'];
                // Validación de imágenes o cualquier procesamiento adicional
            }

            // Ahora que tenemos los datos, los enviamos al modelo
            $resultado = $this->model->crearCertificado(
                $vin, $year, $marca, $modelo, $fabricado_en, $placa, $propietario, $odometro, 
                $monitor_fallo_encendido, $monitor_sistema_combustible, $numero_certificado, $latitud, 
                $longitud, $monitor_integral_catalizador, $monitor_catalizador, $monitor_sensor_c2, 
                $resultado_prueba, $ebitn, $inspector, $firma_inspector, $fecha, $fecha_expiracion
            );

            // Devolvemos una respuesta como JSON
            if ($resultado) {
                $respuesta = ['msg' => 'Certificado creado exitosamente', 'icono' => 'success'];
            } else {
                $respuesta = ['msg' => 'Hubo un error al crear el certificado', 'icono' => 'error'];
            }
            
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
            die();
        } else {
            // Respuesta si los datos no están completos
            $respuesta = ['msg' => 'Faltan campos requeridos', 'icono' => 'warning'];
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
}
?>
