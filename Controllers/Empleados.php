<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

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
        // Validar que sea administrador (rol_id = 1)
        if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] != 1) {
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
    if (!is_numeric($id) || intval($id) <= 0) {
        die("ID inválido");
    }

    $empleado = $this->model->getEmpleado($id);
    if (!$empleado) {
        die("Empleado no encontrado");
    }

    $empleado['nombre_completo'] = trim($empleado['first_name'] . ' ' . $empleado['last_name'] . ' ' . $empleado['second_last_name']);
    $empleado['puesto'] = $this->model->getNombrePuesto($empleado['position_id']);
    $empleado['departamento'] = $this->model->getNombreDepartamento($empleado['department_id']);

    $rutaFolder = 'assets/empleados/credencial/';
    if (!file_exists($rutaFolder)) {
        mkdir($rutaFolder, 0755, true);
    }

    $qrFilename = 'qr_' . $empleado['employee_number'] . '.png';
    $qrRelativePath = $rutaFolder . $qrFilename;
    $qrAbsolutePath = $_SERVER['DOCUMENT_ROOT'] . '/MechanicalSystem/' . $qrRelativePath;
    $qrWebPath = BASE_URL . $qrRelativePath;

    $contenidoQR = $empleado['employee_number'];
    $optionsQR = new \chillerlan\QRCode\QROptions([
        'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L
    ]);
    (new \chillerlan\QRCode\QRCode($optionsQR))->render($contenidoQR, $qrAbsolutePath);

   // $logoPath = BASE_URL . 'assets/images/4.png'; //logo de FABST
    
    $logoPath = BASE_URL . 'assets/images/logo.png'; //logo de FABST
    $fotoWebPath = BASE_URL . $empleado['photo_path'];
    //Conenido html para las credenciales
    //Contenido html para las credenciales
    $html = "
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Credencial Empleado</title>
        <style>
            @page {
                margin: 0;
                padding: 0;
            }

            body {
                margin: 0;
                padding: 20px;
                font-family: 'Segoe UI', sans-serif;
                background: rgb(255, 255, 255);
                text-align: center;
            }

            .contenedor-credencial {
                display: inline-block;
                text-align: center;
                transform: scale(0.65);
                transform-origin: top left;
            }

            .credencial,
            .reverso {
                width: 230pt;
                height: 380pt;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
                display: inline-block;
                vertical-align: top;
                position: relative;
                border: 1.5px solid #000;
            }

            .credencial {
                margin-right: 0px;
            }

            /* Agujero para perforación - mismo en ambas caras */
            .agujero {
                position: absolute;
                width: 30pt;
                height: 10pt;
                background: white;
                border: 2px solid #666;
                border-radius: 8px;
                top: 5pt;
                left: 50%;
                transform: translateX(-50%);
                z-index: 10;
            }

            .header {
                background: #284096;
                color: white;
                padding: 10px 0;
                text-align: center;
                height: 90px;
                width: 100%;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .header img.logo {
                width: 130px;
                height: 65px;
                display: block;
                margin: 15px auto;
            }

            .body {
                background: #f7f7ff;
                padding: 0;
                position: absolute;
                top: 90px;
                bottom: 40px; /* Espacio para el footer */
                left: 0;
                right: 0;
                width: 100%;
                box-sizing: border-box;
                overflow: hidden;
            }

            .foto {
                width: 160px;
                height: 160px;
                background: #284096;
                border-radius: 12px;
                margin: 15px auto;
                text-align: center;
                border: 2px solid #666;
            }

            .foto img {
                width: 100%;
                height: 100%;
                border-radius: 12px;
                object-fit: cover;
            }

            .datos {
                font-size: 13.5px;
                margin-top: 15px;
                color: #050505ff;
                width: 100%;
                box-sizing: border-box;
                padding: 0 15px;
            }

            .datos table {
                width: 100%;
                border-collapse: collapse;
            }

            .datos td {
                padding: 3px 5px;
                text-align: left;
                vertical-align: top;
            }

            .datos td:first-child {
                font-weight: bold;
                width: 35%;
            }

            .footer {
                background: #284096;
                color: white;
                padding: 8px 15px;
                font-size: 11px;
                text-align: left;
                height: 24px;
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100%;
                box-sizing: border-box;
                display: flex;
                align-items: center;
            }

            /* Estilos específicos para el reverso */
            .reverso-content {
                background: #284096;
                color: black;
                padding: 0;
                box-sizing: border-box;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                width: 100%;
                height: 100%;
            }

            .reverso h4 {
                margin: 60px 0 10px 0;
                text-align: center;
                font-size: 16px;
                color: white;
            }

            .qr {
                text-align: center;
                margin: 30px auto;
            }

            .qr img {
                width: 160px;
                height: 160px;
                background: white;
                padding: 5px;
                border-radius: 5px;
            }

            .direccion {
                font-size: 14px;
                line-height: 1.4;
                text-align: center;
                margin-top: 25px;
                color: white;
            }

            .rfc {
                font-size: 14px;
                line-height: 1.4;
                text-align: center;
                position: absolute;
                bottom: 20px;
                left: 0;
                right: 0;
                width: 100%;
                color: white;
            }
        </style>
    </head>

    <body>
        <div class='contenedor-credencial'>
            <!-- FRENTE DE LA CREDENCIAL -->
            <div class='credencial'>
                <div class='agujero'></div>
                <div class='header'>
                    <img src='{$logoPath}' class='logo' alt='Logo'>
                </div>
                <div class='body'>
                    <div class='foto'>
                        <img src='{$fotoWebPath}' alt='Foto'>
                    </div>
                    <div class='datos'>
                        <table>
                            <tr>
                                <td><strong>Nombre:</strong></td>
                                <td>{$empleado['nombre_completo']}</td>
                            </tr>
                            <tr>
                                <td><strong>Puesto:</strong></td>
                                <td>{$empleado['puesto']}</td>
                            </tr>
                            <tr>
                                <td><strong>No.Empleado:</strong></td>
                                <td>{$empleado['employee_number']}</td>
                            </tr>
                            <tr>
                                <td><strong>Departamento:</strong></td>
                                <td>{$empleado['departamento']}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class='footer'>
                    Fecha De Emisión: " . date('d/m/Y', strtotime($empleado['issue_date'])) . "
                </div>
            </div>

            <!-- REVERSO DE LA CREDENCIAL -->
            <div class='reverso'>
                <div class='agujero'></div>
                <div class='reverso-content'>
                    <h4>DIVISIÓN MÉXICO</h4>
                    <div class='qr'>
                        <img src='{$qrWebPath}' alt='Código QR del empleado'>
                    </div>
                    <div class='direccion'>
                        Dirección: Cayetano Perez,Ext. 240,Int. 1.<br>
                        Burócrata Ruiz Cortines, 22406, Tijuana, B.C.
                    </div>
                    <div class='rfc'>
                        RFC: FCO180803NX7
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>";
    // Generar PDF con Dompdf
    $options = new Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    
    $dompdf->setPaper('a4','landscape');
    $dompdf->render();

    $pdfPath = $rutaFolder . 'credencial_' . $empleado['employee_number'] . '.pdf';
    file_put_contents($pdfPath, $dompdf->output());

    if (file_exists($qrAbsolutePath)) {
        unlink($qrAbsolutePath);
    }

    $dompdf->stream('credencial_' . $empleado['employee_number'] . '.pdf', ['Attachment' => true]);
    exit;
}
public function generarTodasCredenciales()
{
    require_once 'vendor/autoload.php';

    $empleados = $this->model->getEmpleados();
    if (empty($empleados)) {
        echo json_encode(['status' => 'error', 'msg' => 'No hay empleados']);
        return;
    }

    $html = '';
    $rutaFolder = 'assets/empleados/credencial/';
    if (!file_exists($rutaFolder)) {
        mkdir($rutaFolder, 0755, true);
    }

    foreach ($empleados as $empleado) {
        // Asegurarse de que tenga los datos necesarios
        $empleado['nombre_completo'] = trim($empleado['nombre_completo']);
        
        $fotoPath = !empty($empleado['photo_path']) ? $empleado['photo_path'] : 'assets/images/default.png';

        // Generar QR
        $qrFilename = 'qr_' . $empleado['employee_number'] . '.png';
        $qrRelativePath = $rutaFolder . $qrFilename;
        $qrAbsolutePath = $_SERVER['DOCUMENT_ROOT'] . '/MechanicalSystem/' . $qrRelativePath;
        $qrWebPath = BASE_URL . $qrRelativePath;

        $optionsQR = new \chillerlan\QRCode\QROptions([
            'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L
        ]);
        (new \chillerlan\QRCode\QRCode($optionsQR))->render($empleado['employee_number'], $qrAbsolutePath);

        $fotoWebPath = BASE_URL . $fotoPath;
        //$logoPath = BASE_URL . 'assets/images/4.png'; //logo de FABST
        $logoPath = BASE_URL . 'assets/images/logo.png'; //Logo Mechanical
        $fechaEmision = date('d/m/Y', strtotime($empleado['issue_date']));

        // HTML por empleado - USANDO EL MISMO ESTILO QUE LA FUNCIÓN INDIVIDUAL
        $html .= "
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>Credencial Empleado</title>
            <style>
                @page {
                    margin: 0;
                    padding: 0;
                }

                body {
                    margin: 0;
                    padding: 20px;
                    font-family: 'Segoe UI', sans-serif;
                    background: rgb(255, 255, 255);
                    text-align: center;
                }

                .contenedor-credencial {
                    display: inline-block;
                    text-align: center;
                    transform: scale(0.65);
                    transform-origin: top left;
                }

                .credencial,
                .reverso {
                    width: 230pt;
                    height: 380pt;
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
                    display: inline-block;
                    vertical-align: top;
                    position: relative;
                    border: 1.5px solid #000;
                }

                .credencial {
                    margin-right: 0px;
                }

                /* Agujero para perforación - mismo en ambas caras */
                .agujero {
                    position: absolute;
                    width: 30pt;
                    height: 10pt;
                    background: white;
                    border: 2px solid #666;
                    border-radius: 8px;
                    top: 5pt;
                    left: 50%;
                    transform: translateX(-50%);
                    z-index: 10;
                }

                .header {
                    background: #284096;
                    color: white;
                    padding: 10px 0;
                    text-align: center;
                    height: 90px;
                    width: 100%;
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    box-sizing: border-box;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                }

                .header img.logo {
                    width: 100px;
                    height: 100px;
                    display: block;
                    margin: 0 auto;
                }

                .body {
                    background: #f7f7ff;
                    padding: 0;
                    position: absolute;
                    top: 90px;
                    bottom: 40px; /* Espacio para el footer */
                    left: 0;
                    right: 0;
                    width: 100%;
                    box-sizing: border-box;
                    overflow: hidden;
                }

                .foto {
                    width: 160px;
                    height: 160px;
                    background: #284096;
                    border-radius: 12px;
                    margin: 15px auto;
                    text-align: center;
                    border: 2px solid #666;
                }

                .foto img {
                    width: 100%;
                    height: 100%;
                    border-radius: 12px;
                    object-fit: cover;
                }

                .datos {
                    font-size: 13.5px;
                    margin-top: 15px;
                    color: #050505ff;
                    width: 100%;
                    box-sizing: border-box;
                    padding: 0 15px;
                }

                .datos table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .datos td {
                    padding: 3px 5px;
                    text-align: left;
                    vertical-align: top;
                }

                .datos td:first-child {
                    font-weight: bold;
                    width: 35%;
                }

                .footer {
                    background: #284096;
                    color: black;
                    padding: 8px 15px;
                    font-size: 11px;
                    text-align: left;
                    height: 24px;
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    width: 100%;
                    box-sizing: border-box;
                    display: flex;
                    align-items: center;
                }

                /* Estilos específicos para el reverso */
                .reverso-content {
                    background: #284096;
                    color: black;
                    padding: 0;
                    box-sizing: border-box;
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    width: 100%;
                    height: 100%;
                }

                .reverso h4 {
                    margin: 60px 0 10px 0;
                    text-align: center;
                    font-size: 16px;
                }

                .qr {
                    text-align: center;
                    margin: 30px auto;
                }

                .qr img {
                    width: 160px;
                    height: 160px;
                    background: white;
                    padding: 5px;
                    border-radius: 5px;
                }

                .direccion {
                    font-size: 14px;
                    line-height: 1.4;
                    text-align: center;
                    margin-top: 25px;
                }

                .rfc {
                    font-size: 14px;
                    line-height: 1.4;
                    text-align: center;
                    position: absolute;
                    bottom: 20px;
                    left: 0;
                    right: 0;
                    width: 100%;
                }
            </style>
        </head>
        <body>
            <div class='contenedor-credencial'>
                <!-- FRENTE DE LA CREDENCIAL -->
                <div class='credencial'>
                    <div class='agujero'></div>
                    <div class='header'>
                        <img src='{$logoPath}' class='logo' alt='Logo'>
                    </div>
                    <div class='body'>
                        <div class='foto'>
                            <img src='{$fotoWebPath}' alt='Foto'>
                        </div>
                        <div class='datos'>
                            <table>
                                <tr>
                                    <td><strong>Nombre:</strong></td>
                                    <td>{$empleado['nombre_completo']}</td>
                                </tr>
                                <tr>
                                    <td><strong>Puesto:</strong></td>
                                    <td>{$empleado['puesto']}</td>
                                </tr>
                                <tr>
                                    <td><strong>No.Empleado:</strong></td>
                                    <td>{$empleado['employee_number']}</td>
                                </tr>
                                <tr>
                                    <td><strong>Departamento:</strong></td>
                                    <td>{$empleado['departamento']}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class='footer'>
                        Fecha De Emisión: {$fechaEmision}
                    </div>
                </div>

                <!-- REVERSO DE LA CREDENCIAL -->
                <div class='reverso'>
                    <div class='agujero'></div>
                    <div class='reverso-content'>
                        <h4>DIVISIÓN MÉXICO</h4>
                        <div class='qr'>
                            <img src='{$qrWebPath}' alt='Código QR del empleado'>
                        </div>
                        <div class='direccion'>
                            Dirección: Cayetano Perez,Ext. 240,Int. 1.<br>
                            Burócrata Ruiz Cortines, 22406, Tijuana, B.C.
                        </div>
                        <div class='rfc'>
                            RFC: FCO180803NX7
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>";

        // Limpieza del QR al terminar el script
        register_shutdown_function(function () use ($qrAbsolutePath) {
            if (file_exists($qrAbsolutePath)) unlink($qrAbsolutePath);
        });
    }

    $options = new Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('a4', 'landscape');
    $dompdf->render();

    $filename = 'credenciales_todas.pdf';
    $outputPath = $rutaFolder . $filename;
    file_put_contents($outputPath, $dompdf->output());

    echo json_encode(['status' => 'success', 'url' => BASE_URL . $outputPath]);
    exit;
}




}

