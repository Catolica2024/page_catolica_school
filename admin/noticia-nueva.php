<?php
require_once 'auth.php';
require_once '../includes/db.php';
require_login();

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']    ?? '');
    $subtitulo = trim($_POST['subtitulo'] ?? '');
    $fecha     = $_POST['fecha']     ?? date('Y-m-d');
    $categoria = $_POST['categoria'] ?? 'Noticia';
    $contenido = trim($_POST['contenido'] ?? '');
    $imagen_url = '';

    // Manejo de imagen via Base64 (evita errores de upload_tmp_dir en hostings)
    $imagen_base64 = $_POST['imagen_base64'] ?? '';
    if (!empty($imagen_base64)) {
        if (preg_match('/^data:image\/(\w+);base64,/', $imagen_base64, $type)) {
            $ext = strtolower($type[1]); // jpg, png, webp, jpeg
            if ($ext === 'jpeg') $ext = 'jpg';
            
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $error = "Formato no permitido. Usa JPG, PNG o WEBP.";
            } else {
                $data = substr($imagen_base64, strpos($imagen_base64, ',') + 1);
                $data = base64_decode($data);
                
                if ($data === false) {
                    $error = "La imagen subida es inválida o corrupta.";
                } else {
                    $upload_dir = dirname(__DIR__) . '/assets/uploads/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    $new_name = uniqid('news_') . '.' . $ext;
                    if (file_put_contents($upload_dir . $new_name, $data) !== false) {
                        @chmod($upload_dir . $new_name, 0644);
                        $imagen_url = 'assets/uploads/' . $new_name;
                    } else {
                        $error = "Error al guardar la imagen en el servidor.";
                    }
                }
            }
        } else {
            $error = "Los datos de la imagen no son válidos.";
        }
    }

    if (!$error) {
        if (empty($titulo) || empty($fecha)) {
            $error = "El título y la fecha son obligatorios.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO noticias (titulo, subtitulo, fecha, categoria, imagen, contenido) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$titulo, $subtitulo, $fecha, $categoria, $imagen_url, $contenido]);
                header('Location: index.php?saved=1');
                exit;
            } catch (Exception $e) {
                $error = "Error al guardar: " . $e->getMessage();
            }
        }
    }
}

