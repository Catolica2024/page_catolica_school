<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir los archivos de la libreria PHPMailer
require 'PHPMailer-6.9.1/src/Exception.php';
require 'PHPMailer-6.9.1/src/PHPMailer.php';
require 'PHPMailer-6.9.1/src/SMTP.php';

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recibir y sanear datos del formulario
    $nombre   = trim($_POST['nombre']   ?? '');
    $correo   = trim($_POST['correo']   ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $dni      = trim($_POST['dni']      ?? '');
    $nivel    = trim($_POST['nivel']    ?? '');

    // Validacion basica en el backend
    if (empty($nombre) || empty($correo)) {
        echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios."]);
        exit;
    }

    // ── Correo SMTP via PHPMailer ────────────────────────────────────────────
    // (Google Sheets lo maneja el JS del navegador directamente)
    $mail = new PHPMailer(true);

    try {
        // Configuracion del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'catolicaschool.edu.pe';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'notiweb@catolicaschool.edu.pe';
        $mail->Password   = 'Cato2026Web';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';

        // Remitente y destinatario
        $mail->setFrom('notiweb@catolicaschool.edu.pe', 'Web Catolica School');
        $mail->addAddress('admision@colegiolacatolica.edu.pe', 'Admisiones Catolica School');
        $mail->addReplyTo($correo, $nombre);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Nuevo registro web: ' . $nombre . ' - Nivel ' . ucfirst($nivel);

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
                    <p>Catolica School — Admision 2027</p>
                </div>
                <div class="content">
                    <h2>Detalles del prospecto</h2>
                    <table class="table-container">
                        <tr>
                            <th>Nombre Completo</th>
                            <td>' . htmlspecialchars($nombre) . '</td>
                        </tr>
                        <tr>
                            <th>Correo Electronico</th>
                            <td><a href="mailto:' . htmlspecialchars($correo) . '" style="color: hsl(223, 100%, 55%);">' . htmlspecialchars($correo) . '</a></td>
                        </tr>
                        <tr>
                            <th>Telefono</th>
                            <td>' . htmlspecialchars($telefono) . '</td>
                        </tr>
                        <tr>
                            <th>DNI / Documento</th>
                            <td>' . htmlspecialchars($dni) . '</td>
                        </tr>
                        <tr>
                            <th>Nivel de Interes</th>
                            <td><span style="display:inline-block; padding:4px 10px; background:hsl(49, 90%, 60%); color:#000; border-radius:12px; font-size:13px; font-weight:bold;">' . ucfirst(htmlspecialchars($nivel)) . '</span></td>
                        </tr>
                    </table>
                </div>
                <div class="footer">
                    Este mensaje fue enviado automaticamente desde el formulario de la pagina web de Catolica School.
                </div>
            </div>
        </body>
        </html>';

        $mail->send();

        echo json_encode(["status" => "success", "message" => "Mensaje enviado."]);

    } catch (Exception $e) {
        error_log("PHPMailer SMTP Error: " . $mail->ErrorInfo);
        echo json_encode(["status" => "error", "message" => "El mensaje no pudo ser enviado. Error interno SMTP."]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Metodo no permitido."]);
}
?>
