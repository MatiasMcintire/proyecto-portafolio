<?php
/**
 * admin/login.php — Página de inicio de sesión del administrador
 *
 * Seguridad:
 *   - password_verify() en lugar de MD5 (bcrypt es mucho más seguro)
 *   - Prepared statements contra SQL Injection
 *   - session_regenerate_id() contra Session Fixation
 */

session_start();

// Si ya está autenticado, redirigir al dashboard
if (isset($_SESSION['admin_user'])) {
    header('Location: index.php');
    exit;
}

require_once '../config/db.php';

$error = '';

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validar que los campos no estén vacíos
    if (empty($username) || empty($password)) {
        $error = 'Ingresa tu usuario y contraseña.';
    } else {
        // Buscar el usuario en la base de datos (prepared statement)
        $stmt = $conn->prepare("SELECT id, username, password FROM usuarios WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        // Verificar contraseña con password_verify() (compatible con password_hash/bcrypt)
        // Por qué NO usar MD5:
        //   MD5 es reversible con tablas rainbow. bcrypt es lento por diseño,
        //   lo que hace ataques de fuerza bruta extremadamente costosos.
        if ($user && password_verify($password, $user['password'])) {
            // Regenerar ID de sesión para prevenir Session Fixation
            session_regenerate_id(true);

            $_SESSION['admin_user'] = $user['username'];
            $_SESSION['admin_id']   = $user['id'];

            header('Location: index.php');
            exit;
        } else {
            // Mensaje genérico — no revelar si el usuario existe o no
            $error = 'Usuario o contraseña incorrectos.';
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Portafolio</title>
  <!-- Sin indexación de páginas de admin -->
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    /* Estilos específicos del login */
    .login-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: var(--color-bg-soft);
      padding: 2rem;
    }
    .login-card {
      background-color: var(--color-bg);
      border-radius: var(--radius-lg);
      padding: 2.5rem;
      width: 100%;
      max-width: 400px;
      box-shadow: var(--shadow-lg);
    }
    .login-card h1 {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
      text-align: center;
    }
    .login-card p {
      text-align: center;
      color: var(--color-text-muted);
      margin-bottom: 2rem;
      font-size: 0.9rem;
    }
    .alert-error {
      background-color: #fef2f2;
      border: 1px solid #fecaca;
      color: #991b1b;
      padding: 0.75rem 1rem;
      border-radius: var(--radius-md);
      margin-bottom: 1.25rem;
      font-size: 0.9rem;
    }
  </style>
</head>
<body class="login-page">

  <div class="login-card">

    <h1>🔐 Admin</h1>
    <p>Panel de administración del portafolio</p>

    <?php if ($error): ?>
      <div class="alert-error" role="alert">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php" novalidate>

      <div class="form-group">
        <label for="username">Usuario</label>
        <input
          type="text"
          id="username"
          name="username"
          required
          autocomplete="username"
          placeholder="admin"
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
        >
      </div>

      <div class="form-group">
        <label for="password">Contraseña</label>
        <input
          type="password"
          id="password"
          name="password"
          required
          autocomplete="current-password"
          placeholder="••••••••"
        >
      </div>

      <button type="submit" class="btn btn--primary" style="width:100%; margin-top:0.5rem">
        Iniciar sesión
      </button>

    </form>

    <p style="margin-top:1.5rem; text-align:center; font-size:0.82rem; color:var(--color-text-muted)">
      <a href="../index.php">← Volver al portafolio</a>
    </p>

  </div>

</body>
</html>
