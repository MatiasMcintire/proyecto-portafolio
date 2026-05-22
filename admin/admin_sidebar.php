<?php
/*
 * admin_sidebar.php — Sidebar compartido del panel de administración.
 * Requiere: $conn abierto (db.php) + $_SESSION['admin_user'] (auth.php).
 */

$_sp = basename($_SERVER['PHP_SELF'], '.php');

// Badge: mensajes sin leer
$_sr  = $conn->query("SELECT COUNT(*) AS n FROM contactos WHERE leido = 0");
$_unr = $_sr ? (int)$_sr->fetch_assoc()['n'] : 0;

// Contador: proyectos
$_rp  = $conn->query("SELECT COUNT(*) AS n FROM proyectos");
$_nPr = $_rp ? (int)$_rp->fetch_assoc()['n'] : 0;

// Contador: habilidades (solo si la tabla existe)
$_nSk = 0;
$_rsk = $conn->query("SHOW TABLES LIKE 'habilidades'");
if ($_rsk && $_rsk->num_rows > 0) {
    $__r  = $conn->query("SELECT COUNT(*) AS n FROM habilidades WHERE visible = 1");
    $_nSk = $__r ? (int)$__r->fetch_assoc()['n'] : 0;
}
?>

<button class="hamburger" id="hamburger" aria-label="Abrir menú">
  <span></span><span></span><span></span>
</button>

<aside class="sidebar" id="sidebar">

  <div class="sidebar__logo">
    <strong>⚙️ Panel Admin</strong>
    <span>Portafolio Profesional</span>
  </div>

  <nav class="sidebar__nav">

    <div class="sidebar__section">Principal</div>

    <a href="index.php" class="sidebar__link <?= $_sp === 'index' ? 'active' : '' ?>">
      <span class="icon">📊</span> Dashboard
    </a>

    <div class="sidebar__section">Contenido</div>

    <a href="index.php#sec-proyectos"
       class="sidebar__link <?= in_array($_sp, ['add', 'edit']) ? 'active' : '' ?>">
      <span class="icon">💼</span> Proyectos
      <?php if ($_nPr > 0): ?>
        <span class="sidebar__count"><?= $_nPr ?></span>
      <?php endif; ?>
    </a>

    <a href="index.php#sec-mensajes" class="sidebar__link">
      <span class="icon">✉️</span> Mensajes
      <?php if ($_unr > 0): ?>
        <span class="sidebar__badge"><?= $_unr ?></span>
      <?php endif; ?>
    </a>

    <a href="skills.php"
       class="sidebar__link <?= str_starts_with($_sp, 'skills') ? 'active' : '' ?>">
      <span class="icon">🛠️</span> Habilidades
      <?php if ($_nSk > 0): ?>
        <span class="sidebar__count"><?= $_nSk ?></span>
      <?php endif; ?>
    </a>

    <div class="sidebar__section">Configuración</div>

    <a href="profile.php" class="sidebar__link <?= $_sp === 'profile' ? 'active' : '' ?>">
      <span class="icon">👤</span> Mi Perfil
    </a>

    <a href="change_password.php"
       class="sidebar__link <?= $_sp === 'change_password' ? 'active' : '' ?>">
      <span class="icon">🔒</span> Contraseña
    </a>

    <div class="sidebar__section">Sitio</div>

    <a href="../index.php" class="sidebar__link" target="_blank" rel="noopener">
      <span class="icon">🌐</span> Ver portafolio
    </a>

  </nav>

  <div class="sidebar__footer">
    <p class="sidebar__user">
      Sesión: <strong><?= htmlspecialchars($_SESSION['admin_user']) ?></strong>
    </p>
    <a href="logout.php" class="sidebar__logout">Cerrar sesión</a>
  </div>

</aside>
