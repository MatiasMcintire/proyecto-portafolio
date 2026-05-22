<?php
/**
 * includes/auth.php — Protección de rutas administrativas
 *
 * Incluir al inicio de CADA archivo del panel admin.
 * Si el usuario no está autenticado, lo redirige al login.
 *
 * Uso:
 *   <?php require_once '../includes/auth.php'; ?>
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}
