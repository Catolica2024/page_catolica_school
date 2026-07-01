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

  <style>
    /* Premium Modal Styles */
    #news-modal {
      opacity: 0;
      visibility: hidden;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #news-modal.active {
      opacity: 1;
      visibility: visible;
    }
    #news-modal-container {
      transform: scale(0.9) translateY(30px);
      opacity: 0;
      transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    #news-modal.active #news-modal-container {
      transform: scale(1) translateY(0);
      opacity: 1;
    }
    .modal-scroll::-webkit-scrollbar {
      width: 6px;
    }
    .modal-scroll::-webkit-scrollbar-track {
      background: transparent;
    }
    .modal-scroll::-webkit-scrollbar-thumb {
      background: hsl(var(--primary)/.2);
      border-radius: 10px;
    }
    .modal-scroll::-webkit-scrollbar-thumb:hover {
      background: hsl(var(--primary)/.4);
    }
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .line-clamp-3 {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    #news-modal {
      background-color: rgba(3, 7, 18, 0.8) !important;
      backdrop-filter: blur(24px) !important;
      -webkit-backdrop-filter: blur(24px) !important;
    }
    .news-card {
      height: 400px !important;
      display: flex !important;
      flex-direction: column !important;
    }
    .news-card-image {
      height: 180px !important;
      width: 100% !important;
      flex-shrink: 0 !important;
      overflow: hidden !important;
    }
    .news-card-image img {
      transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .news-card:hover .news-card-image img {
      transform: scale(1.1) !important;
    }
    #news-modal.active {
      opacity: 1 !important;
      pointer-events: auto !important;
    }
    #news-modal.active #news-modal-container {
      transform: scale(1) !important;
    }
    body.modal-open #partial-navbar,
    body.modal-open #partial-whatsapp,
    body.modal-open #partial-social,
    body.modal-open #partial-faq {
      visibility: hidden !important;
      opacity: 0 !important;
      pointer-events: none !important;
    }
    .custom-modal-scroll::-webkit-scrollbar {
      width: 6px;
    }
    .custom-modal-scroll::-webkit-scrollbar-track {
      background: transparent;
    }
    .custom-modal-scroll::-webkit-scrollbar-thumb {
      background: #CBD5E1;
      border-radius: 10px;
    }
    .custom-modal-scroll::-webkit-scrollbar-thumb:hover {
      background: #94A3B8;
    }
  </style>
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-KRR6TRJ6EK"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-KRR6TRJ6EK');
</script>
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
                <div class="col-span-full text-center py-20 text-muted-foreground">
                    No hay noticias publicadas en este momento.
                </div>
            <?php else: ?>
                <?php foreach ($noticias_db as $n): ?>
                      <?php 
                        $cat_style = 'background-color: #3b82f6; color: white;';
                        $cat = htmlspecialchars($n['categoria']);
                        $cat_upper = strtoupper($cat);
                        
                        if ($cat_upper === 'EVENTO') {
                            $cat_style = 'background-color: #F6D334; color: #451a03;';
                        } elseif ($cat_upper === 'LOGRO' || $cat_upper === 'LOGROS') {
                            $cat_style = 'background-color: #2962FF; color: white;';
                        } elseif ($cat_upper === 'INFRAESTRUCTURA') {
                            $cat_style = 'background-color: #00BCD4; color: white;';
                        } elseif ($cat_upper === 'ACADÉMICO' || $cat_upper === 'ACADEMICO') {
                            $cat_style = 'background-color: #8b5cf6; color: white;';
                        }
                      ?>
                <article class="reveal bg-card rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-border group cursor-pointer news-card" 
                         data-titulo="<?= htmlspecialchars($n['titulo']) ?>"
                         data-categoria="<?= htmlspecialchars($n['categoria']) ?>"
                         data-fecha="<?= date('d M Y', strtotime($n['fecha'])) ?>"
                         data-imagen="<?= $n['imagen'] ? '../'.$n['imagen'] : '' ?>"
                         data-contenido="<?= htmlspecialchars($n['contenido']) ?>"
                         data-cat-style="<?= $cat_style ?>">
                  <div class="news-card-image overflow-hidden bg-gray-100 relative">
                    <?php if ($n['imagen']): ?>
                        <img src="../<?= $n['imagen'] ?>" alt="<?= htmlspecialchars($n['titulo']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <span class="text-white text-sm font-bold flex items-center gap-2 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            Leer noticia completa 
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </span>
                    </div>
                  </div>
                  <div class="p-6 flex-1 flex flex-col">
                    <div class="flex items-center gap-3 mb-4">
                      <span class="font-bold px-3 py-1 rounded-full flex items-center capitalize" style="<?= $cat_style ?> font-size: 11px;">
                        <?= $cat ?>
                      </span>
                      <span class="text-[10px] text-muted-foreground font-normal flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?= date('d M Y', strtotime($n['fecha'])) ?>
                      </span>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-[#0A2657] mb-2 group-hover:text-primary transition-colors leading-tight line-clamp-2"><?= htmlspecialchars($n['titulo']) ?></h3>
                    <p class="text-muted-foreground text-[13px] leading-relaxed line-clamp-3 mb-4"><?= htmlspecialchars($n['subtitulo']) ?></p>
                    
                    <div class="mt-auto">
                        <span class="text-blue-600 font-bold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                            Leer más 
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </span>
                    </div>
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

  <!-- News Modal -->
  <div id="news-modal" class="fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-[#030712]/80 backdrop-blur-md opacity-0 pointer-events-none transition-all duration-300">
    <button id="close-modal" style="position: fixed !important; top: 40px !important; right: 40px !important; z-index: 1000005 !important; background: #2962FF !important; color: #ffffff !important; width: 55px !important; height: 55px !important; border-radius: 50% !important; border: 2px solid #ffffff !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; box-shadow: 0 15px 45px rgba(0,0,0,0.4) !important; transition: transform 0.3s ease !important;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display: block !important;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </button>

    <div id="news-modal-container" class="bg-card w-full rounded-[3rem] shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden flex flex-col relative scale-95 transition-all duration-500 ease-out" style="max-width: 700px !important; height: 90vh !important; border-radius: 3rem !important; z-index: 999999 !important;">
        <!-- Modal Header Image -->
        <div class="shrink-0 relative overflow-hidden" style="height: 250px !important; border-top-left-radius: 3rem !important; border-top-right-radius: 3rem !important;">
            <img id="modal-image" src="" alt="" class="w-full h-full object-cover">
        </div>

        <!-- Modal Content Body -->
        <div class="p-8 md:p-10 overflow-y-auto flex-1 custom-modal-scroll" style="overflow-y: auto !important;">
            <div class="flex items-center gap-3 mb-4">
                <span id="modal-category" class="font-bold px-3 py-1 rounded-full flex items-center capitalize" style="font-size: 11px;"></span>
                <div class="flex items-center gap-1 text-muted-foreground text-[10px] font-normal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span id="modal-date"></span>
                </div>
            </div>

            <h2 id="modal-title" class="font-heading text-2xl md:text-3xl font-bold text-[#0A2657] mb-6 leading-tight"></h2>
            
            <div id="modal-content" class="text-muted-foreground text-[14px] leading-relaxed space-y-4">
                <!-- Contenido de la BD -->
            </div>
        </div>
    </div>
  </div>

  <script src="../js/partials.js"></script>
  <script src="../js/main.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('news-modal');
        const container = document.getElementById('news-modal-container');
        const closeBtn = document.getElementById('close-modal');
        const newsCards = document.querySelectorAll('.news-card');

        const openModal = (data) => {
            document.getElementById('modal-title').textContent = data.titulo || '';
            document.getElementById('modal-date').textContent = data.fecha || '';
            document.getElementById('modal-content').innerHTML = (data.contenido || '').replace(/\n/g, '<br>');
            
            const categoryEl = document.getElementById('modal-category');
            categoryEl.innerHTML = data.categoria || '';
            categoryEl.setAttribute('style', (data.catStyle || '') + ' font-size: 11px;');
            
            const imgEl = document.getElementById('modal-image');
            if (data.imagen && data.imagen !== '../') {
                imgEl.src = data.imagen;
                imgEl.style.display = 'block';
            } else {
                imgEl.style.display = 'none';
            }

            modal.classList.add('active');
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';
        };

        const closeModal = () => {
            modal.classList.remove('active');
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
        };

        newsCards.forEach(card => {
            card.addEventListener('click', () => {
                const data = {
                    titulo: card.dataset.titulo,
                    categoria: card.dataset.categoria,
                    fecha: card.dataset.fecha,
                    imagen: card.dataset.imagen,
                    contenido: card.dataset.contenido,
                    catStyle: card.dataset.catStyle
                };
                openModal(data);
            });
        });

        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    });
  </script>
</body>
</html>
