<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

// ── Acciones sobre mensajes ─────────────────────────────────
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $mid    = (int)$_GET['id'];

    if ($mid > 0) {
        if ($action === 'toggle_read') {
            $stmt = $conn->prepare("UPDATE contactos SET leido = NOT leido WHERE id = ?");
            $stmt->bind_param('i', $mid);
            $stmt->execute();
            $stmt->close();
        }
        if ($action === 'delete_msg') {
            $stmt = $conn->prepare("DELETE FROM contactos WHERE id = ?");
            $stmt->bind_param('i', $mid);
            $stmt->execute();
            $stmt->close();
        }
    }
    header('Location: index.php');
    exit;
}

// ── Estadísticas ────────────────────────────────────────────
$stats = [];

$r = $conn->query("SELECT COUNT(*) AS n FROM proyectos");
$stats['proyectos'] = (int)$r->fetch_assoc()['n'];

$r = $conn->query("SELECT COUNT(*) AS n FROM proyectos WHERE destacado = 1");
$stats['destacados'] = (int)$r->fetch_assoc()['n'];

$r = $conn->query("SELECT COUNT(*) AS n FROM contactos");
$stats['mensajes'] = (int)$r->fetch_assoc()['n'];

$r = $conn->query("SELECT COUNT(*) AS n FROM contactos WHERE leido = 0");
$stats['no_leidos'] = (int)$r->fetch_assoc()['n'];

