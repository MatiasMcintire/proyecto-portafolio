<?php
/**
 * admin/skills_delete.php — Eliminar una habilidad
 * Solo acepta POST con token CSRF válido.
 */
require_once '../includes/auth.php';
require_once '../includes/csrf.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: skills.php');
    exit;
}

csrf_check();

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM habilidades WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: skills.php?msg=eliminada');
exit;
