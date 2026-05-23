<?php
/**
 * admin/add.php — Agregar nuevo proyecto al portafolio
 *
 * Seguridad en subida de imagen:
 *   - Verificar tipo MIME real (no solo extensión)
 *   - Limitar tamaño máximo
 *   - Generar nombre único (evitar sobreescribir archivos)
 */
require_once '../includes/auth.php';
require_once '../config/db.php';

$errores = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger y sanitizar campos de texto
    $titulo        = trim(htmlspecialchars($_POST['titulo']        ?? ''));
    $descripcion   = trim(htmlspecialchars($_POST['descripcion']   ?? ''));
    $tecnologias   = trim(htmlspecialchars($_POST['tecnologias']   ?? ''));
    $url_github    = trim(htmlspecialchars($_POST['url_github']    ?? ''));
    $url_produccion = trim(htmlspecialchars($_POST['url_produccion'] ?? ''));
    $destacado     = isset($_POST['destacado']) ? 1 : 0;
    $orden         = (int)($_POST['orden'] ?? 0);

    // Validaciones
    if (empty($titulo))       $errores[] = 'El título es obligatorio.';
    if (empty($descripcion))  $errores[] = 'La descripción es obligatoria.';

    // Procesar imagen (si se subió)
    $imagenNombre = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

        $file     = $_FILES['imagen'];
        $maxSize  = 2 * 1024 * 1024; // 2 MB máximo
        $allowed  = ['image/jpeg', 'image/png', 'image/webp'];

        // Verificar tamaño
        if ($file['size'] > $maxSize) {
            $errores[] = 'La imagen no debe superar 2 MB.';
        }

        // Verificar tipo MIME real (más seguro que solo la extensión)
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowed)) {
            $errores[] = 'Solo se permiten imágenes JPG, PNG o WebP.';
        }

        if (empty($errores)) {
            // Generar nombre único para evitar sobreescritura
            $extension    = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imagenNombre = uniqid('proyecto_', true) . '.' . strtolower($extension);
            $uploadDir    = '../assets/uploads/';
            $destino      = $uploadDir . $imagenNombre;

            if (!move_uploaded_file($file['tmp_name'], $destino)) {
                $errores[] = 'Error al subir la imagen. Verifica permisos del directorio.';
                $imagenNombre = null;
            }
        }
    }

    // Guardar en la base de datos si no hay errores
    if (empty($errores)) {
        $stmt = $conn->prepare(
            "INSERT INTO proyectos
               (titulo, descripcion, tecnologias, url_github, url_produccion, imagen, destacado, orden)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'ssssssii',
            $titulo, $descripcion, $tecnologias, $url_github, $url_produccion,
            $imagenNombre, $destacado, $orden
        );

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: index.php?msg=creado');
            exit;
        } else {
            $errores[] = 'Error al guardar en la base de datos.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar Proyecto — Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
</head>
<body class="admin-body">

  <header class="admin-header" role="banner">
    <span class="admin-header__title"><i class="ti ti-plus" aria-hidden="true"></i> Agregar Proyecto</span>
    <nav>
      <a href="index.php"><i class="ti ti-arrow-left" aria-hidden="true"></i> Volver al dashboard</a>
    </nav>
  </header>

  <main class="admin-container" role="main">
    <div class="admin-card">

      <h2>Nuevo Proyecto</h2>

      <?php if (!empty($errores)): ?>
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:1rem; border-radius:8px; margin-bottom:1.5rem;" role="alert">
          <strong>Corrige los siguientes errores:</strong>
          <ul style="margin-top:0.5rem; padding-left:1.25rem;">
            <?php foreach ($errores as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- enctype="multipart/form-data" es obligatorio para subir archivos -->
      <form method="POST" action="add.php" enctype="multipart/form-data" novalidate>

        <div class="form-group">
          <label for="titulo">Título del proyecto *</label>
          <input type="text" id="titulo" name="titulo" required
                 value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="descripcion">Descripción *</label>
          <textarea id="descripcion" name="descripcion" rows="4" required><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="tecnologias">Tecnologías (separadas por coma)</label>
          <input type="text" id="tecnologias" name="tecnologias"
                 placeholder="PHP, MySQL, Bootstrap 5, JavaScript"
                 value="<?= htmlspecialchars($_POST['tecnologias'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="url_github">URL GitHub</label>
          <input type="url" id="url_github" name="url_github"
                 placeholder="https://github.com/usuario/repo"
                 value="<?= htmlspecialchars($_POST['url_github'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="url_produccion">URL en Producción</label>
          <input type="url" id="url_produccion" name="url_produccion"
                 placeholder="https://miproyecto.cl/"
                 value="<?= htmlspecialchars($_POST['url_produccion'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="imagen">Imagen del proyecto (JPG/PNG/WebP, máx 2MB)</label>
          <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp"
                 style="padding:0.5rem">
        </div>

        <div class="form-group">
          <label for="orden">Orden de aparición</label>
          <input type="number" id="orden" name="orden" min="0"
                 value="<?= (int)($_POST['orden'] ?? 0) ?>" style="width:120px">
        </div>

        <div class="form-group" style="display:flex; align-items:center; gap:0.75rem">
          <input type="checkbox" id="destacado" name="destacado"
                 <?= isset($_POST['destacado']) ? 'checked' : '' ?>
                 style="width:auto">
          <label for="destacado" style="margin:0">Mostrar como destacado en el home</label>
        </div>

        <div style="display:flex; gap:1rem; margin-top:1.5rem">
          <button type="submit" class="btn btn--primary">Guardar proyecto</button>
          <a href="index.php" class="btn btn--outline">Cancelar</a>
        </div>

      </form>

    </div>
  </main>

<?php $conn->close(); ?>
</body>
</html>
