<?php
require_once '../includes/db.php';
require_once 'auth.php';
redirect_if_not_logged_in();

// Obtener todas las noticias
$stmt = $pdo->query("SELECT * FROM noticias ORDER BY fecha DESC, created_at DESC");
$noticias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Católica School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="../assets/icono.png" alt="Logo" class="w-10 h-10 mr-3">
                    <span class="text-xl font-bold text-gray-800">Católica Admin</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-gray-500 text-sm">Hola, <?= $_SESSION['username'] ?></span>
                    <a href="logout.php" class="text-sm text-red-600 hover:font-bold">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Noticias</h1>
                <p class="text-gray-500 mt-1">Agrega, edita o elimina las noticias de la web.</p>
            </div>
            <a href="noticia-nueva.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-blue-100 flex items-center gap-2 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Nueva Noticia
            </a>
        </div>

        <!-- Tabla de Noticias -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Título</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($noticias)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">No hay noticias publicadas todavía.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($noticias as $n): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?= date('d/m/Y', strtotime($n['fecha'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900"><?= $n['titulo'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= $n['categoria'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <a href="noticia-editar.php?id=<?= $n['id'] ?>" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                    <a href="noticia-eliminar.php?id=<?= $n['id'] ?>" 
                                       onclick="return confirm('¿Estás seguro de eliminar esta noticia?')"
                                       class="text-red-600 hover:text-red-900">Eliminar</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
