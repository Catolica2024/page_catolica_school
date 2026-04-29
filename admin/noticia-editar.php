<?php
require_once '../includes/db.php';
require_once 'auth.php';
redirect_if_not_logged_in();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

// Obtener datos actuales
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    die("Noticia no encontrada.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $subtitulo = $_POST['subtitulo'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    $imagen_url = $noticia['imagen']; // Por defecto la anterior
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_name = uniqid() . '.' . $ext;
            $upload_dir = '../assets/uploads/';
            
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $new_name)) {
                $imagen_url = 'assets/uploads/' . $new_name;
            }
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, subtitulo = ?, fecha = ?, categoria = ?, imagen = ?, contenido = ? WHERE id = ?");
        $stmt->execute([$titulo, $subtitulo, $fecha, $categoria, $imagen_url, $contenido, $id]);
        $success = "Noticia actualizada correctamente.";
        
        // Recargar datos
        $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
        $stmt->execute([$id]);
        $noticia = $stmt->fetch();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Noticia | Católica School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="index.php" class="text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Volver
            </a>
            <span class="font-bold">Editar Noticia</span>
            <div class="w-10"></div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-10">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Título</label>
                        <input type="text" name="titulo" value="<?= htmlspecialchars($noticia['titulo']) ?>" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Categoría</label>
                        <select name="categoria" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Noticia" <?= $noticia['categoria'] === 'Noticia' ? 'selected' : '' ?>>Noticia</option>
                            <option value="Evento" <?= $noticia['categoria'] === 'Evento' ? 'selected' : '' ?>>Evento</option>
                            <option value="Logro" <?= $noticia['categoria'] === 'Logro' ? 'selected' : '' ?>>Logro</option>
                            <option value="Académico" <?= $noticia['categoria'] === 'Académico' ? 'selected' : '' ?>>Académico</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Fecha</label>
                        <input type="date" name="fecha" value="<?= $noticia['fecha'] ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Imagen (Dejar vacío para no cambiar)</label>
                    <?php if ($noticia['imagen']): ?>
                        <img src="../<?= $noticia['imagen'] ?>" class="w-32 h-20 object-cover rounded-lg mb-2">
                    <?php endif; ?>
                    <input type="file" name="imagen" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Resumen</label>
                    <textarea name="subtitulo" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-gray-300"><?= htmlspecialchars($noticia['subtitulo']) ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Contenido</label>
                    <textarea name="contenido" rows="6" class="w-full px-4 py-2.5 rounded-xl border border-gray-300"><?= htmlspecialchars($noticia['contenido']) ?></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg">
                    Guardar Cambios
                </button>
            </form>
        </div>
    </main>
</body>
</html>
