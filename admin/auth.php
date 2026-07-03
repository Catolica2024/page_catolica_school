<?php
if (session_status() === PHP_SESSION_NONE) {
    // Fix para cPanel/hosting compartido donde la ruta de sesiones no existe.
    // Si la ruta por defecto no existe o no es escribible, usamos una carpeta
    // dentro del propio proyecto como fallback.
    $current_path = session_save_path();
    if (!$current_path || !is_dir($current_path) || !is_writable($current_path)) {
        $fallback = dirname(__DIR__) . '/tmp/sessions';
        if (!is_dir($fallback)) {
            @mkdir($fallback, 0700, true);
        }
        if (is_dir($fallback) && is_writable($fallback)) {
            session_save_path($fallback);
        }
    }

    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    session_start();
}

function is_logged_in(): bool {
    return !empty($_SESSION['admin_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
