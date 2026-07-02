<?php
$session_dir = dirname(dirname(__DIR__)) . '/sessions'; // Fuera del webroot
if (!is_dir($session_dir)) {
    @mkdir($session_dir, 0700, true);
}

if (!is_dir($session_dir) || !is_writable($session_dir)) {
    $session_dir = __DIR__ . '/sessions'; // Dentro de admin/sessions
    if (!is_dir($session_dir)) {
        @mkdir($session_dir, 0700, true);
        @file_put_contents($session_dir . '/.htaccess', "Deny from all\n");
    }
}

if (is_dir($session_dir) && is_writable($session_dir)) {
    session_save_path($session_dir);
}
session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}
