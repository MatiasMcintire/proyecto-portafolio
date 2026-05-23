<?php
/**
 * setup_password.php — Activa el usuario admin en la base de datos
 *
 * USAR UNA SOLA VEZ y luego eliminar este archivo del servidor.
 * No requiere login previo (es el script de primer acceso).
 */
require_once '../config/db.php';
require_once '../includes/csrf.php';

$msg     = '';
$success = false;
$newPass = '';

// Si se envió el formulario: actualizar contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    csrf_check();

    $newPass    = $_POST['new_password']     ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if (empty($newPass)) {
        $msg = 'Ingresa una contraseña.';
    } elseif (strlen($newPass) < 8) {
        $msg = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($newPass !== $confirmPass) {
        $msg = 'Las contraseñas no coinciden.';
    } else {
        // Generar hash seguro con bcrypt
        $hash = password_hash($newPass, PASSWORD_DEFAULT);

        // Actualizar en la base de datos
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE username = 'admin'");
        $stmt->bind_param('s', $hash);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $success = true;
            $msg     = '¡Contraseña actualizada! Ya puedes iniciar sesión.';
        } else {
            $msg = 'Error al actualizar. ¿Importaste el archivo portafolio.sql en phpMyAdmin?';
        }
        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #1f2937;
      padding: 2rem;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      padding: 2rem;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0,0,0,.3);
    }
    h1 { font-size: 1.35rem; margin-bottom: 0.5rem; }
    .subtitle { color: #6b7280; font-size: 0.9rem; margin-bottom: 1.5rem; }
    label {
      display: block;
      font-size: 0.88rem;
      font-weight: 600;
      margin-bottom: 0.35rem;
      color: #374151;
    }
    input[type="password"] {
      width: 100%;
      padding: 0.7rem 1rem;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 0.95rem;
      margin-bottom: 1rem;
      outline: none;
      transition: border-color .2s;
    }
    input[type="password"]:focus { border-color: #2563eb; }
    button {
      width: 100%;
      padding: 0.8rem;
      background: #2563eb;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
    }
    button:hover { background: #1d4ed8; }
    .alert {
      padding: 0.85rem 1rem;
      border-radius: 8px;
      margin-bottom: 1.25rem;
      font-size: 0.9rem;
    }
    .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
    .actions-success {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      margin-top: 1.25rem;
    }
    .btn-link {
      display: block;
      text-align: center;
      padding: 0.75rem;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.95rem;
      text-decoration: none;
    }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-outline { border: 2px solid #2563eb; color: #2563eb; }
    .warning {
      margin-top: 1.5rem;
      padding: 0.75rem;
      background: #fffbeb;
      border: 1px solid #fcd34d;
      border-radius: 8px;
      font-size: 0.82rem;
      color: #92400e;
    }
  </style>
</head>
<body>

<div class="card">

  <h1><i class="ti ti-lock"></i> Configurar Admin</h1>
  <p class="subtitle">Define la contraseña para el usuario <strong>admin</strong></p>

  <?php if ($msg && !$success): ?>
    <div class="alert alert-error"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>

    <div class="alert alert-success">
      <strong><i class="ti ti-circle-check"></i> <?= htmlspecialchars($msg) ?></strong>
    </div>

    <div class="actions-success">
      <a href="login.php" class="btn-link btn-primary">Ir al Login →</a>
      <a href="../index.php" class="btn-link btn-outline">Ver portafolio</a>
    </div>

    <div class="warning">
      <i class="ti ti-alert-triangle"></i> <strong>Importante:</strong> Elimina este archivo del servidor
      (<code>admin/setup_password.php</code>) una vez que hayas ingresado al panel.
    </div>

  <?php else: ?>

    <form method="POST" action="setup_password.php">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

      <label for="new_password">Nueva contraseña (mín. 8 caracteres)</label>
      <input
        type="password"
        id="new_password"
        name="new_password"
        placeholder="Ej: MiAdmin2024!"
        required
        autocomplete="new-password"
      >

      <label for="confirm_password">Confirmar contraseña</label>
      <input
        type="password"
        id="confirm_password"
        name="confirm_password"
        placeholder="Repite la contraseña"
        required
        autocomplete="new-password"
      >

      <button type="submit">Activar admin</button>

    </form>

    <div class="warning" style="margin-top:1rem">
      <i class="ti ti-info-circle"></i> Usuario: <strong>admin</strong> &nbsp;|&nbsp;
      Este script solo existe para el primer acceso.
    </div>

  <?php endif; ?>

</div>

</body>
</html>
