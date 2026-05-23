<?php
/*
 * admin_sidebar.php — Sidebar compartido del panel de administración.
 * Requiere: $conn abierto (db.php) + $_SESSION['admin_user'] (auth.php) + csrf.php.
 */
require_once __DIR__ . '/../includes/csrf.php';

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
    <i class="ti ti-command icon" aria-hidden="true"></i>
    <strong>Panel Admin</strong>
  </div>

  <nav class="sidebar__nav">

    <div class="sidebar__section">Principal</div>

    <a href="index.php" class="sidebar__link <?= $_sp === 'index' ? 'active' : '' ?>">
      <i class="ti ti-layout-dashboard icon" aria-hidden="true"></i> Dashboard
    </a>

    <div class="sidebar__section">Contenido</div>

    <a href="index.php#sec-proyectos"
       class="sidebar__link <?= in_array($_sp, ['add', 'edit']) ? 'active' : '' ?>">
      <i class="ti ti-folder icon" aria-hidden="true"></i> Proyectos
      <?php if ($_nPr > 0): ?>
        <span class="sidebar__count"><?= $_nPr ?></span>
      <?php endif; ?>
    </a>

    <a href="index.php#sec-mensajes" class="sidebar__link">
      <i class="ti ti-mail icon" aria-hidden="true"></i> Mensajes
      <?php if ($_unr > 0): ?>
        <span class="sidebar__badge"><?= $_unr ?></span>
      <?php endif; ?>
    </a>

    <a href="skills.php"
       class="sidebar__link <?= str_starts_with($_sp, 'skills') ? 'active' : '' ?>">
      <i class="ti ti-tool icon" aria-hidden="true"></i> Habilidades
      <?php if ($_nSk > 0): ?>
        <span class="sidebar__count"><?= $_nSk ?></span>
      <?php endif; ?>
    </a>

    <div class="sidebar__section">Configuración</div>

    <a href="profile.php" class="sidebar__link <?= $_sp === 'profile' ? 'active' : '' ?>">
      <i class="ti ti-user icon" aria-hidden="true"></i> Mi Perfil
    </a>

    <a href="change_password.php"
       class="sidebar__link <?= $_sp === 'change_password' ? 'active' : '' ?>">
      <i class="ti ti-key icon" aria-hidden="true"></i> Contraseña
    </a>

    <div class="sidebar__section">Sitio</div>

    <a href="../index.php" class="sidebar__link" target="_blank" rel="noopener">
      <i class="ti ti-world icon" aria-hidden="true"></i> Ver portafolio
    </a>

  </nav>

  <div class="sidebar__footer">
    <p class="sidebar__user">
      Sesión: <strong><?= htmlspecialchars($_SESSION['admin_user']) ?></strong>
    </p>
    <form method="POST" action="logout.php" style="margin:0">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <button type="submit" class="sidebar__logout">Cerrar sesión</button>
    </form>
  </div>

</aside>