// ── Datos ───────────────────────────────────────────────────
$proyectos = $conn->query("SELECT * FROM proyectos ORDER BY orden ASC, created_at DESC");
$contactos = $conn->query("SELECT * FROM contactos ORDER BY leido ASC, created_at DESC");

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Panel Admin</title>
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

  <!-- Encabezado -->
  <div class="page-header">
    <div>
      <h1>Dashboard</h1>
      <p>Bienvenido, <?= htmlspecialchars($_SESSION['admin_user']) ?> — <?= date('d \d\e F\, Y') ?></p>
    </div>
    <a href="add.php" class="btn-add"><i class="ti ti-plus"></i> Nuevo proyecto</a>
  </div>

  <!-- Alertas de acciones -->
  <?php if ($msg === 'creado'):    ?><div class="alert alert-ok"><i class="ti ti-circle-check"></i> Proyecto agregado correctamente.</div><?php endif; ?>
  <?php if ($msg === 'editado'):   ?><div class="alert alert-ok"><i class="ti ti-circle-check"></i> Proyecto actualizado correctamente.</div><?php endif; ?>
  <?php if ($msg === 'eliminado'): ?><div class="alert alert-del"><i class="ti ti-trash"></i> Proyecto eliminado.</div><?php endif; ?>

  <!-- ── Stats ── -->
  <div class="stats">
    <div class="stat">
      <div class="stat__icon"><i class="ti ti-briefcase"></i></div>
      <div>
        <div class="stat__value"><?= $stats['proyectos'] ?></div>
        <div class="stat__label">Proyectos totales</div>
      </div>
    </div>
    <div class="stat">
      <div class="stat__icon"><i class="ti ti-star"></i></div>
      <div>
        <div class="stat__value"><?= $stats['destacados'] ?></div>
        <div class="stat__label">Destacados en home</div>
      </div>
    </div>
    <div class="stat">
      <div class="stat__icon"><i class="ti ti-mail"></i></div>
      <div>
        <div class="stat__value"><?= $stats['mensajes'] ?></div>
        <div class="stat__label">Mensajes recibidos</div>
      </div>
    </div>
    <div class="stat">
      <div class="stat__icon"><i class="ti ti-bell"></i></div>
      <div>
        <div class="stat__value"><?= $stats['no_leidos'] ?></div>
        <div class="stat__label">Sin leer</div>
      </div>
    </div>
  </div>

  <!-- ════════════════════════════════════════
       PROYECTOS
  ════════════════════════════════════════ -->
  <div class="card" id="sec-proyectos">
    <div class="card__header">
      <h2><i class="ti ti-folder"></i> Proyectos</h2>
      <a href="add.php" class="btn-add"><i class="ti ti-plus"></i> Agregar</a>
    </div>
    <div class="card__body">
      <?php if ($proyectos && $proyectos->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Imagen</th>
              <th>Título</th>
              <th>Tecnologías</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($p = $proyectos->fetch_assoc()): ?>
              <tr>
                <td>
                  <?php if (!empty($p['imagen']) && file_exists('../assets/uploads/' . $p['imagen'])): ?>
                    <div class="td-img">
                      <img src="../assets/uploads/<?= htmlspecialchars($p['imagen']) ?>"
                           alt="<?= htmlspecialchars($p['titulo']) ?>">
                    </div>
                  <?php else: ?>
                    <div class="td-img"><i class="ti ti-photo"></i></div>
                  <?php endif; ?>
                </td>
                <td>
                  <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                  <div style="font-size:.78rem; color:#94a3b8; margin-top:.2rem;">
                    <?= date('d/m/Y', strtotime($p['created_at'])) ?>
                  </div>
                </td>
                <td style="max-width:200px; color:#64748b; font-size:.8rem; line-height:1.5;">
                  <?= htmlspecialchars($p['tecnologias'] ?? '—') ?>
                </td>
                <td>
                  <?php if ($p['destacado']): ?>
                    <span class="pill-destacado"><i class="ti ti-star"></i> Destacado</span>
                  <?php else: ?>
                    <span class="pill-normal">Normal</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
                    <a href="edit.php?id=<?= $p['id'] ?>"
                       class="btn-xs btn-edit"
                       aria-label="Editar <?= htmlspecialchars($p['titulo']) ?>"
                       title="Editar"><i class="ti ti-edit"></i></a>
                    <a href="delete.php?id=<?= $p['id'] ?>"
                       class="btn-xs btn-delete"
                       aria-label="Eliminar <?= htmlspecialchars($p['titulo']) ?>"
                       title="Eliminar"
                       onclick="return confirm('¿Eliminar «<?= htmlspecialchars(addslashes($p['titulo'])) ?>»?')"><i class="ti ti-trash"></i></a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty">
          <div class="empty-icon"><i class="ti ti-folder-off"></i></div>
          <p>No hay proyectos todavía. <a href="add.php">Agrega el primero</a>.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ════════════════════════════════════════
       MENSAJES DE CONTACTO
  ════════════════════════════════════════ -->
  <div class="card" id="sec-mensajes">
    <div class="card__header">
      <h2>
        <i class="ti ti-mail"></i> Mensajes de Contacto
        <?php if ($stats['no_leidos'] > 0): ?>
          <span class="badge badge-red" style="margin-left:.5rem">
            <?= $stats['no_leidos'] ?> nuevos
          </span>
        <?php endif; ?>
      </h2>
      <?php if ($stats['mensajes'] > 0): ?>
        <span style="font-size:12px; color:var(--text-muted);">
          <?= $stats['mensajes'] ?> mensaje<?= $stats['mensajes'] != 1 ? 's' : '' ?> en total
        </span>
      <?php endif; ?>
    </div>
    <div class="card__body">
      <?php if ($contactos && $contactos->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Estado</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Asunto</th>
              <th>Fecha</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($c = $contactos->fetch_assoc()):
              $rowClass = !$c['leido'] ? 'unread' : '';
              $msgId    = (int)$c['id'];
            ?>

              <tr class="<?= $rowClass ?>">
                <td>
                  <?php if (!$c['leido']): ?>
                    <span class="badge badge-blue">Nuevo</span>
                  <?php else: ?>
                    <span class="badge badge-gray">Leído</span>
                  <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($c['nombre']) ?></strong></td>
                <td>
                  <a href="mailto:<?= htmlspecialchars($c['email']) ?>" style="color:#2563eb;">
                    <?= htmlspecialchars($c['email']) ?>
                  </a>
                </td>
                <td><?= htmlspecialchars($c['asunto'] ?? '—') ?></td>
                <td style="white-space:nowrap; color:#94a3b8; font-size:.8rem;">
                  <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                </td>
                <td>
                  <div style="display:flex; gap:.4rem; flex-wrap:wrap;">

                    <button class="btn-xs btn-view"
                            onclick="toggleMsg(<?= $msgId ?>)"
                            aria-expanded="false"
                            aria-label="Ver mensaje"
                            title="Ver"
                            id="btn-view-<?= $msgId ?>"><i class="ti ti-eye"></i></button>

                    <a href="index.php?action=toggle_read&id=<?= $msgId ?>"
                       class="btn-xs <?= $c['leido'] ? 'btn-unread' : 'btn-read' ?>"
                       aria-label="<?= $c['leido'] ? 'Marcar como no leído' : 'Marcar como leído' ?>"
                       title="<?= $c['leido'] ? 'Marcar como no leído' : 'Marcar como leído' ?>"><i class="ti <?= $c['leido'] ? 'ti-arrow-back-up' : 'ti-circle-check' ?>"></i></a>

                    <a href="index.php?action=delete_msg&id=<?= $msgId ?>"
                       class="btn-xs btn-delete"
                       aria-label="Eliminar mensaje"
                       title="Eliminar"
                       onclick="return confirm('¿Eliminar este mensaje? Esta acción no se puede deshacer.')"><i class="ti ti-trash"></i></a>

                  </div>
                </td>
              </tr>

              <tr class="msg-body" id="msg-body-<?= $msgId ?>">
                <td colspan="6">
                  <strong class="msg-body__title">
                    <i class="ti ti-message"></i> Mensaje de <?= htmlspecialchars($c['nombre']) ?>:
                  </strong>
                  <p style="white-space:pre-wrap;"><?= htmlspecialchars($c['mensaje']) ?></p>
                  <div class="msg-body__meta">
                    <span><i class="ti ti-mail"></i><?= htmlspecialchars($c['email']) ?></span>
                    <span><i class="ti ti-clock"></i><?= date('d/m/Y \a \l\a\s H:i', strtotime($c['created_at'])) ?></span>
                  </div>
                </td>
              </tr>

            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty">
          <div class="empty-icon"><i class="ti ti-mailbox"></i></div>
          <p>Aún no hay mensajes de contacto.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

</main>

<script src="../assets/js/admin.js"></script>
<script>
function toggleMsg(id) {
  var row = document.getElementById('msg-body-' + id);
  var btn = document.getElementById('btn-view-' + id);
  if (!row) return;
  var isOpen = row.classList.toggle('open');
  btn.setAttribute('aria-expanded', isOpen);
  btn.setAttribute('title', isOpen ? 'Cerrar' : 'Ver');
  btn.setAttribute('aria-label', isOpen ? 'Cerrar mensaje' : 'Ver mensaje');
  btn.innerHTML = isOpen
    ? '<i class="ti ti-chevron-up"></i>'
    : '<i class="ti ti-eye"></i>';
}
</script>

</body>
</html>
