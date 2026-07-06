<?php
require_once 'auth.php';
require_once '../includes/db.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();
if (!$noticia) { header('Location: index.php'); exit; }

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']    ?? '');
    $subtitulo = trim($_POST['subtitulo'] ?? '');
    $fecha     = $_POST['fecha']     ?? date('Y-m-d');
    $categoria = $_POST['categoria'] ?? 'Noticia';
    $contenido = trim($_POST['contenido'] ?? '');
    $imagen_url = $noticia['imagen']; // Mantener la imagen existente por defecto

    // Nueva imagen subida via Base64 (evita errores de upload_tmp_dir en hostings)
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
                        // Borrar imagen anterior si la hay
                        if ($noticia['imagen']) {
                            $old_path = dirname(__DIR__) . '/' . $noticia['imagen'];
                            if (file_exists($old_path)) @unlink($old_path);
                        }
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

    // ¿Se pidió eliminar la imagen? (Actualizado para el esquema Base64)
    if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1' && empty($imagen_base64)) {
        if ($noticia['imagen']) {
            $old_path = dirname(__DIR__) . '/' . $noticia['imagen'];
            if (file_exists($old_path)) @unlink($old_path);
        }
        $imagen_url = '';
    }

    if (!$error) {
        if (empty($titulo) || empty($fecha)) {
            $error = "El título y la fecha son obligatorios.";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE noticias SET titulo=?, subtitulo=?, fecha=?, categoria=?, imagen=?, contenido=?, updated_at=NOW() WHERE id=?");
                $stmt->execute([$titulo, $subtitulo, $fecha, $categoria, $imagen_url, $contenido, $id]);
                header('Location: index.php?saved=1');
                exit;
            } catch (Exception $e) {
                $error = "Error al actualizar: " . $e->getMessage();
            }
        }
    }

    // Refrescar datos tras error para mantener los valores del POST en el form
    $noticia = array_merge($noticia, [
        'titulo' => $titulo, 'subtitulo' => $subtitulo, 'fecha' => $fecha,
        'categoria' => $categoria, 'contenido' => $contenido, 'imagen' => $imagen_url,
    ]);
}

