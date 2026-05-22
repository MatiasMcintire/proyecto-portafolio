<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$error   = '';
$success = '';
$perfil  = null;

// Verificar si la tabla perfil existe
$_check      = $conn->query("SHOW TABLES LIKE 'perfil'");
$tableExists = ($_check && $_check->num_rows > 0);

if ($tableExists) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nombre    = trim($_POST['nombre']             ?? '');
        $titulo    = trim($_POST['titulo_profesional'] ?? '');
        $bio       = trim($_POST['bio']                ?? '');
        $email_c   = trim($_POST['email_contacto']     ?? '');
        $telefono  = trim($_POST['telefono']           ?? '');
        $ubicacion = trim($_POST['ubicacion']          ?? '');
        $github    = trim($_POST['github']             ?? '');
        $linkedin  = trim($_POST['linkedin']           ?? '');
        $foto      = trim($_POST['foto_actual']        ?? '');

        // Validaciones básicas
        if (empty($nombre)) {
            $error = 'El nombre no puede estar vacío.';
        } elseif (!empty($email_c) && !filter_var($email_c, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email de contacto no es válido.';
        }

        // Subida de foto
        if (!$error && !empty($_FILES['foto']['name'])) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fi      = finfo_open(FILEINFO_MIME_TYPE);
            $mime    = finfo_file($fi, $_FILES['foto']['tmp_name']);
            finfo_close($fi);

            if (!in_array($mime, $allowed)) {
                $error = 'Solo se permiten imágenes JPG, PNG, GIF o WEBP.';
            } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                $error = 'La imagen no puede superar 2 MB.';
            } else {
                $ext      = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $newFoto  = 'perfil_' . time() . '.' . $ext;
                $uploadDir = '../assets/uploads/';

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $newFoto)) {
                    // Eliminar foto anterior
                    if (!empty($foto) && file_exists($uploadDir . $foto)) {
                        unlink($uploadDir . $foto);
                    }
                    $foto = $newFoto;
                } else {
                    $error = 'Error al subir la imagen. Verifica permisos de la carpeta uploads/.';
                }
            }
        }

        if (!$error) {
            $stmt = $conn->prepare(
                "UPDATE perfil SET
                    nombre = ?, titulo_profesional = ?, bio = ?,
                    email_contacto = ?, telefono = ?, ubicacion = ?,
                    github = ?, linkedin = ?, foto = ?
                 WHERE id = 1"
            );
            $stmt->bind_param(
                'sssssssss',
                $nombre, $titulo, $bio,
                $email_c, $telefono, $ubicacion,
                $github, $linkedin, $foto
            );
            $stmt->execute();
            $stmt->close();
            $success = '¡Perfil actualizado correctamente!';
        }
    }

    // Obtener perfil actual
    $r      = $conn->query("SELECT * FROM perfil WHERE id = 1 LIMIT 1");
    $perfil = $r ? $r->fetch_assoc() : null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Perfil — Panel Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<main class="main">

  <div class="page-header">
    <div>
      <h1>👤 Mi Perfil</h1>
      <p>Información pública de tu portafolio</p>
    </div>
  </div>

  <?php if (!$tableExists): ?>
  <!-- ── Instrucciones de primer uso ── -->
  <div class="sql-instructions">
    <strong>⚠️ Primer uso — Crea la tabla en phpMyAdmin</strong><br>
    Copia y ejecuta este SQL en <strong>phpMyAdmin → SQL</strong>:
  </div>
  <div class="sql-box">CREATE TABLE IF NOT EXISTS `perfil` (
  `id`                  INT            AUTO_INCREMENT PRIMARY KEY,
  `nombre`              VARCHAR(100)   NOT NULL DEFAULT '',
  `titulo_profesional`  VARCHAR(150)   NOT NULL DEFAULT '',
  `bio`                 TEXT,
  `email_contacto`      VARCHAR(120)   DEFAULT '',
  `telefono`            VARCHAR(30)    DEFAULT '',
  `ubicacion`           VARCHAR(100)   DEFAULT '',
  `github`              VARCHAR(200)   DEFAULT '',
  `linkedin`            VARCHAR(200)   DEFAULT '',
  `foto`                VARCHAR(255)   DEFAULT '',
  `updated_at`          TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT IGNORE INTO `perfil` (`id`) VALUES (1);</div>
  <p style="color:#64748b; font-size:.88rem; margin-top:.5rem;">
    Después de ejecutar el SQL, recarga esta página.
  </p>

  <?php else: ?>

  <?php if ($error):   ?><div class="alert alert-del"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-ok">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="POST" action="profile.php" enctype="multipart/form-data">

    <div class="card">
      <div class="card__header"><h2>Información personal</h2></div>
      <div class="card__body" style="padding:1.5rem">

        <div class="form-grid-2">
          <div class="form-row">
            <label for="nombre">Nombre completo *</label>
            <input type="text" id="nombre" name="nombre" required
                   value="<?= htmlspecialchars($perfil['nombre'] ?? '') ?>"
                   placeholder="Ej: Matías González">
          </div>
          <div class="form-row">
            <label for="titulo_profesional">Título profesional</label>
            <input type="text" id="titulo_profesional" name="titulo_profesional"
                   value="<?= htmlspecialchars($perfil['titulo_profesional'] ?? '') ?>"
                   placeholder="Ej: Desarrollador Web Full Stack">
          </div>
        </div>

        <div class="form-row">
          <label for="bio">Biografía / Sobre mí</label>
          <textarea id="bio" name="bio" rows="5"
                    placeholder="Cuéntate en 2-3 párrafos: tu experiencia, motivaciones y qué ofreces."><?= htmlspecialchars($perfil['bio'] ?? '') ?></textarea>
        </div>

      </div>
    </div>

    <div class="card">
      <div class="card__header"><h2>Contacto y ubicación</h2></div>
      <div class="card__body" style="padding:1.5rem">

        <div class="form-grid-2">
          <div class="form-row">
            <label for="email_contacto">Email de contacto</label>
            <input type="email" id="email_contacto" name="email_contacto"
                   value="<?= htmlspecialchars($perfil['email_contacto'] ?? '') ?>"
                   placeholder="tucorreo@email.com">
          </div>
          <div class="form-row">
            <label for="telefono">Teléfono / WhatsApp</label>
            <input type="text" id="telefono" name="telefono"
                   value="<?= htmlspecialchars($perfil['telefono'] ?? '') ?>"
                   placeholder="+56 9 1234 5678">
          </div>
        </div>

        <div class="form-row">
          <label for="ubicacion">Ubicación</label>
          <input type="text" id="ubicacion" name="ubicacion"
                 value="<?= htmlspecialchars($perfil['ubicacion'] ?? '') ?>"
                 placeholder="Ej: Santiago, Chile">
        </div>

      </div>
    </div>

    <div class="card">
      <div class="card__header"><h2>Redes y foto</h2></div>
      <div class="card__body" style="padding:1.5rem">

        <div class="form-grid-2">
          <div class="form-row">
            <label for="github">GitHub <small>(URL completa)</small></label>
            <input type="url" id="github" name="github"
                   value="<?= htmlspecialchars($perfil['github'] ?? '') ?>"
                   placeholder="https://github.com/tu-usuario">
          </div>
          <div class="form-row">
            <label for="linkedin">LinkedIn <small>(URL completa)</small></label>
            <input type="url" id="linkedin" name="linkedin"
                   value="<?= htmlspecialchars($perfil['linkedin'] ?? '') ?>"
                   placeholder="https://linkedin.com/in/tu-usuario">
          </div>
        </div>

        <div class="form-row">
          <label for="foto">Foto de perfil <small>(JPG, PNG, WEBP — máx. 2 MB)</small></label>
          <?php if (!empty($perfil['foto']) && file_exists('../assets/uploads/' . $perfil['foto'])): ?>
            <div style="margin-bottom:.75rem; display:flex; align-items:center; gap:1rem;">
              <img src="../assets/uploads/<?= htmlspecialchars($perfil['foto']) ?>"
                   alt="Foto actual"
                   style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid #e2e8f0;">
              <span style="font-size:.82rem; color:#64748b;">Foto actual</span>
            </div>
          <?php endif; ?>
          <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($perfil['foto'] ?? '') ?>">
          <input type="file" id="foto" name="foto" accept="image/*">
          <div class="hint">Sube una nueva imagen para reemplazar la actual.</div>
        </div>

        <button type="submit" class="btn-save">💾 Guardar perfil</button>

      </div>
    </div>

  </form>

  <?php endif; ?>

</main>

<script src="../assets/js/admin.js"></script>
</body>
</html>
