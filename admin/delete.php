<?php
/**
 * admin/delete.php — Eliminar un proyecto
 * Solo acepta POST con token CSRF válido (evita CSRF + prefetch del navegador).
 */
require_once '../includes/auth.php';
require_once '../includes/csrf.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: index.php');
    exit;
}

csrf_check();

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Obtener imagen antes de eliminar (para borrar el archivo)
$stmt = $conn->prepare("SELECT imagen FROM proyectos WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Eliminar el registro de la base de datos
$stmt = $conn->prepare("DELETE FROM proyectos WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    // Eliminar archivo de imagen si existe
    if (!empty($row['imagen'])) {
        $path = '../assets/uploads/' . $row['imagen'];
        if (file_exists($path)) {
            unlink($path);
        }
    }
}

$stmt->close();
$conn->close();

header('Location: index.php?msg=eliminado');
exit;
