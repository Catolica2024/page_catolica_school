<?php
require_once 'auth.php';
require_once '../includes/db.php';

if (is_logged_in()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_user'] = $user['username'];
        header('Location: index.php');
        exit;
    }
    $error = 'Usuario o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acceso · Panel Noticias | Católica School</title>
  <link rel="icon" type="image/png" href="../assets/icono.png?v=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg:        #F0F4FF;
      --card:      #FFFFFF;
      --surface:   #F5F7FA;
      --border:    rgba(15,23,42,.1);
      --primary:   #2962FF;
      --primary-h: #1E4BCC;
      --text-1:    #0F172A;
      --text-2:    #64748B;
      --danger:    #EF4444;
      --gold:      #F59E0B;
      --r:         16px;
    }
    html, body { height: 100%; }
    body {
      font-family: 'Inter', system-ui, sans-serif;
      background: linear-gradient(145deg, #EEF2FF 0%, #F5F7FA 60%, #EFF6FF 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      overflow: hidden;
    }

    /* ── Orbs de fondo ───────────────────────── */
    .orb {
      position: fixed;
      border-radius: 50%;
      filter: blur(80px);
      opacity: .35;
      pointer-events: none;
      animation: float 8s ease-in-out infinite;
    }
    .orb-1 { width:520px;height:520px;background:radial-gradient(circle,rgba(41,98,255,.18) 0%,transparent 70%);top:-160px;left:-160px;animation-delay:0s; }
    .orb-2 { width:400px;height:400px;background:radial-gradient(circle,rgba(139,92,246,.14) 0%,transparent 70%);bottom:-120px;right:-100px;animation-delay:-4s; }
    .orb-3 { width:280px;height:280px;background:radial-gradient(circle,rgba(245,158,11,.18) 0%,transparent 70%);top:40%;left:50%;animation-delay:-2s;opacity:.5; }
    @keyframes float {
      0%,100% { transform: translateY(0) scale(1); }
      50%      { transform: translateY(-24px) scale(1.04); }
    }

    /* ── Tarjeta principal ───────────────────── */
    .card {
      position: relative;
      z-index: 10;
      width: 420px;
      max-width: 95vw;
      background: var(--card);
      border: 1px solid rgba(15,23,42,.08);
      border-radius: 28px;
      padding: 48px 40px;
      box-shadow: 0 4px 6px rgba(0,0,0,.04), 0 20px 60px rgba(41,98,255,.1), 0 0 0 1px rgba(255,255,255,.8);
      animation: rise .5s cubic-bezier(.34,1.56,.64,1) both;
    }
    @keyframes rise {
      from { opacity:0; transform:translateY(32px) scale(.96); }
      to   { opacity:1; transform:translateY(0)   scale(1); }
    }

    /* Logo */
    .logo-wrap {
      display: flex; align-items: center; gap: 12px;
      margin-bottom: 36px;
    }
    .logo-wrap img { width: 44px; height: 44px; object-fit: contain; }
    .logo-text { line-height: 1.1; }
    .logo-text strong { display:block; font-size:15px; font-weight:700; color:var(--text-1); }
    .logo-text span   { font-size:11px; color:var(--text-2); letter-spacing:.5px; }

    h1 { font-size:24px; font-weight:800; color:var(--text-1); margin-bottom:6px; }
    .subtitle { font-size:13px; color:var(--text-2); margin-bottom:32px; }

    /* Error */
    .alert-error {
      display:flex; align-items:center; gap:10px;
      background: rgba(255,77,91,.1); border:1px solid rgba(255,77,91,.25);
      color:#FF6B77; border-radius:12px; padding:12px 16px;
      font-size:13px; font-weight:500; margin-bottom:24px;
    }

    /* Fields */
    .field { margin-bottom: 20px; }
    label {
      display:block; font-size:12px; font-weight:600;
      color:var(--text-2); margin-bottom:8px; letter-spacing:.4px; text-transform:uppercase;
    }
    .input-wrap { position: relative; }
    input[type=text], input[type=password] {
      width:100%; height:52px;
      background:#F8FAFC; border:1.5px solid #E2E8F0;
      border-radius:12px; padding:0 48px 0 16px;
      color:var(--text-1); font-family:inherit; font-size:14px;
      outline:none; transition:border-color .2s, box-shadow .2s;
    }
    input:focus {
      border-color:var(--primary);
      box-shadow:0 0 0 3px rgba(41,98,255,.18);
    }
    .toggle-pw {
      position:absolute; right:14px; top:50%; transform:translateY(-50%);
      background:none; border:none; color:var(--text-2); cursor:pointer;
      padding:4px; display:flex; transition:color .2s;
    }
    .toggle-pw:hover { color:var(--text-1); }

    /* Submit */
    .btn-submit {
      width:100%; height:52px; margin-top:8px;
      background:var(--primary);
      border:none; border-radius:12px;
      color:#fff; font-family:inherit; font-size:15px; font-weight:700;
      cursor:pointer; letter-spacing:.3px;
      transition:background .2s, transform .15s, box-shadow .2s;
      box-shadow: 0 8px 24px rgba(41,98,255,.35);
    }
    .btn-submit:hover  { background:var(--primary-h); transform:translateY(-1px); box-shadow:0 12px 32px rgba(41,98,255,.45); }
    .btn-submit:active { transform:translateY(0); }

    /* Back link */
    .back { margin-top:28px; text-align:center; }
    .back a {
      font-size:13px; color:var(--text-2); text-decoration:none;
      display:inline-flex; align-items:center; gap:6px; transition:color .2s;
    }
    .back a:hover { color:var(--text-1); }

    /* Credenciales hint */
    .hint {
      margin-top:24px; padding:14px;
      background: rgba(245,158,11,.07); border:1px solid rgba(245,158,11,.2);
      border-radius:12px; font-size:12px; color:rgba(146,90,0,.85); text-align:center;
    }
  </style>
</head>
<body>
  <!-- Orbs decorativos -->
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>

  <div class="card">
    <div class="logo-wrap">
      <img src="../assets/logo-catolica.png" alt="Católica School">
      <div class="logo-text">
        <strong>Católica School</strong>
        <span>Panel de Administración</span>
      </div>
    </div>

    <h1>Bienvenido</h1>
    <p class="subtitle">Ingresa tus credenciales para gestionar las noticias</p>

    <?php if ($error): ?>
    <div class="alert-error">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="login-form">
      <div class="field">
        <label for="username">Usuario</label>
        <div class="input-wrap">
          <input type="text" id="username" name="username" placeholder="Ingresa tu usuario" autocomplete="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
      </div>

      <div class="field">
        <label for="password">Contraseña</label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" placeholder="••••••••••" autocomplete="current-password" required>
          <button type="button" class="toggle-pw" id="toggle-pw" aria-label="Mostrar contraseña">
            <svg id="eye-on" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg id="eye-off" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-submit">Ingresar al Panel</button>
    </form>

    <div class="back">
      <a href="../index.html">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver al sitio web
      </a>
    </div>

    <div class="hint">
      Primera vez: ejecuta <code style="background:rgba(0,0,0,.3);padding:2px 6px;border-radius:4px">admin/setup.php</code> para crear las tablas y el usuario inicial.
    </div>
  </div>

  <script>
    const pwInput = document.getElementById('password');
    const eyeOn   = document.getElementById('eye-on');
    const eyeOff  = document.getElementById('eye-off');
    document.getElementById('toggle-pw').addEventListener('click', () => {
      const show = pwInput.type === 'password';
      pwInput.type = show ? 'text' : 'password';
      eyeOn.style.display  = show ? 'none'  : '';
      eyeOff.style.display = show ? ''      : 'none';
    });
  </script>
</body>
</html>
