<?php
/**
 * api/contact.php — Endpoint de procesamiento del formulario de contacto
 *
 * Recibe los datos del formulario vía POST (enviados por fetch en main.js).
 * Valida, sanitiza y guarda en la base de datos.
 * Devuelve una respuesta JSON (no HTML).
 *
 * Seguridad implementada:
 *   - Verificación del método HTTP (solo acepta POST)
 *   - Sanitización de entradas con filter_var()
 *   - Validación server-side (no confiar solo en JS del cliente)
 *   - Prepared statements para prevenir SQL injection
 *   - htmlspecialchars() al mostrar datos (previene XSS)
 */

// Indicar que la respuesta es JSON
header('Content-Type: application/json; charset=UTF-8');

// Solo aceptar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

// Conectar a la base de datos
require_once '../config/db.php';

// ── Recoger datos del formulario en RAW ───────────────────────
// SQL injection: bloqueado por los prepared statements (más abajo).
// XSS: se escapa al renderizar en el admin con htmlspecialchars().
// No usamos FILTER_SANITIZE_SPECIAL_CHARS porque modifica el contenido
// y produce doble-escape al mostrar (M&M's → M&#38;M&#39;s en BD).
$nombre  = trim($_POST['nombre']  ?? '');
$email   = trim($_POST['email']   ?? '');
$asunto  = trim($_POST['asunto']  ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

// IP del visitante (para control de spam, si se requiere)
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// ── Validaciones server-side ──────────────────────────────────
// Siempre validar en el servidor, aunque el cliente (JS) ya validó.
// El usuario podría desactivar JavaScript o manipular las peticiones.

$errors = [];

if (empty($nombre)) {
    $errors[] = 'El nombre es obligatorio.';
} elseif (strlen($nombre) < 3 || strlen($nombre) > 100) {
    $errors[] = 'El nombre debe tener entre 3 y 100 caracteres.';
} elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/', $nombre)) {
    $errors[] = 'El nombre solo puede contener letras y espacios.';
}

if (empty($email)) {
    $errors[] = 'El correo electrónico es obligatorio.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El correo electrónico no tiene un formato válido.';
}

if (empty($asunto)) {
    $errors[] = 'El asunto es obligatorio.';
} elseif (strlen($asunto) < 5 || strlen($asunto) > 150) {
    $errors[] = 'El asunto debe tener entre 5 y 150 caracteres.';
}

if (empty($mensaje)) {
    $errors[] = 'El mensaje es obligatorio.';
} elseif (strlen($mensaje) < 20 || strlen($mensaje) > 1000) {
    $errors[] = 'El mensaje debe tener entre 20 y 1000 caracteres.';
}

// Si hay errores, devolver respuesta con error
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors)
    ]);
    $conn->close();
    exit;
}

// ── Guardar en la base de datos (prepared statement) ─────────
// Prepared statements previenen SQL Injection al separar
// la consulta SQL de los datos del usuario.

$stmt = $conn->prepare(
    "INSERT INTO contactos (nombre, email, asunto, mensaje, ip)
     VALUES (?, ?, ?, ?, ?)"
);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
    $conn->close();
    exit;
}

// "sssss" → los 5 parámetros son strings
$stmt->bind_param('sssss', $nombre, $email, $asunto, $mensaje, $ip);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => '¡Mensaje enviado correctamente!'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo guardar el mensaje. Intenta más tarde.'
    ]);
}

$stmt->close();
$conn->close();