$cats = ['Noticia','Evento','Logro','Infraestructura','Académico','Comunidad','Deporte','Anuncio'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Noticia · Panel | Católica School</title>
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
      --bg:#F5F7FA;--sidebar:#FFFFFF;--card:#FFFFFF;--surface:#F1F5F9;
      --border:rgba(15,23,42,.09);--primary:#2962FF;--primary-h:#1E4BCC;
      --gold:#F59E0B;--success:#10B981;--danger:#EF4444;
      --text-1:#0F172A;--text-2:#64748B;--text-3:#94A3B8;
      --sidebar-w:260px;--r:14px;
    }
    html,body{height:100%;}
    body{font-family:'Inter',system-ui,sans-serif;background:var(--bg);color:var(--text-1);display:flex;min-height:100vh;}
    /* Sidebar - identical to noticia-nueva.php */
    .sidebar{width:var(--sidebar-w);min-height:100vh;background:var(--sidebar);border-right:1px solid var(--border);box-shadow:2px 0 12px rgba(0,0,0,.05);display:flex;flex-direction:column;position:fixed;left:0;top:0;bottom:0;z-index:100;}
    .sidebar-logo{padding:28px 24px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;}
    .sidebar-logo img{width:40px;height:40px;object-fit:contain;}
    .sidebar-logo-text strong{display:block;font-size:13px;font-weight:700;color:var(--text-1);}
    .sidebar-logo-text span{font-size:11px;color:var(--text-2);}
    .sidebar-nav{flex:1;padding:20px 12px;}
    .nav-label{font-size:10px;font-weight:600;color:var(--text-3);letter-spacing:1px;text-transform:uppercase;padding:0 12px;margin-bottom:8px;margin-top:16px;}
    .nav-item{display:flex;align-items:center;gap:10px;padding:11px 14px;border-radius:10px;color:var(--text-2);font-size:13px;font-weight:500;text-decoration:none;transition:all .18s;margin-bottom:2px;}
    .nav-item:hover,.nav-item.active{background:rgba(41,98,255,.12);color:var(--text-1);}
    .nav-item.active{color:#7BA5FF;}
    .nav-item svg{flex-shrink:0;opacity:.7;}
    .nav-item.active svg,.nav-item:hover svg{opacity:1;}
    .sidebar-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
    .sidebar-user{display:flex;align-items:center;gap:10px;}
    .user-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--primary),#8B5CF6);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;}
    .user-name{font-size:12px;font-weight:600;color:var(--text-1);}
    .user-role{font-size:10px;color:var(--text-2);}
    .btn-logout{display:flex;align-items:center;gap:6px;background:none;border:1px solid var(--border);border-radius:8px;color:var(--text-2);font-family:inherit;font-size:11px;font-weight:500;padding:6px 10px;cursor:pointer;text-decoration:none;transition:all .18s;}
    .btn-logout:hover{border-color:var(--danger);color:var(--danger);}
    /* Main */
    .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;}
    .topbar{height:68px;padding:0 32px;background:rgba(255,255,255,.92);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
    .topbar-left{display:flex;align-items:center;gap:14px;}
    .btn-back{display:flex;align-items:center;gap:8px;background:none;border:1px solid var(--border);border-radius:10px;color:var(--text-2);font-family:inherit;font-size:12px;font-weight:600;padding:8px 14px;text-decoration:none;transition:all .18s;}
    .btn-back:hover{border-color:var(--text-2);color:var(--text-1);}
    .topbar-title{font-size:20px;font-weight:800;}
    .topbar-title span{color:var(--gold);}
    .content{padding:32px;flex:1;}
    .alert-error{display:flex;align-items:center;gap:10px;background:rgba(255,77,91,.08);border:1px solid rgba(255,77,91,.22);color:#FF7A85;border-radius:12px;padding:14px 18px;font-size:13px;font-weight:500;margin-bottom:24px;}
    .form-grid{display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;}
    .form-card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);overflow:hidden;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.06);}
    .form-card:last-child{margin-bottom:0;}
    .form-card-header{padding:20px 24px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;}
    .form-card-header svg{color:var(--gold);}
    .form-card-header h2{font-size:15px;font-weight:700;}
    .form-card-body{padding:24px;display:flex;flex-direction:column;gap:20px;}
    .field label{display:block;font-size:11px;font-weight:600;color:var(--text-2);margin-bottom:8px;letter-spacing:.5px;text-transform:uppercase;}
    .field input[type=text],.field input[type=date],.field select,.field textarea{width:100%;background:#F8FAFC;border:1.5px solid #E2E8F0;border-radius:10px;color:var(--text-1);font-family:inherit;font-size:14px;padding:12px 14px;outline:none;transition:border-color .2s,box-shadow .2s;-webkit-appearance:none;}
    .field input[type=date]{color-scheme:dark;}
    .field textarea{min-height:120px;resize:vertical;line-height:1.6;}
    .field input:focus,.field select:focus,.field textarea:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(41,98,255,.15);}
    .field input::placeholder,.field textarea::placeholder{color:var(--text-3);}
    .field .hint{font-size:11px;color:var(--text-3);margin-top:6px;}
    .field-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    /* Image section */
    .current-img{border-radius:12px;overflow:hidden;position:relative;margin-bottom:16px;}
    .current-img img{width:100%;height:180px;object-fit:cover;display:block;}
    .current-img-label{font-size:11px;font-weight:600;color:var(--text-2);margin-bottom:8px;letter-spacing:.5px;text-transform:uppercase;}
    .remove-img-row{display:flex;align-items:center;gap:10px;margin-top:10px;}
    .toggle-remove{display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;color:var(--text-2);}
    .toggle-remove input{width:14px;height:14px;accent-color:var(--danger);cursor:pointer;}
    .toggle-remove:hover{color:var(--danger);}
    .divider{text-align:center;color:var(--text-3);font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;margin:12px 0;position:relative;}
    .divider::before,.divider::after{content:'';position:absolute;top:50%;width:calc(50% - 40px);height:1px;background:var(--border);}
    .divider::before{left:0;} .divider::after{right:0;}
    .upload-zone{border:2px dashed #CBD5E1;border-radius:12px;padding:24px 20px;text-align:center;cursor:pointer;transition:all .25s;background:#F8FAFC;position:relative;}
    .upload-zone.drag-over{border-color:var(--primary);background:rgba(41,98,255,.05);}
    .upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
    .upload-icon{color:var(--text-2);margin:0 auto 10px;}
    .upload-text{font-size:13px;font-weight:600;color:var(--text-1);margin-bottom:4px;}
    .upload-sub{font-size:11px;color:var(--text-2);}
    .upload-chip{display:inline-block;background:rgba(41,98,255,.15);color:#7BA5FF;border-radius:6px;padding:2px 8px;font-size:11px;font-weight:600;margin-top:8px;}
    .img-preview-wrap{margin-top:12px;border-radius:12px;overflow:hidden;position:relative;display:none;}
    .img-preview-wrap img{width:100%;height:160px;object-fit:cover;display:block;}
    .img-preview-wrap.show{display:block;}
    .new-img-label{font-size:11px;color:var(--success);font-weight:600;margin-top:8px;text-align:center;}
    /* Actions */
    .form-actions{display:flex;gap:12px;padding:0 24px 24px;}
    .btn-save{flex:1;height:50px;background:var(--primary);border:none;border-radius:12px;color:#fff;font-family:inherit;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 8px 24px rgba(41,98,255,.35);transition:all .2s;}
    .btn-save:hover{background:var(--primary-h);transform:translateY(-1px);box-shadow:0 12px 32px rgba(41,98,255,.45);}
    .btn-discard{height:50px;padding:0 20px;background:none;border:1.5px solid var(--border);border-radius:12px;color:var(--text-2);font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;display:flex;align-items:center;transition:all .18s;}
    .btn-discard:hover{border-color:var(--text-2);color:var(--text-1);}
    /* Gold accent for edit */
    .form-card-header svg.edit-icon{color:var(--gold);}
    .btn-save.gold{background:linear-gradient(135deg,#2962FF,#8B5CF6);box-shadow:0 8px 24px rgba(139,92,246,.3);}
    .btn-save.gold:hover{filter:brightness(1.1);}
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
    <a href="index.php" class="nav-item active">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>
    <a href="noticia-nueva.php" class="nav-item">
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
      <div class="topbar-title">Editar <span>Noticia</span></div>
    </div>
    <div style="font-size:12px;color:var(--text-3)">ID: #<?= $id ?></div>
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

        <!-- Col izquierda: datos -->
        <div>
          <div class="form-card">
            <div class="form-card-header">
              <svg class="edit-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              <h2>Datos de la Noticia</h2>
            </div>
            <div class="form-card-body">
              <div class="field">
                <label for="titulo">Título *</label>
                <input type="text" id="titulo" name="titulo" required value="<?= htmlspecialchars($noticia['titulo']) ?>">
              </div>
              <div class="field">
                <label for="subtitulo">Resumen / Subtítulo</label>
                <textarea id="subtitulo" name="subtitulo" style="min-height:90px"><?= htmlspecialchars($noticia['subtitulo'] ?? '') ?></textarea>
              </div>
              <div class="field-row">
                <div class="field">
                  <label for="fecha">Fecha *</label>
                  <input type="text" id="fecha" name="fecha" value="<?= htmlspecialchars($noticia['fecha']) ?>" required placeholder="Seleccionar fecha...">
                </div>
                <div class="field">
                  <label for="categoria">Categoría</label>
                  <select name="categoria" id="categoria">
                    <?php foreach ($cats as $c): ?>
                    <option value="<?= $c ?>" <?= $noticia['categoria'] === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="form-card">
            <div class="form-card-header">
              <svg class="edit-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
              <h2>Contenido Completo</h2>
            </div>
            <div class="form-card-body">
              <div class="field">
                <label for="contenido">Desarrollo de la noticia</label>
                <textarea id="contenido" name="contenido" style="min-height:220px"><?= htmlspecialchars($noticia['contenido'] ?? '') ?></textarea>
              </div>
            </div>
            <div class="form-actions">
              <a href="index.php" class="btn-discard">Cancelar</a>
              <button type="submit" class="btn-save gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar Cambios
              </button>
            </div>
          </div>
        </div>

        <!-- Col derecha: imagen -->
        <div>
          <div class="form-card">
            <div class="form-card-header">
              <svg class="edit-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              <h2>Imagen Destacada</h2>
            </div>
            <div class="form-card-body">
              <?php if ($noticia['imagen']): ?>
              <div>
                <div class="current-img-label">Imagen actual</div>
                <div class="current-img">
                  <img src="../<?= htmlspecialchars($noticia['imagen']) ?>" alt="Imagen actual">
                </div>
                <label class="toggle-remove">
                  <input type="checkbox" name="remove_image" value="1" id="remove-img-check">
                  Eliminar imagen actual
                </label>
              </div>
              <div class="divider">REEMPLAZAR CON</div>
              <?php endif; ?>

              <div class="upload-zone" id="upload-zone">
                <input type="file" id="img-input" accept="image/jpeg,image/png,image/webp">
                <input type="hidden" name="imagen_base64" id="imagen_base64">
                <div id="upload-placeholder">
                  <svg class="upload-icon" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                  <div class="upload-text">Subir nueva imagen</div>
                  <div class="upload-sub">JPG · PNG · WEBP</div>
                  <div class="upload-chip">Reemplaza la actual</div>
                </div>
              </div>

              <div class="img-preview-wrap" id="img-preview-wrap">
                <img id="img-preview" src="" alt="Vista previa">
              </div>
              <div class="new-img-label" id="new-img-label" style="display:none">✓ Nueva imagen seleccionada</div>

              <div class="hint" style="margin-top:12px">Al guardar con una nueva imagen, la anterior se eliminará permanentemente del servidor.</div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</main>

<script>
  const imgInput    = document.getElementById('img-input');
  const zone        = document.getElementById('upload-zone');
  const wrap        = document.getElementById('img-preview-wrap');
  const preview     = document.getElementById('img-preview');
  const placeholder = document.getElementById('upload-placeholder');
  const newLabel    = document.getElementById('new-img-label');

  const base64Input = document.getElementById('imagen_base64');

  function showPreview(file) {
    if (!file || !file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = e => {
      const img = new Image();
      img.onload = () => {
        // Redimensionar si excede límites recomendados para web (1200px máx)
        const maxW = 1200;
        const maxH = 1200;
        let w = img.width;
        let h = img.height;
        
        if (w > maxW || h > maxH) {
          if (w > h) {
            h = Math.round((h * maxW) / w);
            w = maxW;
          } else {
            w = Math.round((w * maxH) / h);
            h = maxH;
          }
        }
        
        const canvas = document.createElement('canvas');
        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, w, h);
        
        // Exportar a JPG con calidad optimizada de 0.75 (reduce drásticamente el tamaño a ~150-250KB)
        const compressedBase64 = canvas.toDataURL('image/jpeg', 0.75);
        
        preview.src = compressedBase64;
        base64Input.value = compressedBase64;
        wrap.classList.add('show');
        newLabel.style.display = 'block';
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
  imgInput.addEventListener('change', () => { if (imgInput.files[0]) showPreview(imgInput.files[0]); });
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
  zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) { showPreview(file); }
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
