<?php
/**
 * admin/logout.php — Cierre de sesión seguro
 * Solo acepta POST con CSRF válido (evita logout forzado vía CSRF).
 * Destruye la sesión completamente y redirige al login.
 */
require_once '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: index.php');
    exit;
}

csrf_check();

session_unset();
session_destroy();

header('Location: login.php');
exit;
