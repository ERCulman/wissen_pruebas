<?php

require_once __DIR__ . "/../config/brevo_config.php";
require_once __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailServicio {

    static public function enviarEmailBienvenida($email, $nombre, $usuario) {
        
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = BREVO_SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = BREVO_SMTP_USER;
            $mail->Password   = BREVO_SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = BREVO_SMTP_PORT;
            
            // Deshabilitar verificación SSL
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Remitente y destinatario
            $mail->setFrom(BREVO_FROM_EMAIL, BREVO_FROM_NAME);
            $mail->addAddress($email, $nombre);

            // Contenido
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Bienvenido a Wissen System';
            
            $enlaceLogin = "http://" . $_SERVER['HTTP_HOST'] . "/wissen/login";
            
            $mail->Body = "
                <h2>¡Bienvenido a Wissen System!</h2>
                <p>Hola <strong>$nombre</strong>,</p>
                <p>Tu cuenta ha sido creada exitosamente en nuestro sistema.</p>
                <p><strong>Datos de acceso:</strong></p>
                <ul>
                    <li><strong>Usuario:</strong> $usuario</li>
                    <li><strong>Enlace de acceso:</strong> <a href='$enlaceLogin'>$enlaceLogin</a></li>
                </ul>
                <p>Ya puedes iniciar sesión con tu usuario y la contraseña que estableciste.</p>
                <br>
                <p>Saludos,<br>Equipo Wissen System</p>
            ";

            $mail->send();
            return "ok";
            
        } catch (Exception $e) {
            error_log("Error PHPMailer bienvenida: " . $e->getMessage());
            return "Error: " . $e->getMessage();
        }
    }

    static public function enviarEmailRecuperacion($email, $nombre, $token) {
        
        error_log("[EMAIL] Iniciando envío de email a: $email con token: $token - " . date('Y-m-d H:i:s'));
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = BREVO_SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = BREVO_SMTP_USER;
            $mail->Password   = BREVO_SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = BREVO_SMTP_PORT;
            
            // Deshabilitar verificación SSL
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Remitente y destinatario
            $mail->setFrom(BREVO_FROM_EMAIL, BREVO_FROM_NAME);
            $mail->addAddress($email, $nombre);

            // Contenido
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Recuperación de Contraseña - Wissen System';
            
            $enlaceRecuperacion = "http://" . $_SERVER['HTTP_HOST'] . "/wissen/resetear-password.php?token=" . $token;
            
            $mail->Body = "
                <h2>Recuperación de Contraseña</h2>
                <p>Hola <strong>$nombre</strong>,</p>
                <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para crear una nueva contraseña:</p>
                <p><a href='$enlaceRecuperacion' style='background-color: #3c8ebd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a></p>
                <p>Este enlace expirará en 1 hora.</p>
                <p>Si no solicitaste este cambio, ignora este correo.</p>
                <br>
                <p>Saludos,<br>Equipo Wissen System</p>
            ";

            $mail->send();
            error_log("[EMAIL] Email enviado exitosamente a: $email");
            return "ok";
            
        } catch (Exception $e) {
            error_log("[EMAIL] Error PHPMailer: " . $e->getMessage());
            error_log("[EMAIL] ErrorInfo: " . $mail->ErrorInfo);
            return "Error: " . $e->getMessage();
        }
    }
}

?>