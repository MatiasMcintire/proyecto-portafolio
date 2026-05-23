<?php
/**
 * admin/edit.php — Editar proyecto existente
 */
require_once '../includes/auth.php';
require_once '../config/db.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Obtener el proyecto actual
$stmt    = $conn->prepare("SELECT * FROM proyectos WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$proyecto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$proyecto) {
    header('Location: index.php');
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo         = trim(htmlspecialchars($_POST['titulo']         ?? ''));
    $descripcion    = trim(htmlspecialchars($_POST['descripcion']    ?? ''));
    $tecnologias    = trim(htmlspecialchars($_POST['tecnologias']    ?? ''));
    $url_github     = trim(htmlspecialchars($_POST['url_github']     ?? ''));
    $url_produccion = trim(htmlspecialchars($_POST['url_produccion'] ?? ''));
    $destacado      = isset($_POST['destacado']) ? 1 : 0;
    $orden          = (int)($_POST['orden'] ?? 0);
    $imagenActual   = $proyecto['imagen'];
    $imagenNombre   = $imagenActual; // Conservar imagen actual por defecto

    if (empty($titulo))       $errores[] = 'El título es obligatorio.';
    if (empty($descripcion))  $errores[] = 'La descripción es obligatoria.';

    // Procesar nueva imagen si se subió
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file    = $_FILES['imagen'];
        $maxSize = 2 * 1024 * 1024;
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if ($file['size'] > $maxSize) {
            $errores[] = 'La imagen no debe superar 2 MB.';
        }

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowed)) {
            $errores[] = 'Solo se permiten imágenes JPG, PNG o WebP.';
        }

        if (empty($errores)) {
            $extension    = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imagenNombre = uniqid('proyecto_', true) . '.' . strtolower($extension);
            $uploadDir    = '../assets/uploads/';

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $imagenNombre)) {
                // Eliminar imagen anterior si existía
                if ($imagenActual && file_exists($uploadDir . $imagenActual)) {
                    unlink($uploadDir . $imagenActual);
                }
            } else {
                $errores[] = 'Error al subir la imagen.';
                $imagenNombre = $imagenActual;
            }
        }
    }

    if (empty($errores)) {
        $stmt = $conn->prepare(
            "UPDATE proyectos SET
               titulo = ?, descripcion = ?, tecnologias = ?,
               url_github = ?, url_produccion = ?, imagen = ?,
               destacado = ?, orden = ?
             WHERE id = ?"
        );
        $stmt->bind_param(
            'sssssssii',
            $titulo, $descripcion, $tecnologias, $url_github, $url_produccion,
            $imagenNombre, $destacado, $orden, $id
        );

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: index.php?msg=editado');
            exit;
        } else {
            $errores[] = 'Error al actualizar en la base de datos.';
        }
        $stmt->close();
    }

    // Actualizar datos del proyecto con los valores del POST para repoblar el form
    $proyecto = array_merge($proyecto, [
        'titulo' => $titulo, 'descripcion' => $descripcion,
        'tecnologias' => $tecnologias, 'url_github' => $url_github,
        'url_produccion' => $url_produccion, 'destacado' => $destacado,
        'orden' => $orden
    ]);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Proyecto — Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
</head>
<body class="admin-body">

  <header class="admin-header" role="banner">
    <span class="admin-header__title"><i class="ti ti-edit" aria-hidden="true"></i> Editar Proyecto</span>
    <nav><a href="index.php"><i class="ti ti-arrow-left" aria-hidden="true"></i> Volver</a></nav>
  </header>

  <main class="admin-container" role="main">
    <div class="admin-card">

      <h2>Editando: <?= htmlspecialchars($proyecto['titulo']) ?></h2>

      <?php if (!empty($errores)): ?>
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:1rem; border-radius:8px; margin-bottom:1.5rem;" role="alert">
          <strong>Errores:</strong>
          <ul style="margin-top:0.5rem; padding-left:1.25rem;">
            <?php foreach ($errores as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="POST" action="edit.php?id=<?= $id ?>" enctype="multipart/form-data" novalidate>

        <div class="form-group">
          <label for="titulo">Título *</label>
          <input type="text" id="titulo" name="titulo" required
                 value="<?= htmlspecialchars($proyecto['titulo']) ?>">
        </div>

        <div class="form-group">
          <label for="descripcion">Descripción *</label>
          <textarea id="descripcion" name="descripcion" rows="4" required><?= htmlspecialchars($proyecto['descripcion']) ?></textarea>
        </div>

        <div class="form-group">
          <label for="tecnologias">Tecnologías</label>
          <input type="text" id="tecnologias" name="tecnologias"
                 value="<?= htmlspecialchars($proyecto['tecnologias'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="url_github">URL GitHub</label>
          <input type="url" id="url_github" name="url_github"
                 value="<?= htmlspecialchars($proyecto['url_github'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="url_produccion">URL Producción</label>
          <input type="url" id="url_produccion" name="url_produccion"
                 value="<?= htmlspecialchars($proyecto['url_produccion'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label>Imagen actual</label>
          <?php if (!empty($proyecto['imagen']) && file_exists('../assets/uploads/' . $proyecto['imagen'])): ?>
            <img src="../assets/uploads/<?= htmlspecialchars($proyecto['imagen']) ?>"
                 alt="Imagen actual" style="height:80px; border-radius:8px; margin-bottom:0.5rem; display:block">
          <?php else: ?>
            <p style="color:var(--color-text-muted); font-size:0.9rem;">Sin imagen</p>
          <?php endif; ?>
          <label for="imagen">Cambiar imagen (dejar vacío para conservar la actual)</label>
          <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp"
                 style="padding:0.5rem">
        </div>

        <div class="form-group">
          <label for="orden">Orden</label>
          <input type="number" id="orden" name="orden" min="0"
                 value="<?= (int)$proyecto['orden'] ?>" style="width:120px">
        </div>

        <div class="form-group" style="display:flex; align-items:center; gap:0.75rem">
          <input type="checkbox" id="destacado" name="destacado"
                 <?= $proyecto['destacado'] ? 'checked' : '' ?>
                 style="width:auto">
          <label for="destacado" style="margin:0">Destacado en home</label>
        </div>

        <div style="display:flex; gap:1rem; margin-top:1.5rem">
          <button type="submit" class="btn btn--primary">Guardar cambios</button>
          <a href="index.php" class="btn btn--outline">Cancelar</a>
        </div>

      </form>
    </div>
  </main>

<?php $conn->close(); ?>
</body>
</html>
