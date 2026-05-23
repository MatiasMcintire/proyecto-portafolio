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

  <!-- Bootstrap 5.3 (framework CSS) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

  <!-- CSS propio (variables del tema, sobrescribe Bootstrap donde se requiere) -->
  <link rel="stylesheet" href="../assets/css/style.css">

  <style>
    /* Página de login: fondo suave, centra verticalmente */
    .login-page {
      min-height: 100vh;
      background-color: var(--color-bg-soft);
    }
    .login-card {
      background-color: var(--color-bg);
      border: 1px solid var(--color-border);
    }
  </style>
</head>
<body class="login-page d-flex align-items-center">

  <main class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">

        <div class="card login-card shadow-lg">
          <div class="card-body p-4 p-md-5">

            <h1 class="h4 text-center mb-1"><i class="ti ti-lock" aria-hidden="true"></i> Admin</h1>
            <p class="text-center text-muted small mb-4">
              Panel de administración del portafolio
            </p>

            <?php if ($error): ?>
              <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <form method="POST" action="login.php" novalidate>

              <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input
                  type="text"
                  id="username"
                  name="username"
                  class="form-control"
                  required
                  autocomplete="username"
                  placeholder="admin"
                  value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                >
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input
                  type="password"
                  id="password"
                  name="password"
                  class="form-control"
                  required
                  autocomplete="current-password"
                  placeholder="••••••••"
                >
              </div>

              <button type="submit" class="btn btn-primary w-100 mt-2">
                Iniciar sesión
              </button>

            </form>

            <p class="text-center text-muted small mt-4 mb-0">
              <a href="../index.php" class="text-decoration-none">← Volver al portafolio</a>
            </p>

          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- Bootstrap JS bundle (no estrictamente necesario en login, pero coherente) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
