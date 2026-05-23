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

if (session_status() === PHP_SESSION_NONE) {
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
