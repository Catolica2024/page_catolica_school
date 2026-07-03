<?php
require_once "auth.php";
require_once "../includes/db.php";
require_login();

$id = (int)($_GET["id"] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT imagen FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $noticia = $stmt->fetch();
    if ($noticia) {
        if ($noticia["imagen"]) {
            $ruta = dirname(__DIR__) . "/" . $noticia["imagen"];
            if (file_exists($ruta)) @unlink($ruta);
        }
        $pdo->prepare("DELETE FROM noticias WHERE id = ?")->execute([$id]);
    }
}
header("Location: index.php?deleted=1");
exit;
