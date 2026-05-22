<?php
/**
 * admin/logout.php — Cierre de sesión seguro
 * Destruye la sesión completamente y redirige al login.
 */
session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
