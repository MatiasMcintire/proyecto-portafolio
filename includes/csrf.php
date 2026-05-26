<?php
/**
 * includes/csrf.php — Protección CSRF para formularios POST del admin
 *
 * Uso:
 *   - Incluir al inicio del archivo:        require_once '../includes/csrf.php';
 *   - En el handler POST, antes de procesar: csrf_check();
 *   - En cada <form method="POST">:
 *        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
 *
 * El token se guarda en $_SESSION['csrf_token'] y se compara con hash_equals()
 * para evitar timing attacks. El token se regenera si no existe; se mantiene
 * mientras dure la sesión (no rota por petición — más simple y suficiente
 * para un panel single-user).
 */

// ── Hardening de cookies de sesión ─────────────────────────────
// Estos parámetros DEBEN aplicarse ANTES del primer session_start().
// Si otra parte del código abrió sesión antes, no surten efecto.
if (session_status() === PHP_SESSION_NONE) {

    // Rechaza session IDs no inicializados por el servidor → mitiga
    // session fixation residual cuando se intenta inyectar PHPSESSID.
    ini_set('session.use_strict_mode', '1');

    // Secure solo en HTTPS: en producción (teclab.uct.cl) activa,
    // en local XAMPP (HTTP) desactivada para no romper login.
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'lifetime' => 0,          // cookie muere al cerrar navegador
        'path'     => '/',
        'domain'   => '',         // host actual
        'secure'   => $secure,    // dinámico según protocolo
        'httponly' => true,       // JS no puede leer la cookie → mitiga robo por XSS
        'samesite' => 'Strict',   // no se envía cross-site → mitiga CSRF a nivel cookie
    ]);

    session_start();
}

/**
 * Devuelve el token CSRF de la sesión (lo genera si no existe).
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        // 32 bytes aleatorios → 64 caracteres hex
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica el token CSRF enviado en $_POST['csrf'].
 * Si la verificación falla, responde 403 y termina el script.
 *
 * Llamar al inicio del bloque `if ($_SERVER['REQUEST_METHOD'] === 'POST')`.
 */
function csrf_check(): void
{
    $sent     = $_POST['csrf']             ?? '';
    $expected = $_SESSION['csrf_token']    ?? '';

    if (!is_string($sent) || $expected === '' || !hash_equals($expected, $sent)) {
        http_response_code(403);
        die('Token CSRF inválido. Recargá la página y volvé a intentar.');
    }
}
