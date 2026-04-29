<?php
require_once '../includes/db.php';
require_once 'auth.php';
redirect_if_not_logged_in();

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Opcional: podrías eliminar el archivo físico de la imagen aquí
        $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
        $stmt->execute([$id]);
    } catch (Exception $e) {
        // Manejar error
    }
}

header("Location: index.php");
exit();
?>
