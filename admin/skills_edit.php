<?php
require_once '../includes/auth.php';
require_once '../includes/csrf.php';
require_once '../config/db.php';

$id    = (int)($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    header('Location: skills.php');
    exit;
}

// Obtener habilidad actual
$stmt = $conn->prepare("SELECT * FROM habilidades WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$h = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$h) {
    header('Location: skills.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    csrf_check();

    $categoria  = trim($_POST['categoria']  ?? '');
    $nombre     = trim($_POST['nombre']     ?? '');
    $nivel      = (int)($_POST['nivel']     ?? 80);
    $icon_class = trim($_POST['icon_class'] ?? '');
    $orden      = (int)($_POST['orden']     ?? 0);
    $visible    = isset($_POST['visible']) ? 1 : 0;

    if (empty($categoria)) {
        $error = 'La categoría es obligatoria.';
    } elseif (empty($nombre)) {
        $error = 'El nombre de la habilidad es obligatorio.';
    } elseif ($nivel < 0 || $nivel > 100) {
        $error = 'El nivel debe estar entre 0 y 100.';
    } else {
        $stmt = $conn->prepare(
            "UPDATE habilidades
             SET categoria=?, nombre=?, nivel=?, icon_class=?, orden=?, visible=?
             WHERE id=?"
        );
        $stmt->bind_param('ssisiii', $categoria, $nombre, $nivel, $icon_class, $orden, $visible, $id);
        $stmt->execute();
        $stmt->close();

        header('Location: skills.php?msg=editada');
        exit;
    }

    $h = array_merge($h, [
        'categoria'  => $categoria,
        'nombre'     => $nombre,
        'nivel'      => $nivel,
        'icon_class' => $icon_class,
        'orden'      => $orden,
        'visible'    => $visible,
    ]);
}

// Categorías existentes para sugerencia
$cats = [];
$rc = $conn->query("SELECT DISTINCT categoria FROM habilidades ORDER BY categoria ASC");
if ($rc) {
    while ($row = $rc->fetch_assoc()) {
        $cats[] = $row['categoria'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Habilidad — Panel Admin</title>
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
      <h1>Editar Habilidad</h1>
      <p><?= htmlspecialchars($h['nombre']) ?></p>
    </div>
    <a href="skills.php" class="btn-add" style="background:var(--text-secondary)"><i class="ti ti-arrow-left"></i> Volver</a>
  </div>

  <?php if ($error): ?><div class="alert alert-del"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <div class="card" style="max-width:560px">
    <div class="card__header"><h2>Datos de la habilidad</h2></div>
    <div class="card__body" style="padding:1.5rem">

      <form method="POST" action="skills_edit.php?id=<?= $id ?>" novalidate>
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <div class="form-grid-2">
          <div class="form-row">
            <label for="categoria">Categoría *</label>
            <input type="text" id="categoria" name="categoria" required
                   list="cat-list"
                   value="<?= htmlspecialchars($h['categoria']) ?>"
                   placeholder="Ej: Frontend, Backend, Herramientas">
            <datalist id="cat-list">
              <?php foreach ($cats as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>">
              <?php endforeach; ?>
              <option value="Frontend">
              <option value="Backend">
              <option value="Herramientas">
              <option value="Diseño">
              <option value="DevOps">
            </datalist>
          </div>

          <div class="form-row">
            <label for="nombre">Nombre de la habilidad *</label>
            <input type="text" id="nombre" name="nombre" required
                   value="<?= htmlspecialchars($h['nombre']) ?>"
                   placeholder="Ej: PHP, JavaScript, Git">
          </div>
        </div>

        <div class="form-row">
          <label for="nivel">Nivel de dominio: <strong id="nivel-val"><?= (int)$h['nivel'] ?>%</strong></label>
          <input type="range" id="nivel" name="nivel"
                 min="0" max="100" step="5"
                 value="<?= (int)$h['nivel'] ?>"
                 oninput="document.getElementById('nivel-val').textContent = this.value + '%'"
                 style="width:100%; accent-color:#3b82f6; margin-top:.5rem;">
        </div>

        <div class="form-row">
          <label for="icon_class">Clase del ícono <small>(Devicon o Tabler Icons)</small></label>
          <input type="text" id="icon_class" name="icon_class"
                 value="<?= htmlspecialchars($h['icon_class'] ?? '') ?>"
                 placeholder="Ej: devicon-php-plain colored  o  ti ti-shield-lock">
          <div class="hint">
            Buscar en <a href="https://devicon.dev" target="_blank" rel="noopener">devicon.dev</a>
            o <a href="https://tabler.io/icons" target="_blank" rel="noopener">tabler.io/icons</a>.
            Si lo dejas vacío, se usa <code>ti ti-tag</code> como fallback.
          </div>
        </div>

        <div class="form-grid-2">
          <div class="form-row">
            <label for="orden">Orden de visualización</label>
            <input type="number" id="orden" name="orden" min="0" max="999"
                   value="<?= (int)$h['orden'] ?>"
                   placeholder="0">
            <div class="hint">Menor número = aparece primero.</div>
          </div>

          <div class="form-row" style="display:flex; flex-direction:column; justify-content:center;">
            <label>&nbsp;</label>
            <div class="form-check">
              <input type="checkbox" id="visible" name="visible" value="1"
                     <?= $h['visible'] ? 'checked' : '' ?>>
              <label for="visible">Visible en el portafolio</label>
            </div>
          </div>
        </div>

        <button type="submit" class="btn-save"><i class="ti ti-device-floppy"></i> Actualizar habilidad</button>

      </form>

    </div>
  </div>

</main>

<script src="../assets/js/admin.js"></script>
</body>
</html>
