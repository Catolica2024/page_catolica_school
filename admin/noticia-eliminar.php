<?php
require_once '../includes/db.php';
require_once 'auth.php';
redirect_if_not_logged_in();

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Obtener la ruta de la imagen antes de borrar
        $stmt = $pdo->prepare("SELECT imagen FROM noticias WHERE id = ?");
        $stmt->execute([$id]);
        $noticia = $stmt->fetch();

        if ($noticia && $noticia['imagen']) {
            $file_path = '../' . $noticia['imagen'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
        $stmt->execute([$id]);
    } catch (Exception $e) {
        // Manejar error
    }
}

header("Location: index.php");
exit();
?>
