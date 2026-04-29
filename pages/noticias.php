<?php
require_once '../includes/db.php';

// Obtener noticias de la DB
$stmt = $pdo->query("SELECT * FROM noticias ORDER BY fecha DESC, created_at DESC");
$noticias_db = $stmt->fetchAll();

// Si no hay noticias en la DB, podemos mostrar unas por defecto o dejarlo vacío
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Noticias y Eventos | Colegio en Carabayllo - Católica School</title>
  <meta name="description" content="Mantente informado sobre los logros, eventos y novedades de Católica School en Carabayllo. ¡Conoce lo que sucede en nuestra comunidad educativa!">
  <link rel="icon" href="../assets/icono.png">
  <link rel="stylesheet" href="../css/styles.css">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://catolicaschool.edu.pe/pages/noticias.php">
  <meta property="og:title" content="Noticias | Católica School Carabayllo">
  <meta property="og:description" content="Lo último de nuestra comunidad educativa en Carabayllo.">
  <meta property="og:image" content="https://catolicaschool.edu.pe/assets/hero-school.jpg">

  <script src="../js/preloader.js"></script>
</head>
<body class="font-body bg-background text-foreground">
  <div id="partial-banner"></div>
  <div id="partial-navbar"></div>

  <main>

    <section class="bg-primary py-20 relative overflow-hidden">
      <div class="absolute inset-0 opacity-90" style="background:linear-gradient(135deg, hsl(var(--primary)), hsl(var(--primary)), hsl(var(--foreground)/.3));"></div>
      <div class="absolute top-0 right-0 w-96 h-96 bg-secondary/20 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
      <div class="container mx-auto px-4 relative text-center">
        <h1 class="font-heading text-4xl lg:text-5xl font-black text-primary-foreground mb-4 animate-fade-in-up">Noticias</h1>
        <p class="text-primary-foreground/80 text-lg max-w-2xl mx-auto animate-fade-in-up">Entérate de las últimas novedades de Católica School</p>
      </div>
    </section>

    <section class="py-20">
      <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($noticias_db)): ?>
                <!-- Si la DB está vacía, mostramos las noticias estáticas iniciales -->
                <article class="reveal bg-card rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-border group">
                  <div class="h-44 overflow-hidden">
                    <img src="../assets/news-1.jpg" alt="Open Day 2026" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                  </div>
                  <div class="p-6">
                    <div class="flex items-center gap-3 mb-3">
                      <span class="text-xs font-bold px-3 py-1 rounded-full bg-accent text-accent-foreground">Evento</span>
                      <span class="text-xs text-muted-foreground flex items-center gap-1">25 Oct 2026</span>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-foreground mb-2 group-hover:text-primary transition-colors">Open Day 2026: Ven a Conocernos</h3>
                    <p class="text-muted-foreground text-sm leading-relaxed">Te invitamos a nuestro Open Day donde podrás recorrer nuestras instalaciones.</p>
                  </div>
                </article>
                <!-- ... (podrías poner más aquí o simplemente un mensaje) -->
            <?php else: ?>
                <?php foreach ($noticias_db as $n): ?>
                <article class="reveal bg-card rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-border group">
                  <div class="h-44 overflow-hidden bg-gray-100">
                    <?php if ($n['imagen']): ?>
                        <img src="../<?= $n['imagen'] ?>" alt="<?= htmlspecialchars($n['titulo']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    <?php endif; ?>
                  </div>
                  <div class="p-6">
                    <div class="flex items-center gap-3 mb-3">
                      <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-100 text-blue-800"><?= htmlspecialchars($n['categoria']) ?></span>
                      <span class="text-xs text-muted-foreground flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?= date('d M Y', strtotime($n['fecha'])) ?>
                      </span>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-foreground mb-2 group-hover:text-primary transition-colors"><?= htmlspecialchars($n['titulo']) ?></h3>
                    <p class="text-muted-foreground text-sm leading-relaxed"><?= htmlspecialchars($n['subtitulo']) ?></p>
                  </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
      </div>
    </section>

  </main>

  <div id="partial-footer"></div>
  <div id="partial-whatsapp"></div>
  <div id="partial-social"></div>

  <script src="../js/partials.js"></script>
  <script src="../js/main.js"></script>
</body>
</html>
