<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $actual   = $_POST['actual']   ?? '';
    $nueva    = $_POST['nueva']    ?? '';
    $confirma = $_POST['confirma'] ?? '';

    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $_SESSION['admin_user']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        $error = 'Usuario no encontrado.';
    } elseif (!password_verify($actual, $row['password'])) {
        $error = 'La contraseña actual es incorrecta.';
    } elseif (strlen($nueva) < 8) {
        $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
    } elseif ($nueva !== $confirma) {
        $error = 'Las contraseñas nuevas no coinciden.';
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE username = ?");
        $stmt->bind_param('ss', $hash, $_SESSION['admin_user']);
        $stmt->execute();
        $stmt->close();
        $success = '¡Contraseña actualizada correctamente!';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contraseña — Panel Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<main class="main">

  <div class="page-header">
    <div>
      <h1>Seguridad</h1>
      <p>Cambia la contraseña de acceso al panel</p>
    </div>
  </div>

  <?php if ($error):   ?><div class="alert alert-del"><i class="ti ti-alert-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-ok"><i class="ti ti-circle-check"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>

  <div class="card" style="max-width:480px">
    <div class="card__header"><h2><i class="ti ti-key"></i> Nueva contraseña</h2></div>
    <div class="card__body" style="padding:1.5rem">

      <form method="POST" action="change_password.php" novalidate>

        <div class="form-row">
          <label for="actual">Contraseña actual</label>
          <input type="password" id="actual" name="actual" required
                 placeholder="••••••••" autocomplete="current-password">
        </div>

        <div class="form-row">
          <label for="nueva">Nueva contraseña <small>(mín. 8 caracteres)</small></label>
          <input type="password" id="nueva" name="nueva" required
                 placeholder="••••••••" autocomplete="new-password">
        </div>

        <div class="form-row">
          <label for="confirma">Confirmar nueva contraseña</label>
          <input type="password" id="confirma" name="confirma" required
                 placeholder="••••••••" autocomplete="new-password">
        </div>

        <button type="submit" class="btn-save">Actualizar contraseña</button>

      </form>

    </div>
  </div>

</main>

<script src="../assets/js/admin.js"></script>
</body>
</html>