$cats = ['Noticia','Evento','Logro','Infraestructura','Académico','Comunidad','Deporte','Anuncio'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva Noticia · Panel | Católica School</title>
  <link rel="icon" type="image/png" href="../assets/icono.png?v=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <style>
    /* Estilos personalizados para Flatpickr */
    .flatpickr-calendar {
      border-radius: 12px !important;
      box-shadow: 0 10px 25px rgba(0,0,0,0.06) !important;
      border: 1px solid rgba(15,23,42,.08) !important;
      font-family: 'Inter', sans-serif;
      background: #ffffff !important;
    }
    .flatpickr-day.selected, .flatpickr-day.selected:focus, .flatpickr-day.selected:hover {
      background: #2962FF !important;
      border-color: #2962FF !important;
      color: #fff !important;
    }
    .flatpickr-months .flatpickr-month {
      color: #0F172A !important;
      fill: #0F172A !important;
    }
    .flatpickr-current-month .numInputWrapper span.arr.up:after { border-bottom-color: #0F172A !important; }
    .flatpickr-current-month .numInputWrapper span.arr.down:after { border-top-color: #0F172A !important; }
    .flatpickr-day:hover { background: #EEF2FF !important; }
    
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg:       #F5F7FA;
      --sidebar:  #FFFFFF;
      --card:     #FFFFFF;
      --surface:  #F1F5F9;
      --border:   rgba(15,23,42,.09);
      --primary:  #2962FF;
      --primary-h:#1E4BCC;
      --gold:     #F59E0B;
      --success:  #10B981;
      --danger:   #EF4444;
      --text-1:   #0F172A;
      --text-2:   #64748B;
      --text-3:   #94A3B8;
      --sidebar-w:260px;
      --r:        14px;
    }
    html,body { height:100%; }
    body { font-family:'Inter',system-ui,sans-serif; background:var(--bg); color:var(--text-1); display:flex; min-height:100vh; }

    /* ── SIDEBAR ── */
    .sidebar { width:var(--sidebar-w);min-height:100vh;background:var(--sidebar);border-right:1px solid var(--border);box-shadow:2px 0 12px rgba(0,0,0,.05);display:flex;flex-direction:column;position:fixed;left:0;top:0;bottom:0;z-index:100; }
    .sidebar-logo { padding:28px 24px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:12px; }
    .sidebar-logo img { width:40px;height:40px;object-fit:contain; }
    .sidebar-logo-text strong { display:block;font-size:13px;font-weight:700;color:var(--text-1); }
    .sidebar-logo-text span   { font-size:11px;color:var(--text-2); }
    .sidebar-nav { flex:1; padding:20px 12px; }
    .nav-label { font-size:10px;font-weight:600;color:var(--text-3);letter-spacing:1px;text-transform:uppercase;padding:0 12px;margin-bottom:8px;margin-top:16px; }
    .nav-item { display:flex;align-items:center;gap:10px; padding:11px 14px; border-radius:10px; color:var(--text-2); font-size:13px; font-weight:500; text-decoration:none; transition:all .18s; margin-bottom:2px; }
    .nav-item:hover, .nav-item.active { background:rgba(41,98,255,.12); color:var(--text-1); }
    .nav-item.active { color:#7BA5FF; }
    .nav-item svg { flex-shrink:0;opacity:.7; }
    .nav-item.active svg, .nav-item:hover svg { opacity:1; }
    .sidebar-footer { padding:16px 24px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .sidebar-user { display:flex;align-items:center;gap:10px; }
    .user-avatar { width:34px;height:34px;border-radius:50%; background:linear-gradient(135deg,var(--primary),#8B5CF6); display:flex;align-items:center;justify-content:center; font-size:13px;font-weight:700;color:#fff; }
    .user-name { font-size:12px;font-weight:600;color:var(--text-1); }
    .user-role { font-size:10px;color:var(--text-2); }
    .btn-logout { display:flex;align-items:center;gap:6px; background:none;border:1px solid var(--border);border-radius:8px; color:var(--text-2);font-family:inherit;font-size:11px;font-weight:500; padding:6px 10px;cursor:pointer;text-decoration:none;transition:all .18s; }
    .btn-logout:hover { border-color:var(--danger);color:var(--danger); }

    /* ── MAIN ── */
    .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; }
    .topbar { height:68px;padding:0 32px;background:rgba(255,255,255,.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50; }
    .topbar-left { display:flex;align-items:center;gap:14px; }
    .btn-back { display:flex;align-items:center;gap:8px; background:none;border:1px solid var(--border);border-radius:10px; color:var(--text-2);font-family:inherit;font-size:12px;font-weight:600; padding:8px 14px;text-decoration:none;transition:all .18s; }
    .btn-back:hover { border-color:var(--text-2);color:var(--text-1); }
    .topbar-title { font-size:20px;font-weight:800; }
    .topbar-title span { color:var(--primary); }

    /* Content */
    .content { padding:32px; flex:1; }

    /* Alert */
    .alert-error { display:flex;align-items:center;gap:10px; background:rgba(255,77,91,.08); border:1px solid rgba(255,77,91,.22); color:#FF7A85; border-radius:12px; padding:14px 18px; font-size:13px; font-weight:500; margin-bottom:24px; }

    /* Form Grid */
    .form-grid { display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start; }

    /* Card */
    .form-card { background:var(--card);border:1px solid var(--border);border-radius:var(--r);overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06); }
    .form-card-header { padding:20px 24px 16px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
    .form-card-header svg { color:var(--primary); }
    .form-card-header h2 { font-size:15px; font-weight:700; }
    .form-card-body { padding:24px; display:flex; flex-direction:column; gap:20px; }

    /* Fields */
    .field label { display:block; font-size:11px; font-weight:600; color:var(--text-2); margin-bottom:8px; letter-spacing:.5px; text-transform:uppercase; }
    .field input[type=text],
    .field input[type=date],
    .field select,
    .field textarea {
      width:100%; background:#F8FAFC; border:1.5px solid #E2E8F0;
      border-radius:10px; color:var(--text-1); font-family:inherit; font-size:14px;
      padding:12px 14px; outline:none; transition:border-color .2s, box-shadow .2s;
      -webkit-appearance:none;
    }
    .field input[type=date] { color-scheme: dark; }
    .field textarea { min-height:120px; resize:vertical; line-height:1.6; }
    .field input:focus, .field select:focus, .field textarea:focus {
      border-color:var(--primary); box-shadow:0 0 0 3px rgba(41,98,255,.15);
    }
    .field input::placeholder, .field textarea::placeholder { color:var(--text-3); }
    .field .hint { font-size:11px; color:var(--text-3); margin-top:6px; }

    /* Category pills */
    .cat-pills { display:flex; flex-wrap:wrap; gap:8px; }
    .cat-pill { position:relative; }
    .cat-pill input[type=radio] { position:absolute; opacity:0; width:0; height:0; }
    .cat-pill label {
      display:inline-block; padding:6px 14px; border-radius:20px;
      border:1.5px solid var(--border); font-size:12px; font-weight:600;
      color:var(--text-2); cursor:pointer; transition:all .18s; text-transform:none; letter-spacing:0;
    }
    .cat-pill input:checked + label { background:var(--primary); border-color:var(--primary); color:#fff; }
    .cat-pill label:hover { border-color:var(--primary); color:var(--text-1); }

    /* Row */
    .field-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }

    /* Image upload zone */
    .upload-zone{
      border:2px dashed #CBD5E1; border-radius:12px;
      padding:32px 20px; text-align:center; cursor:pointer;
      transition:all .25s; background:#F8FAFC;
      position:relative;
    }
    .upload-zone.drag-over{border-color:var(--primary);background:rgba(41,98,255,.05);}
    .upload-zone input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .upload-icon { color:var(--text-2); margin:0 auto 12px; }
    .upload-text { font-size:13px; font-weight:600; color:var(--text-1); margin-bottom:4px; }
    .upload-sub  { font-size:11px; color:var(--text-2); }
    .upload-chip { display:inline-block; background:rgba(41,98,255,.15); color:#7BA5FF; border-radius:6px; padding:2px 8px; font-size:11px; font-weight:600; margin-top:8px; }

    /* Preview */
    .img-preview-wrap { margin-top:16px; border-radius:12px; overflow:hidden; position:relative; display:none; }
    .img-preview-wrap img { width:100%; height:200px; object-fit:cover; display:block; }
    .img-preview-wrap.show { display:block; }
    .preview-remove {
      position:absolute; top:8px; right:8px;
      background:rgba(0,0,0,.7); border:none; border-radius:50%; width:28px; height:28px;
      display:flex; align-items:center; justify-content:center;
      color:#fff; cursor:pointer; font-size:16px; line-height:1; transition:background .18s;
    }
    .preview-remove:hover { background:var(--danger); }

    /* Submit */
    .form-actions { display:flex; gap:12px; padding:0 24px 24px; }
    .btn-save {
      flex:1; height:50px; background:var(--primary); border:none; border-radius:12px;
      color:#fff; font-family:inherit; font-size:14px; font-weight:700; cursor:pointer;
      display:flex; align-items:center; justify-content:center; gap:8px;
      box-shadow:0 8px 24px rgba(41,98,255,.35); transition:all .2s;
    }
    .btn-save:hover { background:var(--primary-h); transform:translateY(-1px); box-shadow:0 12px 32px rgba(41,98,255,.45); }
    .btn-discard {
      height:50px; padding:0 20px; background:none; border:1.5px solid var(--border);
      border-radius:12px; color:var(--text-2); font-family:inherit; font-size:14px;
      font-weight:600; cursor:pointer; text-decoration:none; display:flex; align-items:center; transition:all .18s;
    }
    .btn-discard:hover { border-color:var(--text-2); color:var(--text-1); }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <img src="../assets/logo-catolica.png" alt="Católica School">
    <div class="sidebar-logo-text">
      <strong>Católica School</strong>
      <span>Panel Admin</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Gestión</div>
    <a href="index.php" class="nav-item">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>
    <a href="noticia-nueva.php" class="nav-item active">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nueva Noticia
    </a>
    <div class="nav-label">Sistema</div>
    <a href="../pages/noticias.php" target="_blank" class="nav-item">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      Ver página web
    </a>
  </nav>
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="user-avatar"><?= strtoupper(substr($_SESSION['admin_user'], 0, 1)) ?></div>
      <div>
        <div class="user-name"><?= htmlspecialchars($_SESSION['admin_user']) ?></div>
        <div class="user-role">Administrador</div>
      </div>
    </div>
    <a href="logout.php" class="btn-logout">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Salir
    </a>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <div class="topbar">
    <div class="topbar-left">
      <a href="index.php" class="btn-back">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver
      </a>
      <div class="topbar-title">Nueva <span>Noticia</span></div>
    </div>
  </div>

  <div class="content">
    <?php if ($error): ?>
    <div class="alert-error">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-grid">

        <!-- Columna izquierda: datos -->
        <div>
          <!-- Datos principales -->
          <div class="form-card" style="margin-bottom:20px">
            <div class="form-card-header">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              <h2>Datos de la Noticia</h2>
            </div>
            <div class="form-card-body">
              <div class="field">
                <label for="titulo">Título *</label>
                <input type="text" id="titulo" name="titulo" placeholder="Ej: Open Day 2026: Ven a Conocernos" required value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
              </div>
              <div class="field">
                <label for="subtitulo">Resumen / Subtítulo</label>
                <textarea id="subtitulo" name="subtitulo" placeholder="Párrafo corto que se mostrará en la tarjeta de la noticia…" style="min-height:90px"><?= htmlspecialchars($_POST['subtitulo'] ?? '') ?></textarea>
                <div class="hint">Máximo 2-3 oraciones. Aparece en la vista de tarjetas.</div>
              </div>
              <div class="field-row">
                <div class="field">
                  <label for="fecha">Fecha de la Noticia *</label>
                  <input type="text" id="fecha" name="fecha" value="<?= htmlspecialchars($_POST['fecha'] ?? date('Y-m-d')) ?>" required placeholder="Seleccionar fecha...">
                </div>
                <div class="field">
                  <label>Categoría</label>
                  <select name="categoria" id="categoria">
                    <?php foreach ($cats as $c): ?>
                    <option value="<?= $c ?>" <?= (($_POST['categoria'] ?? 'Noticia') === $c) ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Contenido -->
          <div class="form-card">
            <div class="form-card-header">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
              <h2>Contenido Completo</h2>
            </div>
            <div class="form-card-body">
              <div class="field">
                <label for="contenido">Desarrollo de la noticia</label>
                <textarea id="contenido" name="contenido" placeholder="Escribe el contenido completo aquí. Se mostrará en el modal al abrir la noticia.&#10;&#10;Puedes usar saltos de línea para separar párrafos." style="min-height:220px"><?= htmlspecialchars($_POST['contenido'] ?? '') ?></textarea>
                <div class="hint">Se mostrará al hacer clic en "Leer más" desde la página de noticias.</div>
              </div>
            </div>
            <div class="form-actions">
              <a href="index.php" class="btn-discard">Cancelar</a>
              <button type="submit" class="btn-save">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Publicar Noticia
              </button>
            </div>
          </div>
        </div>

        <!-- Columna derecha: imagen -->
        <div>
          <div class="form-card">
            <div class="form-card-header">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              <h2>Imagen Destacada</h2>
            </div>
            <div class="form-card-body">
              <div class="upload-zone" id="upload-zone">
                <input type="file" id="img-input" accept="image/jpeg,image/png,image/webp">
                <input type="hidden" name="imagen_base64" id="imagen_base64">
                <div id="upload-placeholder">
                  <svg class="upload-icon" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                  <div class="upload-text">Arrastra tu imagen aquí</div>
                  <div class="upload-sub">o haz clic para seleccionar</div>
                  <div class="upload-chip">JPG · PNG · WEBP · max 20MB</div>
                </div>
              </div>

              <div class="img-preview-wrap" id="img-preview-wrap">
                <img id="img-preview" src="" alt="Vista previa">
                <button type="button" class="preview-remove" id="preview-remove" title="Quitar imagen">✕</button>
              </div>

              <div class="hint" style="margin-top:12px">La imagen se mostrará en la tarjeta de la noticia y en el modal de detalle. Tamaño recomendado: 800×500px.</div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</main>

<script>
  const imgInput  = document.getElementById('img-input');
  const zone      = document.getElementById('upload-zone');
  const wrap      = document.getElementById('img-preview-wrap');
  const preview   = document.getElementById('img-preview');
  const placeholder = document.getElementById('upload-placeholder');
  const removeBtn = document.getElementById('preview-remove');

  const base64Input = document.getElementById('imagen_base64');

  function showPreview(file) {
    if (!file || !file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      base64Input.value = e.target.result;
      wrap.classList.add('show');
      placeholder.style.display = 'none';
      zone.style.padding = '0';
      zone.style.border = 'none';
    };
    reader.readAsDataURL(file);
  }

  imgInput.addEventListener('change', () => { if (imgInput.files[0]) showPreview(imgInput.files[0]); });

  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) { showPreview(file); imgInput.files = e.dataTransfer.files; }
  });

  removeBtn.addEventListener('click', () => {
    imgInput.value = '';
    base64Input.value = '';
    wrap.classList.remove('show');
    placeholder.style.display = '';
    zone.style.padding = '';
    zone.style.border = '';
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    flatpickr("#fecha", {
      locale: "es",
      dateFormat: "Y-m-d",
      allowInput: true
    });
  });
</script>
</body>
</html>
