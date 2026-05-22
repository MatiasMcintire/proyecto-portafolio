<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $categoria = trim($_POST['categoria'] ?? '');
    $nombre    = trim($_POST['nombre']    ?? '');
    $nivel     = (int)($_POST['nivel']    ?? 80);
    $icono     = trim($_POST['icono']     ?? '⚙️');
    $orden     = (int)($_POST['orden']    ?? 0);
    $visible   = isset($_POST['visible']) ? 1 : 0;

    if (empty($categoria)) {
        $error = 'La categoría es obligatoria.';
    } elseif (empty($nombre)) {
        $error = 'El nombre de la habilidad es obligatorio.';
    } elseif ($nivel < 0 || $nivel > 100) {
        $error = 'El nivel debe estar entre 0 y 100.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO habilidades (categoria, nombre, nivel, icono, orden, visible)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('ssisii', $categoria, $nombre, $nivel, $icono, $orden, $visible);
        $stmt->execute();
        $stmt->close();
        header('Location: skills.php?msg=creada');
        exit;
    }
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
  <title>Nueva Habilidad — Panel Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<main class="main">

  <div class="page-header">
    <div>
      <h1>🛠️ Nueva Habilidad</h1>
      <p>Agrega una habilidad técnica a tu portafolio</p>
    </div>
    <a href="skills.php" class="btn-add" style="background:#64748b">← Volver</a>
  </div>

  <?php if ($error): ?><div class="alert alert-del"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <div class="card" style="max-width:560px">
    <div class="card__header"><h2>Datos de la habilidad</h2></div>
    <div class="card__body" style="padding:1.5rem">

      <form method="POST" action="skills_add.php" novalidate>

        <div class="form-grid-2">
          <div class="form-row">
            <label for="categoria">Categoría *</label>
            <input type="text" id="categoria" name="categoria" required
                   list="cat-list"
                   value="<?= htmlspecialchars($_POST['categoria'] ?? '') ?>"
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
                   value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                   placeholder="Ej: PHP, JavaScript, Git">
          </div>
        </div>

        <div class="form-grid-2">
          <div class="form-row">
            <label for="icono">Icono <small>(emoji)</small></label>
            <input type="text" id="icono" name="icono"
                   value="<?= htmlspecialchars($_POST['icono'] ?? '⚙️') ?>"
                   placeholder="⚙️"
                   style="font-size:1.4rem; text-align:center;">
            <div class="hint">Copia un emoji. Ej: 🌐 🎨 ⚡ 🐘 🗄️ 🔧</div>
          </div>

          <div class="form-row">
            <label for="nivel">Nivel de dominio: <strong id="nivel-val"><?= (int)($_POST['nivel'] ?? 80) ?>%</strong></label>
            <input type="range" id="nivel" name="nivel"
                   min="0" max="100" step="5"
                   value="<?= (int)($_POST['nivel'] ?? 80) ?>"
                   oninput="document.getElementById('nivel-val').textContent = this.value + '%'"
                   style="width:100%; accent-color:#3b82f6; margin-top:.5rem;">
          </div>
        </div>

        <div class="form-grid-2">
          <div class="form-row">
            <label for="orden">Orden de visualización</label>
            <input type="number" id="orden" name="orden" min="0" max="999"
                   value="<?= (int)($_POST['orden'] ?? 0) ?>"
                   placeholder="0">
            <div class="hint">Menor número = aparece primero.</div>
          </div>

          <div class="form-row" style="display:flex; flex-direction:column; justify-content:center;">
            <label>&nbsp;</label>
            <div class="form-check">
              <input type="checkbox" id="visible" name="visible" value="1"
                     <?= (!isset($_POST['visible']) || $_POST['visible']) ? 'checked' : '' ?>>
              <label for="visible">Visible en el portafolio</label>
            </div>
          </div>
        </div>

        <button type="submit" class="btn-save">💾 Guardar habilidad</button>

      </form>

    </div>
  </div>

</main>

<script src="../assets/js/admin.js"></script>
</body>
</html>
