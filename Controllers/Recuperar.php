<?php
require 'vendor/autoload.php'; // para PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Recuperar extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = 'Recuperar contraseña';
        $this->views->getView('admin/Recuperar', "index", $data);
    }

    public function solicitarToken()
    {
        $correo = $_POST['correo'];
        $usuario = $this->model->verificarCorreo($correo);
        if (!$usuario) {
            echo json_encode(['icono' => 'error', 'msg' => 'Correo no registrado']);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));
        $this->model->guardarToken($correo, $token, $expira);

        $enlace = BASE_URL . "Recuperar/restablecer?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = HOST_SMTP;
            $mail->SMTPAuth   = true;
            $mail->Username   = USER_SMTP;
            $mail->Password   = PASS_SMTP;
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = PUERTO_SMTP;

            $mail->setFrom(USER_SMTP, TITLE);
            $mail->addAddress($correo);
            $mail->isHTML(true);
            $mail->Subject = 'Recuperar acceso - ' . TITLE;
            $mail->Body = '
                <div
                    style="max-width:600px; margin:0 auto; font-family:\'Segoe UI\', sans-serif; background:#f9f9f9; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                    <div style="background-color:#1c1e74; padding:30px; text-align:center;">
                        <img src="https://cdn-icons-png.flaticon.com/128/3208/3208753.png" alt="Ícono de seguridad" width="64"
                            height="64" style="margin-bottom:10px;" />
                        <h1 style="color:white; margin:0; font-size:22px;">Solicitud para restablecer tu contraseña</h1>
                    </div>
                    <div style="padding:30px; background-color:#ffffff; color:#333;">
                        <p style="font-size:16px; line-height:1.6;">
                            Hola,<br><br>
                            Hemos recibido una solicitud para restablecer la contraseña asociada a este correo electrónico.<br><br>
                            Si realizaste esta solicitud, haz clic en el siguiente botón para establecer una nueva contraseña:
                        </p>
                        <div style="text-align:center; margin:30px 0;">
                            <a href="' . $enlace . '"
                                style="background-color:#1c1e74; color:#ffffff; padding:12px 25px; border-radius:6px; text-decoration:none; font-weight:bold;">Restablecer
                                contraseña</a>
                        </div>
                        <p style="font-size:15px; line-height:1.5;">
                            Este enlace es válido por <strong>1 hora</strong>. Si no solicitaste el cambio, puedes ignorar este
                            mensaje.
                        </p>
                        <p style="margin-top:30px;">Saludos cordiales,<br><strong>' . TITLE . '</strong></p>
                    </div>
                    <div style="background-color:#1c1e74; color:#ffffff; text-align:center; padding:20px; font-size:14px;">
                        <p style="margin: 0 0 10px;">Síguenos en nuestras redes sociales:</p>
                        <a href="https://www.facebook.com/pacificnort" target="_blank" style="margin:0 10px;">
                            <img src="https://cdn-icons-png.flaticon.com/24/733/733547.png" alt="Facebook" width="24" height="24" />
                        </a>
                        <a href="https://www.linkedin.com/company/pacificnort" target="_blank" style="margin:0 10px;">
                            <img src="https://cdn-icons-png.flaticon.com/24/1384/1384014.png" alt="LinkedIn" width="24"
                                height="24" />
                        </a>
                        <br><br>
                        © ' . date("Y") . ' ' . TITLE . '. Todos los derechos reservados.
                    </div>
                </div>
';

            $mail->AltBody = "Recibimos tu solicitud para restablecer contraseña. Usa este enlace: $enlace";


            $mail->send();
            echo json_encode(['icono' => 'success', 'msg' => 'Revisa tu correo electrónico']);
        } catch (Exception $e) {
            echo json_encode(['icono' => 'error', 'msg' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
        }
    }

public function restablecer()
{
    $token = $_GET['token'] ?? '';

    // Validar token desde el modelo
    $verificado = $this->model->verificarToken($token);

    if ($verificado) {
        $data['token'] = $token;
        $this->views->getView('admin/Recuperar', "restablecer", $data);
    } else {
        // Redirigir al controlador Errors con mensaje personalizado
        header('Location: ' . BASE_URL . 'Errors/index?msg=' . urlencode('El enlace para restablecer la contraseña ya no es válido o ha expirado.'));
        exit;
    }
}
public function cambiarPassword()
{
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if (empty($token) || empty($password) || empty($confirmar)) {
        echo json_encode(['icono' => 'error', 'msg' => 'Todos los campos son obligatorios']);
        return;
    }

    if ($password !== $confirmar) {
        echo json_encode(['icono' => 'error', 'msg' => 'Las contraseñas no coinciden']);
        return;
    }

    $usuario = $this->model->verificarToken($token);

    if ($usuario) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->model->actualizarPassword($usuario['email'], $hash);
        $this->model->eliminarToken($token);

        echo json_encode(['icono' => 'success', 'msg' => 'Contraseña actualizada correctamente']);
    } else {
        echo json_encode(['icono' => 'error', 'msg' => 'Token inválido o expirado']);
    }
}

}
