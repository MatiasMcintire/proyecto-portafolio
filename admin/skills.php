<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$error   = '';
$success = '';

// Verificar si la tabla habilidades existe
$_check      = $conn->query("SHOW TABLES LIKE 'habilidades'");
$tableExists = ($_check && $_check->num_rows > 0);

$habilidades = null;
if ($tableExists) {
    $habilidades = $conn->query(
        "SELECT * FROM habilidades ORDER BY categoria ASC, orden ASC, nombre ASC"
    );
}

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Habilidades — Panel Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <style>
    .nivel-bar {
      height: 6px;
      background: #e2e8f0;
      border-radius: 999px;
      width: 100px;
      overflow: hidden;
      display: inline-block;
      vertical-align: middle;
    }
    .nivel-bar__fill {
      height: 100%;
      background: #3b82f6;
      border-radius: 999px;
    }
  </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<main class="main">

  <div class="page-header">
    <div>
      <h1>🛠️ Habilidades</h1>
      <p>Gestiona las habilidades técnicas de tu portafolio</p>
    </div>
    <?php if ($tableExists): ?>
      <a href="skills_add.php" class="btn-add">+ Nueva habilidad</a>
    <?php endif; ?>
  </div>

  <?php if ($msg === 'creada'):   ?><div class="alert alert-ok">✅ Habilidad agregada.</div><?php endif; ?>
  <?php if ($msg === 'editada'):  ?><div class="alert alert-ok">✅ Habilidad actualizada.</div><?php endif; ?>
  <?php if ($msg === 'eliminada'):?><div class="alert alert-del">🗑️ Habilidad eliminada.</div><?php endif; ?>

  <?php if (!$tableExists): ?>
  <!-- ── Instrucciones de primer uso ── -->
  <div class="sql-instructions">
    <strong>⚠️ Primer uso — Crea la tabla en phpMyAdmin</strong><br>
    Copia y ejecuta este SQL en <strong>phpMyAdmin → SQL</strong>:
  </div>
  <div class="sql-box">CREATE TABLE IF NOT EXISTS `habilidades` (
  `id`        INT            AUTO_INCREMENT PRIMARY KEY,
  `categoria` VARCHAR(80)    NOT NULL,
  `nombre`    VARCHAR(80)    NOT NULL,
  `nivel`     TINYINT UNSIGNED NOT NULL DEFAULT 80,
  `icono`     VARCHAR(10)    DEFAULT '⚙️',
  `orden`     SMALLINT       DEFAULT 0,
  `visible`   TINYINT(1)     DEFAULT 1,
  `created_at` TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- Habilidades de ejemplo (puedes editarlas o eliminarlas)
INSERT INTO `habilidades` (categoria, nombre, nivel, icono, orden) VALUES
  ('Frontend',     'HTML5',         90, '🌐', 1),
  ('Frontend',     'CSS3',          85, '🎨', 2),
  ('Frontend',     'JavaScript',    80, '⚡', 3),
  ('Backend',      'PHP',           80, '🐘', 1),
  ('Backend',      'MySQL',         75, '🗄️', 2),
  ('Herramientas', 'Git',           70, '🔧', 1),
  ('Herramientas', 'XAMPP',         75, '💻', 2);</div>
  <p style="color:#64748b; font-size:.88rem; margin-top:.5rem;">
    Después de ejecutar el SQL, recarga esta página.
  </p>

  <?php else: ?>

  <div class="card">
    <div class="card__header">
      <h2>Lista de habilidades</h2>
      <a href="skills_add.php" class="btn-add">+ Agregar</a>
    </div>
    <div class="card__body">
      <?php if ($habilidades && $habilidades->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Icono</th>
              <th>Habilidad</th>
              <th>Categoría</th>
              <th>Nivel</th>
              <th>Orden</th>
              <th>Visible</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($h = $habilidades->fetch_assoc()): ?>
              <tr>
                <td style="font-size:1.4rem; text-align:center; padding:.5rem;">
                  <?= htmlspecialchars($h['icono'] ?? '⚙️') ?>
                </td>
                <td><strong><?= htmlspecialchars($h['nombre']) ?></strong></td>
                <td>
                  <span class="badge badge-blue"><?= htmlspecialchars($h['categoria']) ?></span>
                </td>
                <td>
                  <span style="font-size:.8rem; color:#64748b; margin-right:.4rem;">
                    <?= (int)$h['nivel'] ?>%
                  </span>
                  <span class="nivel-bar">
                    <span class="nivel-bar__fill" style="width:<?= (int)$h['nivel'] ?>%"></span>
                  </span>
                </td>
                <td style="color:#94a3b8; font-size:.82rem;"><?= (int)$h['orden'] ?></td>
                <td>
                  <?php if ($h['visible']): ?>
                    <span class="badge badge-green">Visible</span>
                  <?php else: ?>
                    <span class="badge badge-gray">Oculto</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
                    <a href="skills_edit.php?id=<?= $h['id'] ?>" class="btn-xs btn-edit">✏️ Editar</a>
                    <a href="skills_delete.php?id=<?= $h['id'] ?>"
                       class="btn-xs btn-delete"
                       onclick="return confirm('¿Eliminar «<?= htmlspecialchars(addslashes($h['nombre'])) ?>»?')">
                      🗑️ Eliminar
                    </a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty">
          <div class="empty-icon">🛠️</div>
          <p>No hay habilidades registradas. <a href="skills_add.php">Agrega la primera</a>.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php endif; ?>

</main>

<script src="../assets/js/admin.js"></script>
</body>
</html>
