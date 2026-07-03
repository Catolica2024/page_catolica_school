<?php
require_once 'auth.php';
require_once '../includes/db.php';
require_login();

// Contar estadísticas
$total     = $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
$este_mes  = $pdo->query("SELECT COUNT(*) FROM noticias WHERE MONTH(fecha)=MONTH(NOW()) AND YEAR(fecha)=YEAR(NOW())")->fetchColumn();
$categorias = $pdo->query("SELECT COUNT(DISTINCT categoria) FROM noticias")->fetchColumn();

// Obtener noticias
$search = trim($_GET['q'] ?? '');
$cat_filter = trim($_GET['cat'] ?? '');
$sql = "SELECT * FROM noticias WHERE 1=1";
$params = [];
if ($search)     { $sql .= " AND titulo LIKE ?"; $params[] = "%$search%"; }
if ($cat_filter) { $sql .= " AND categoria = ?"; $params[] = $cat_filter; }
$sql .= " ORDER BY fecha DESC, created_at DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$noticias = $stmt->fetchAll();

$cats_all = $pdo->query("SELECT DISTINCT categoria FROM noticias ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);

$deleted = isset($_GET['deleted']);
$saved   = isset($_GET['saved']);

// Colores por categoría
function cat_color(string $cat): array {
    return match(strtoupper($cat)) {
        'EVENTO'          => ['#F6C94E','#3D2F00'],
        'LOGRO','LOGROS'  => ['#2962FF','#fff'],
        'INFRAESTRUCTURA' => ['#06B6D4','#fff'],
        'ACADÉMICO','ACADEMICO' => ['#8B5CF6','#fff'],
        'COMUNIDAD'       => ['#10B981','#fff'],
        'DEPORTE','DEPORTES' => ['#F97316','#fff'],
        'ANUNCIO'         => ['#EF4444','#fff'],
        default           => ['#64748B','#fff'],
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard · Noticias | Católica School</title>
  <link rel="icon" type="image/png" href="../assets/icono.png?v=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
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
    body {
      font-family:'Inter',system-ui,sans-serif;
      background:var(--bg); color:var(--text-1);
      display:flex; min-height:100vh;
    }

    /* ── SIDEBAR ───────────────────────────────── */
    .sidebar {
      width:var(--sidebar-w); min-height:100vh;
      background:var(--sidebar);
      border-right:1px solid var(--border);
      box-shadow: 2px 0 12px rgba(0,0,0,.05);
      display:flex; flex-direction:column;
      position:fixed; left:0; top:0; bottom:0;
      z-index:100;
    }
    .sidebar-logo {
      padding:28px 24px 20px;
      border-bottom:1px solid var(--border);
      display:flex; align-items:center; gap:12px;
    }
    .sidebar-logo img { width:40px;height:40px;object-fit:contain; }
    .sidebar-logo-text strong { display:block;font-size:13px;font-weight:700;color:var(--text-1); }
    .sidebar-logo-text span   { font-size:11px;color:var(--text-2); }

    .sidebar-nav { flex:1; padding:20px 12px; }
    .nav-label { font-size:10px;font-weight:600;color:var(--text-3);letter-spacing:1px;text-transform:uppercase;padding:0 12px;margin-bottom:8px;margin-top:16px; }
    .nav-item {
      display:flex;align-items:center;gap:10px;
      padding:11px 14px; border-radius:10px;
      color:var(--text-2); font-size:13px; font-weight:500;
      text-decoration:none; transition:all .18s; margin-bottom:2px;
    }
    .nav-item:hover, .nav-item.active {
      background:rgba(41,98,255,.12); color:var(--text-1);
    }
    .nav-item.active { color:#7BA5FF; }
    .nav-item svg { flex-shrink:0;opacity:.7; }
    .nav-item.active svg, .nav-item:hover svg { opacity:1; }

    .sidebar-footer {
      padding:16px 24px;
      border-top:1px solid var(--border);
      display:flex; align-items:center; justify-content:space-between;
    }
    .sidebar-user { display:flex;align-items:center;gap:10px; }
    .user-avatar {
      width:34px;height:34px;border-radius:50%;
      background:linear-gradient(135deg,var(--primary),#8B5CF6);
      display:flex;align-items:center;justify-content:center;
      font-size:13px;font-weight:700;color:#fff;
    }
    .user-name { font-size:12px;font-weight:600;color:var(--text-1); }
    .user-role { font-size:10px;color:var(--text-2); }
    .btn-logout {
      display:flex;align-items:center;gap:6px;
      background:none;border:1px solid var(--border);border-radius:8px;
      color:var(--text-2);font-family:inherit;font-size:11px;font-weight:500;
      padding:6px 10px;cursor:pointer;text-decoration:none;transition:all .18s;
    }
    .btn-logout:hover { border-color:var(--danger);color:var(--danger); }

    /* ── MAIN ──────────────────────────────────── */
    .main { margin-left:var(--sidebar-w); flex:1; display:flex;flex-direction:column; }

    /* Top bar */
    .topbar {
      height:68px; padding:0 32px;
      background:rgba(255,255,255,.92); backdrop-filter:blur(12px);
      border-bottom:1px solid var(--border);
      display:flex;align-items:center;justify-content:space-between;
      position:sticky;top:0;z-index:50;
    }
    .topbar-title { font-size:20px;font-weight:800; }
    .topbar-title span { color:var(--primary); }

    .btn-new {
      display:inline-flex;align-items:center;gap:8px;
      background:var(--primary); color:#fff;
      border:none;border-radius:12px;
      padding:10px 20px;font-family:inherit;font-size:13px;font-weight:700;
      text-decoration:none;cursor:pointer;
      box-shadow:0 6px 20px rgba(41,98,255,.35);
      transition:all .2s;
    }
    .btn-new:hover { background:var(--primary-h);transform:translateY(-1px);box-shadow:0 10px 28px rgba(41,98,255,.45); }

    /* Content */
    .content { padding:28px 32px; flex:1; }

    /* Toast */
    .toast {
      position:fixed;top:80px;right:28px;z-index:999;
      display:flex;align-items:center;gap:10px;
      background:var(--card);border:1px solid var(--border);
      border-radius:12px;padding:14px 20px;
      font-size:13px;font-weight:500;
      box-shadow:0 12px 40px rgba(0,0,0,.5);
      animation: slideIn .3s cubic-bezier(.34,1.56,.64,1) both, fadeOut .3s ease 3s forwards;
    }
    .toast.success { border-color:rgba(16,212,126,.3); color:var(--success); }
    .toast.danger  { border-color:rgba(255,77,91,.3);  color:var(--danger); }
    @keyframes slideIn { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }
    @keyframes fadeOut { to{opacity:0;transform:translateX(20px)} }

    /* Stats */
    .stats { display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px; }
    .stat-card {
      background:var(--card);border:1px solid var(--border);
      border-radius:var(--r);padding:24px;
      display:flex;align-items:center;gap:18px;
      box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
      transition:box-shadow .2s;
    }
    .stat-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.1); }
    .stat-icon {
      width:48px;height:48px;border-radius:12px;
      display:flex;align-items:center;justify-content:center;flex-shrink:0;
    }
    .stat-icon.blue   { background:rgba(41,98,255,.15); }
    .stat-icon.gold   { background:rgba(246,201,78,.15); }
    .stat-icon.green  { background:rgba(16,212,126,.15); }
    .stat-val   { font-size:28px;font-weight:800;color:var(--text-1);line-height:1; }
    .stat-label { font-size:12px;color:var(--text-2);margin-top:4px; }

    /* Table section */
    .section-header {
      display:flex;align-items:center;justify-content:space-between;
      margin-bottom:16px;flex-wrap:wrap;gap:12px;
    }
    .section-title { font-size:16px;font-weight:700; }

    /* Filters */
    .filters { display:flex;align-items:center;gap:10px;flex-wrap:wrap; }
    .search-wrap { position:relative; }
    .search-wrap input {
      height:40px;padding:0 14px 0 38px;width:240px;
      background:var(--surface);border:1px solid var(--border);
      border-radius:10px;color:var(--text-1);font-family:inherit;font-size:13px;
      outline:none;transition:border-color .2s;
    }
    .search-wrap input:focus { border-color:var(--primary); }
    .search-icon { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-2);pointer-events:none; }

    .filter-select {
      height:40px;padding:0 12px;
      background:var(--surface);border:1px solid var(--border);
      border-radius:10px;color:var(--text-1);font-family:inherit;font-size:13px;
      outline:none;cursor:pointer;
    }

    /* Table */
    .table-wrap {
      background:var(--card);border:1px solid var(--border);
      border-radius:var(--r);overflow:hidden;
      box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    table { width:100%;border-collapse:collapse; }
    thead th {
      padding:14px 20px;text-align:left;
      font-size:11px;font-weight:600;text-transform:uppercase;
      letter-spacing:.6px;color:var(--text-2);
      background:#F8FAFC;border-bottom:1px solid var(--border);
    }
    tbody tr {
      border-bottom:1px solid rgba(15,23,42,.05);
      transition:background .15s;
    }
    tbody tr:last-child { border-bottom:none; }
    tbody tr:hover { background:#F8FAFF; }
    td { padding:16px 20px;vertical-align:middle; }

    /* Thumbnail */
    .thumb {
      width:56px;height:40px;border-radius:8px;object-fit:cover;
      background:var(--surface);flex-shrink:0;
    }
    .thumb-placeholder {
      width:56px;height:40px;border-radius:8px;
      background:var(--surface);display:flex;align-items:center;justify-content:center;
      color:var(--text-3);
    }
    .news-cell { display:flex;align-items:center;gap:14px; }
    .news-title { font-size:13px;font-weight:600;color:var(--text-1);max-width:280px; white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .news-sub   { font-size:11px;color:var(--text-2);margin-top:2px;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }

    /* Badge categoria */
    .badge {
      display:inline-flex;align-items:center;
      padding:3px 10px;border-radius:20px;
      font-size:10px;font-weight:700;letter-spacing:.3px;text-transform:uppercase;
      white-space:nowrap;
    }

    .date-cell { font-size:12px;color:var(--text-2);white-space:nowrap; }

    /* Actions */
    .actions { display:flex;align-items:center;gap:8px;justify-content:flex-end; }
    .btn-icon {
      width:34px;height:34px;border-radius:9px;border:none;cursor:pointer;
      display:flex;align-items:center;justify-content:center;
      transition:all .18s;flex-shrink:0;
    }
    .btn-icon.edit   { background:rgba(41,98,255,.12);color:#7BA5FF; }
    .btn-icon.edit:hover   { background:rgba(41,98,255,.25);color:#fff; }
    .btn-icon.delete { background:rgba(255,77,91,.1);color:#FF7A85; }
    .btn-icon.delete:hover { background:rgba(255,77,91,.25);color:#fff; }

    /* Empty */
    .empty {
      text-align:center;padding:60px 20px;color:var(--text-2);
    }
    .empty svg { opacity:.3;margin:0 auto 16px; }

    /* ── MODAL ─────────────────────────────────── */
    .modal-overlay {
      display:none;position:fixed;inset:0;z-index:999;
      background:rgba(0,0,0,.45);backdrop-filter:blur(6px);
      align-items:center;justify-content:center;padding:20px;
    }
    .modal-overlay.open { display:flex; }
    .modal {
      background:var(--card);border:1px solid var(--border);
      border-radius:24px;padding:40px;max-width:420px;width:100%;
      box-shadow:0 20px 60px rgba(0,0,0,.15);
      animation:rise .35s cubic-bezier(.34,1.56,.64,1) both;
    }
    @keyframes rise { from{opacity:0;transform:scale(.9)} to{opacity:1;transform:scale(1)} }
    .modal-icon {
      width:60px;height:60px;border-radius:16px;
      background:rgba(255,77,91,.12);display:flex;align-items:center;justify-content:center;
      margin-bottom:20px;
    }
    .modal-title  { font-size:20px;font-weight:800;margin-bottom:10px; }
    .modal-body   { font-size:13px;color:var(--text-2);line-height:1.6;margin-bottom:28px; }
    .modal-news   { font-weight:600;color:var(--text-1); }
    .modal-actions { display:flex;gap:12px; }
    .btn-cancel {
      flex:1;height:46px;border:1px solid var(--border);border-radius:11px;
      background:none;color:var(--text-2);font-family:inherit;font-size:14px;font-weight:600;
      cursor:pointer;transition:all .18s;
    }
    .btn-cancel:hover { border-color:var(--text-2);color:var(--text-1); }
    .btn-confirm-delete {
      flex:1;height:46px;border:none;border-radius:11px;
      background:var(--danger);color:#fff;font-family:inherit;font-size:14px;font-weight:700;
      cursor:pointer;transition:all .18s;
      box-shadow:0 6px 20px rgba(255,77,91,.35);
    }
    .btn-confirm-delete:hover { filter:brightness(1.1); }
  </style>
</head>
<body>

<!-- ── SIDEBAR ───────────────────────────── -->
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
    <a href="logout.php" class="btn-logout" title="Cerrar sesión">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Salir
    </a>
  </div>
</aside>

<!-- ── MAIN ──────────────────────────────── -->
<main class="main">
  <div class="topbar">
    <div class="topbar-title">Gestión de <span>Noticias</span></div>
    <a href="noticia-nueva.php" class="btn-new">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nueva Noticia
    </a>
  </div>

  <div class="content">

    <!-- Toasts -->
    <?php if ($deleted): ?>
    <div class="toast danger">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
      Noticia eliminada correctamente.
    </div>
    <?php elseif ($saved): ?>
    <div class="toast success">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      Noticia guardada correctamente.
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats">
      <div class="stat-card">
        <div class="stat-icon blue">
          <svg width="22" height="22" fill="none" stroke="#2962FF" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div>
          <div class="stat-val"><?= $total ?></div>
          <div class="stat-label">Noticias totales</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon gold">
          <svg width="22" height="22" fill="none" stroke="#F6C94E" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
          <div class="stat-val"><?= $este_mes ?></div>
          <div class="stat-label">Publicadas este mes</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">
          <svg width="22" height="22" fill="none" stroke="#10D47E" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        </div>
        <div>
          <div class="stat-val"><?= $categorias ?></div>
          <div class="stat-label">Categorías activas</div>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="section-header">
      <div class="section-title">Todas las noticias</div>
      <form method="GET" class="filters">
        <div class="search-wrap">
          <svg class="search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" name="q" placeholder="Buscar por título…" value="<?= htmlspecialchars($search) ?>">
        </div>
        <select name="cat" class="filter-select" onchange="this.form.submit()">
          <option value="">Todas las categorías</option>
          <?php foreach ($cats_all as $c): ?>
          <option value="<?= htmlspecialchars($c) ?>" <?= $cat_filter === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if ($search || $cat_filter): ?>
        <a href="index.php" style="font-size:12px;color:var(--text-2);text-decoration:none;align-self:center;padding:0 4px;">✕ Limpiar</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:80px">Imagen</th>
            <th>Noticia</th>
            <th>Categoría</th>
            <th>Fecha</th>
            <th style="text-align:right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($noticias)): ?>
          <tr><td colspan="5">
            <div class="empty">
              <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              <p>No hay noticias <?= $search || $cat_filter ? 'con esos filtros' : 'publicadas aún' ?></p>
            </div>
          </td></tr>
          <?php else: ?>
          <?php foreach ($noticias as $n):
            [$bg, $fg] = cat_color($n['categoria']);
          ?>
          <tr>
            <td>
              <?php if ($n['imagen']): ?>
              <img class="thumb" src="../<?= htmlspecialchars($n['imagen']) ?>" alt="">
              <?php else: ?>
              <div class="thumb-placeholder">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              </div>
              <?php endif; ?>
            </td>
            <td>
              <div class="news-cell">
                <div>
                  <div class="news-title"><?= htmlspecialchars($n['titulo']) ?></div>
                  <div class="news-sub"><?= htmlspecialchars($n['subtitulo'] ?? '') ?></div>
                </div>
              </div>
            </td>
            <td>
              <span class="badge" style="background:<?= $bg ?>;color:<?= $fg ?>"><?= htmlspecialchars($n['categoria']) ?></span>
            </td>
            <td>
              <div class="date-cell"><?= date('d/m/Y', strtotime($n['fecha'])) ?></div>
            </td>
            <td>
              <div class="actions">
                <a href="noticia-editar.php?id=<?= $n['id'] ?>" class="btn-icon edit" title="Editar">
                  <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <button class="btn-icon delete" title="Eliminar"
                  onclick="confirmDelete(<?= $n['id'] ?>, <?= htmlspecialchars(json_encode($n['titulo']), ENT_QUOTES, 'UTF-8') ?>)">
                  <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div><!-- /content -->
</main>

<!-- ── MODAL DELETE ──────────────────────── -->
<div class="modal-overlay" id="delete-modal">
  <div class="modal">
    <div class="modal-icon">
      <svg width="28" height="28" fill="none" stroke="#FF4D5B" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
    </div>
    <div class="modal-title">Eliminar noticia</div>
    <div class="modal-body">
      ¿Seguro que deseas eliminar <span class="modal-news" id="modal-news-title">"…"</span>?
      <br><br>
      Esta acción es <strong style="color:#FF4D5B">irreversible</strong>. Se eliminará tanto el registro como la imagen del servidor.
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeDelete()">Cancelar</button>
      <a href="#" id="confirm-delete-link" class="btn-confirm-delete" style="display:flex;align-items:center;justify-content:center;text-decoration:none">
        Sí, eliminar
      </a>
    </div>
  </div>
</div>

<script>
  function confirmDelete(id, title) {
    document.getElementById('modal-news-title').textContent = '"' + title + '"';
    document.getElementById('confirm-delete-link').href = 'noticia-eliminar.php?id=' + id;
    document.getElementById('delete-modal').classList.add('open');
  }
  function closeDelete() {
    document.getElementById('delete-modal').classList.remove('open');
  }
  document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDelete();
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDelete(); });

  // Auto-remove toasts
  const toast = document.querySelector('.toast');
  if (toast) setTimeout(() => toast.remove(), 4000);

  // Live search
  const searchInput = document.querySelector('.search-wrap input');
  if (searchInput) {
    let timer;
    searchInput.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(() => searchInput.form.submit(), 500);
    });
  }
</script>
</body>
</html>
