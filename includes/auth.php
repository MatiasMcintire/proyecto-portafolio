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

// csrf.php abre la sesión con HttpOnly/SameSite/Strict/use_strict_mode.
// Centralizar el session_start ahí garantiza que el hardening siempre aplique.
require_once __DIR__ . '/csrf.php';

if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}
