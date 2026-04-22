<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir los archivos de la librería PHPMailer
require 'PHPMailer-6.9.1/src/Exception.php';
require 'PHPMailer-6.9.1/src/PHPMailer.php';
require 'PHPMailer-6.9.1/src/SMTP.php';

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario (se envían por FormData en JS)
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $nivel = $_POST['nivel'] ?? '';

    // Validación básica de seguridad en el backend
    if (empty($nombre) || empty($correo)) {
        echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios."]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // --- 1. Configuración del Servidor SMTP ---
        $mail->isSMTP();
        $mail->Host       = 'catolicaschool.edu.pe'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'notiweb@catolicaschool.edu.pe';
        $mail->Password   = 'Cato2026Web';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Para puerto 465 (SSL/TLS estricto)
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8'; // Para asegurar las tildes y ñ

        // --- 2. Remitente y Destinatarios ---
        // Desde quién se envía (Debe coincidir con la cuenta autenticada para evitar SPAM)
        $mail->setFrom('notiweb@catolicaschool.edu.pe', 'Web Católica School');
        // A dónde llega el correo final (tu buzón de pruebas / admisiones)
        $mail->addAddress('correoprueba@colegiolacatolica.edu.pe', 'Admisiones Católica School');
        // Si quieres responder el correo, que la respuesta vaya al usuario
        $mail->addReplyTo($correo, $nombre);

        // --- 3. Contenido del Correo (Plantilla HTML Premium) ---
        $mail->isHTML(true);
        $mail->Subject = 'Nuevo registro web: ' . $nombre . ' - Nivel ' . ucfirst($nivel);
        
        // Colores extraídos del CSS: Primary=hsl(223, 100%, 55%) | Accent=hsl(49, 90%, 60%)
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: "Segoe UI", Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; color: #1e293b; }
                .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
                .header { background-color: hsl(223, 100%, 55%); padding: 30px 20px; text-align: center; }
                .header h1 { color: #ffffff; margin: 0; font-size: 26px; font-weight: bold; }
                .header p { color: hsl(49, 90%, 60%); margin: 5px 0 0 0; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
                .content { padding: 30px; }
                .content h2 { color: #1e293b; font-size: 18px; margin-top: 0; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
                .table-container { width: 100%; border-collapse: collapse; margin-top: 20px; }
                .table-container th { text-align: left; padding: 12px; background: #f8fafc; color: #64748b; font-weight: 600; width: 35%; border-bottom: 1px solid #e2e8f0; }
                .table-container td { padding: 12px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-weight: 500; }
                .footer { background: #f8fafc; padding: 20px; text-align: center; color: #64748b; font-size: 12px; border-top: 1px solid #e2e8f0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Nuevo Registro Web</h1>
                    <p>Católica School</p>
                </div>
                <div class="content">
                    <h2>Detalles del prospecto</h2>
                    <table class="table-container">
                        <tr>
                            <th>Nombre Completo</th>
                            <td>' . htmlspecialchars($nombre) . '</td>
                        </tr>
                        <tr>
                            <th>Correo Electrónico</th>
                            <td><a href="mailto:' . htmlspecialchars($correo) . '" style="color: hsl(223, 100%, 55%);">' . htmlspecialchars($correo) . '</a></td>
                        </tr>
                        <tr>
                            <th>Teléfono</th>
                            <td>' . htmlspecialchars($telefono) . '</td>
                        </tr>
                        <tr>
                            <th>DNI / Documento</th>
                            <td>' . htmlspecialchars($dni) . '</td>
                        </tr>
                        <tr>
                            <th>Nivel de Interés</th>
                            <td><span style="display:inline-block; padding:4px 10px; background:hsl(49, 90%, 60%); color:#000; border-radius:12px; font-size:13px; font-weight:bold;">' . ucfirst(htmlspecialchars($nivel)) . '</span></td>
                        </tr>
                    </table>
                </div>
                <div class="footer">
                    Este mensaje fue enviado automáticamente desde el formulario de la página web de Católica School.
                </div>
            </div>
        </body>
        </html>';

        // Intentar enviar
        $mail->send();
        
        // Retornar éxito
        echo json_encode(["status" => "success", "message" => "Mensaje enviado."]);
        
    } catch (Exception $e) {
        // En caso de error, devolver detalles (útil para diagnosticar al subirlo a cPanel)
        echo json_encode(["status" => "error", "message" => "El mensaje no pudo ser enviado. Error interno SMTP."]);
        // Para debug interno en logs: error_log("Mailer Error: {$mail->ErrorInfo}");
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>
