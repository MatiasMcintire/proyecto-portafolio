<?php
/**
 * index.php — Página principal del portafolio
 *
 * Estructura semántica HTML5 completa:
 *   <header> → navegación principal
 *   <main>   → contenido principal
 *     <section id="inicio">       → Hero
 *     <section id="biografia">    → Bio desde DB (tabla perfil)
 *     <section id="habilidades">  → Habilidades desde DB agrupadas por categoría
 *     <section id="tecnologias">  → Mismas habilidades como barras de progreso
 *     <section id="proyectos">    → Proyectos desde DB
 *     <section id="contacto">     → Formulario de contacto
 *   <footer> → pie de página
 */

// Configurar variables para los includes
$pageTitle = 'Inicio';
$pageDesc  = 'Desarrollador Web especializado en PHP, MySQL, JavaScript y diseño responsive. Portafolio de proyectos reales.';
$cssPath   = '';
$jsPath    = '';
$rootPath  = '';

// Conectar a la base de datos para cargar proyectos
require_once 'config/db.php';

// ── Perfil (id = 1) ─────────────────────────────────────────
// Fallback si la tabla aún no existe (ej: bd.sql no reimportado tras Fase 2.4)
$perfil = [
    'nombre'             => 'Matias McIntire',
    'titulo_profesional' => 'Desarrollador Web Full Stack',
    'bio'                => 'Desarrollador web con enfoque en PHP, MySQL y JavaScript. Construyo aplicaciones funcionales, seguras y bien diseñadas.',
    'email_contacto'     => 'Motka2269@gmail.com',
    'telefono'           => '',
    'ubicacion'          => '',
    'github'             => 'https://github.com/MatiasMcintire',
    'linkedin'           => '',
    'foto'               => '',
];
$_chkPerfil = $conn->query("SHOW TABLES LIKE 'perfil'");
if ($_chkPerfil && $_chkPerfil->num_rows > 0) {
    $rp = $conn->query("SELECT * FROM perfil WHERE id = 1 LIMIT 1");
    if ($rp && $rp->num_rows > 0) {
        $perfil = array_merge($perfil, $rp->fetch_assoc());
    }
}

// ── Habilidades agrupadas por categoría ─────────────────────
// Orden preferido de categorías (las no listadas van al final, alfabéticamente)
$ordenCategorias = ['Backend', 'Frontend', 'Base de Datos', 'Herramientas'];
$iconosCategorias = [
    'Backend'        => 'server',
    'Frontend'       => 'monitor',
    'Base de Datos'  => 'database',
    'Herramientas'   => 'wrench',
];
$habilidadesPorCategoria = [];
$_chkHab = $conn->query("SHOW TABLES LIKE 'habilidades'");
if ($_chkHab && $_chkHab->num_rows > 0) {
    $rh = $conn->query(
        "SELECT * FROM habilidades WHERE visible = 1
         ORDER BY categoria ASC, orden ASC, nombre ASC"
    );
    if ($rh) {
        while ($h = $rh->fetch_assoc()) {
            $habilidadesPorCategoria[$h['categoria']][] = $h;
        }
    }
}
// Reordenar según preferencia
$categoriasOrdenadas = [];
foreach ($ordenCategorias as $cat) {
    if (isset($habilidadesPorCategoria[$cat])) {
        $categoriasOrdenadas[$cat] = $habilidadesPorCategoria[$cat];
        unset($habilidadesPorCategoria[$cat]);
    }
}
// Las categorías no listadas se anexan al final
foreach ($habilidadesPorCategoria as $cat => $items) {
    $categoriasOrdenadas[$cat] = $items;
}

// ── Proyectos ───────────────────────────────────────────────
// Obtener proyectos destacados para el home (prepared statement)
$stmt = $conn->prepare("SELECT * FROM proyectos WHERE destacado = 1 ORDER BY orden ASC LIMIT 4");
$stmt->execute();
$proyectosDestacados = $stmt->get_result();
$stmt->close();

// Obtener todos los proyectos para la sección completa
$stmtAll = $conn->prepare("SELECT * FROM proyectos ORDER BY orden ASC");
$stmtAll->execute();
$todosProyectos = $stmtAll->get_result();
$stmtAll->close();
?>
<?php require_once 'includes/header.php'; ?>

<!-- ============================================================
     MAIN — Contenido principal de la página
     role="main" → landmark ARIA
     ============================================================ -->
<main role="main">


  <!-- ──────────────────────────────────────────────────────────
       SECCIÓN 1: HERO — Presentación personal
       Estructura semántica: <section> con aria-labelledby
       Un solo <h1> por página (criterio SEO y accesibilidad)
       ──────────────────────────────────────────────────────────── -->
  <section id="inicio" class="section" aria-labelledby="hero-heading">
    <div class="container">
      <div class="hero__content">

        <!-- Badge de disponibilidad -->
        <span class="hero__badge" role="status">
          Disponible para proyectos
        </span>

        <!-- h1 único de la página — palabra clave al inicio -->
        <h1 id="hero-heading">
          Hola, soy
          <span class="hero__name"><?= htmlspecialchars($perfil['nombre']) ?></span>
        </h1>

        <!-- Subtítulo descriptivo (título profesional desde tabla perfil) -->
        <p>
          <strong><?= htmlspecialchars($perfil['titulo_profesional']) ?></strong>.
          Construyo aplicaciones web funcionales, seguras y bien diseñadas.
        </p>

        <!-- Call to action -->
        <div class="hero__actions">
          <a href="#proyectos" class="btn btn--primary">
            Ver mis proyectos
          </a>
          <a href="#contacto" class="btn btn--outline">
            Contáctame
          </a>
        </div>

      </div><!-- .hero__content -->
    </div><!-- .container -->
  </section>
  <!-- FIN SECCIÓN HERO -->


  <!-- ──────────────────────────────────────────────────────────
       SECCIÓN 2: BIOGRAFÍA
       Información personal desde la tabla `perfil` (id = 1).
       Editable desde /admin/profile.php
       ──────────────────────────────────────────────────────────── -->
  <section id="biografia" class="section section--alt" aria-labelledby="bio-heading">
    <div class="container">

      <h2 id="bio-heading" class="section__title">Biografía</h2>
      <p class="section__subtitle">Quién soy y qué hago</p>

      <div class="bio">

        <!-- Columna izquierda: foto / avatar -->
        <div class="bio__photo">
          <?php if (!empty($perfil['foto']) && file_exists('assets/uploads/' . $perfil['foto'])): ?>
            <img
              src="assets/uploads/<?= htmlspecialchars($perfil['foto']) ?>"
              alt="Foto de <?= htmlspecialchars($perfil['nombre']) ?>"
              loading="lazy"
            >
          <?php else: ?>
            <div class="bio__photo-placeholder" aria-hidden="true">
              <?= htmlspecialchars(mb_strtoupper(mb_substr($perfil['nombre'], 0, 1))) ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Columna derecha: contenido -->
        <div class="bio__content">

          <h3 class="bio__role"><?= htmlspecialchars($perfil['titulo_profesional']) ?></h3>

          <?php if (!empty($perfil['bio'])): ?>
            <p class="bio__text"><?= nl2br(htmlspecialchars($perfil['bio'])) ?></p>
          <?php endif; ?>

          <!-- Datos de contacto (solo los que existen) -->
          <ul class="bio__meta" role="list">
            <?php if (!empty($perfil['ubicacion'])): ?>
              <li><i data-lucide="map-pin" width="16" height="16" aria-hidden="true"></i>
                <?= htmlspecialchars($perfil['ubicacion']) ?></li>
            <?php endif; ?>
            <?php if (!empty($perfil['email_contacto'])): ?>
              <li><i data-lucide="mail" width="16" height="16" aria-hidden="true"></i>
                <a href="mailto:<?= htmlspecialchars($perfil['email_contacto']) ?>">
                  <?= htmlspecialchars($perfil['email_contacto']) ?>
                </a></li>
            <?php endif; ?>
            <?php if (!empty($perfil['telefono'])): ?>
              <li><i data-lucide="phone" width="16" height="16" aria-hidden="true"></i>
                <?= htmlspecialchars($perfil['telefono']) ?></li>
            <?php endif; ?>
          </ul>

          <!-- Redes sociales -->
          <?php if (!empty($perfil['github']) || !empty($perfil['linkedin'])): ?>
            <div class="bio__social">
              <?php if (!empty($perfil['github'])): ?>
                <a href="<?= htmlspecialchars($perfil['github']) ?>"
                   class="btn btn--outline btn--sm"
                   target="_blank" rel="noopener noreferrer"
                   aria-label="GitHub de <?= htmlspecialchars($perfil['nombre']) ?>">
                  <i class="devicon-github-original"></i> GitHub
                </a>
              <?php endif; ?>
              <?php if (!empty($perfil['linkedin'])): ?>
                <a href="<?= htmlspecialchars($perfil['linkedin']) ?>"
                   class="btn btn--outline btn--sm"
                   target="_blank" rel="noopener noreferrer"
                   aria-label="LinkedIn de <?= htmlspecialchars($perfil['nombre']) ?>">
                  <i class="devicon-linkedin-plain colored"></i> LinkedIn
                </a>
              <?php endif; ?>
            </div>
          <?php endif; ?>

        </div><!-- .bio__content -->

      </div><!-- .bio -->

    </div><!-- .container -->
  </section>
  <!-- FIN SECCIÓN BIOGRAFÍA -->


  <!-- ──────────────────────────────────────────────────────────
       SECCIÓN 3: HABILIDADES TÉCNICAS
       Cargadas desde la tabla `habilidades` agrupadas por categoría.
       Editable desde /admin/skills.php
       ──────────────────────────────────────────────────────────── -->
  <section id="habilidades" class="section" aria-labelledby="skills-heading">
    <div class="container">

      <h2 id="skills-heading" class="section__title">Habilidades Técnicas</h2>
      <p class="section__subtitle">Tecnologías con las que trabajo día a día, todo bajo estricta autodisciplina</p>

      <?php if (!empty($categoriasOrdenadas)): ?>

        <!-- Grid de tarjetas de habilidad (una por categoría) -->
        <div class="skills-grid">

          <?php foreach ($categoriasOrdenadas as $categoria => $items): ?>
            <article class="skill-card" aria-label="Habilidades de <?= htmlspecialchars($categoria) ?>">
              <div class="skill-card__icon" aria-hidden="true">
                <i data-lucide="<?= htmlspecialchars($iconosCategorias[$categoria] ?? 'tag') ?>"
                   width="28" height="28"></i>
              </div>
              <h3 class="skill-card__title"><?= htmlspecialchars($categoria) ?></h3>
              <ul role="list">
                <?php foreach ($items as $h): ?>
                  <li>
                    <i class="<?= htmlspecialchars($h['icon_class'] ?: 'ti ti-tag') ?>" aria-hidden="true"></i>
                    <?= htmlspecialchars($h['nombre']) ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </article>
          <?php endforeach; ?>

        </div><!-- .skills-grid -->

      <?php else: ?>
        <p style="text-align:center; color: var(--color-text-muted);">
          Próximamente se agregarán habilidades desde el panel.
        </p>
      <?php endif; ?>

    </div><!-- .container -->
  </section>
  <!-- FIN SECCIÓN HABILIDADES -->


  <!-- ──────────────────────────────────────────────────────────
       SECCIÓN 4: TECNOLOGÍAS — barras de nivel
       Misma fuente de datos (tabla habilidades) pero visualización
       distinta: lista plana con barra de progreso por nivel %.
       ──────────────────────────────────────────────────────────── -->
  <section id="tecnologias" class="section section--alt" aria-labelledby="tech-heading">
    <div class="container">

      <h2 id="tech-heading" class="section__title">Tecnologías</h2>
      <p class="section__subtitle">Nivel de dominio por tecnología</p>

      <?php if (!empty($categoriasOrdenadas)): ?>

        <div class="tech-groups">

          <?php foreach ($categoriasOrdenadas as $categoria => $items): ?>
            <div class="tech-group">
              <h3 class="tech-group__title"><?= htmlspecialchars($categoria) ?></h3>
              <ul class="tech-list" role="list">
                <?php foreach ($items as $h): ?>
                  <li class="tech-item">
                    <span class="tech-item__icon" aria-hidden="true">
                      <i class="<?= htmlspecialchars($h['icon_class'] ?: 'ti ti-tag') ?>"></i>
                    </span>
                    <span class="tech-item__name"><?= htmlspecialchars($h['nombre']) ?></span>
                    <!-- Bootstrap .progress + .progress-bar (theme custom via .tech-item__*) -->
                    <div class="progress tech-item__bar"
                         role="progressbar"
                         aria-valuenow="<?= (int)$h['nivel'] ?>"
                         aria-valuemin="0"
                         aria-valuemax="100"
                         aria-label="Nivel de <?= htmlspecialchars($h['nombre']) ?>">
                      <div class="progress-bar tech-item__fill"
                           style="width: <?= (int)$h['nivel'] ?>%"></div>
                    </div>
                    <span class="tech-item__level"><?= (int)$h['nivel'] ?>%</span>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endforeach; ?>

        </div><!-- .tech-groups -->

      <?php else: ?>
        <p style="text-align:center; color: var(--color-text-muted);">
          Próximamente se agregarán tecnologías desde el panel.
        </p>
      <?php endif; ?>

    </div><!-- .container -->
  </section>
  <!-- FIN SECCIÓN TECNOLOGÍAS -->


  <!-- ──────────────────────────────────────────────────────────
       SECCIÓN 5: PROYECTOS
       Los proyectos se cargan dinámicamente desde MySQL.
       Cada proyecto es un <article> (contenido autónomo).
       ──────────────────────────────────────────────────────────── -->
  <section id="proyectos" class="section" aria-labelledby="projects-heading">
    <div class="container">

      <h2 id="projects-heading" class="section__title">Proyectos Destacados</h2>
      <p class="section__subtitle">Trabajo real con código real</p>

      <?php if ($proyectosDestacados && $proyectosDestacados->num_rows > 0): ?>

        <div class="projects-grid">

          <?php while ($proyecto = $proyectosDestacados->fetch_assoc()): ?>

            <!-- <article> → contenido autónomo (semántica correcta para proyectos)
                 Combina .card (Bootstrap) + .project-card (tema custom: hover, overlay) -->
            <article class="card project-card h-100" aria-label="<?= htmlspecialchars($proyecto['titulo']) ?>">

              <!-- Imagen del proyecto (o placeholder si no tiene) -->
              <div class="card-img-top project-card__img">
                <?php if (!empty($proyecto['imagen']) && file_exists('assets/uploads/' . $proyecto['imagen'])): ?>
                  <img
                    src="assets/uploads/<?= htmlspecialchars($proyecto['imagen']) ?>"
                    alt="Captura del proyecto <?= htmlspecialchars($proyecto['titulo']) ?>"
                    loading="lazy"
                  >
                <?php else: ?>
                  <div class="img-placeholder" aria-hidden="true"><i class="ti ti-photo"></i></div>
                <?php endif; ?>
              </div>

              <!-- Contenido de la tarjeta -->
              <div class="card-body project-card__body">

                <h3 class="card-title project-card__title">
                  <?= htmlspecialchars($proyecto['titulo']) ?>
                </h3>

                <p class="card-text">
                  <?= htmlspecialchars($proyecto['descripcion']) ?>
                </p>

                <!-- Etiquetas de tecnologías -->
                <?php if (!empty($proyecto['tecnologias'])): ?>
                  <div class="project-card__tags" aria-label="Tecnologías usadas">
                    <?php
                      $techs = explode(',', $proyecto['tecnologias']);
                      foreach ($techs as $tech):
                    ?>
                      <span class="tag"><?= htmlspecialchars(trim($tech)) ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

                <!-- Botones de acción -->
                <div class="project-card__links">
                  <?php if (!empty($proyecto['url_github'])): ?>
                    <a
                      href="<?= htmlspecialchars($proyecto['url_github']) ?>"
                      class="btn btn--outline btn--sm"
                      target="_blank"
                      rel="noopener noreferrer"
                      aria-label="Ver código de <?= htmlspecialchars($proyecto['titulo']) ?> en GitHub"
                    >
                      GitHub
                    </a>
                  <?php endif; ?>

                  <?php if (!empty($proyecto['url_produccion'])): ?>
                    <a
                      href="<?= htmlspecialchars($proyecto['url_produccion']) ?>"
                      class="btn btn--primary btn--sm"
                      target="_blank"
                      rel="noopener noreferrer"
                      aria-label="Ver demo en vivo de <?= htmlspecialchars($proyecto['titulo']) ?>"
                    >
                      Demo en vivo
                    </a>
                  <?php endif; ?>
                </div>

              </div><!-- .project-card__body -->

            </article>
            <!-- FIN article.project-card -->

          <?php endwhile; ?>

        </div><!-- .projects-grid -->

      <?php else: ?>
        <p style="text-align:center; color: var(--color-text-muted);">
          Próximamente se agregarán proyectos.
        </p>
      <?php endif; ?>

    </div><!-- .container -->
  </section>
  <!-- FIN SECCIÓN PROYECTOS -->


  <!-- ──────────────────────────────────────────────────────────
       SECCIÓN 6: CONTACTO
       Formulario con validaciones JS avanzadas (25 pts rúbrica).
       Sin alert() — feedback dinámico con mensajes en línea.
       ──────────────────────────────────────────────────────────── -->
  <section id="contacto" class="section" aria-labelledby="contact-heading">
    <div class="container">

      <h2 id="contact-heading" class="section__title">Contacto</h2>
      <p class="section__subtitle">¿Tienes un proyecto en mente? Hablemos.</p>

      <div class="contact-wrapper">

        <!-- Información de contacto (columna izquierda) -->
        <aside class="contact-info" aria-label="Información de contacto">
          <h3 class="contact-info__title">Trabajemos juntos</h3>
          <p>
            Estoy disponible para proyectos freelance, prácticas profesionales
            y oportunidades de trabajo en desarrollo web.
          </p>
          <p>
            Respondo en menos de 24 horas hábiles.
          </p>
          <address>
            <p>
              <i class="ti ti-mail" aria-hidden="true"></i> <a href="mailto:Motka2269@gmail.com">Motka2269@gmail.com</a>
            </p>
            <p>
              <i class="ti ti-brand-github" aria-hidden="true"></i> <a href="https://github.com/MatiasMcintire" target="_blank" rel="noopener">
                github.com/MatiasMcintire
              </a>
            </p>
          </address>
        </aside>

        <!-- Formulario de contacto (columna derecha) -->
        <div class="contact-form-wrapper">

          <!-- Formulario — action apunta al backend PHP que devuelve JSON -->
          <form
            id="contactForm"
            action="api/contact.php"
            method="POST"
            novalidate
            aria-label="Formulario de contacto"
          >

            <!-- Campo: Nombre -->
            <div class="form-group">
              <label for="nombre">
                Nombre completo
                <span aria-hidden="true" style="color:var(--color-error)">*</span>
              </label>
              <input
                type="text"
                id="nombre"
                name="nombre"
                placeholder="Ej: María González"
                required
                data-minlength="3"
                data-maxlength="100"
                autocomplete="name"
                aria-required="true"
                aria-describedby="nombre-error"
              >
              <!-- Mensaje de error (visible solo si hay error, controlado por JS) -->
              <span id="nombre-error" class="error-msg" role="alert"></span>
            </div>

            <!-- Campo: Email -->
            <div class="form-group">
              <label for="email">
                Correo electrónico
                <span aria-hidden="true" style="color:var(--color-error)">*</span>
              </label>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="Ej: maria@dominio.com"
                required
                autocomplete="email"
                aria-required="true"
                aria-describedby="email-error"
              >
              <span id="email-error" class="error-msg" role="alert"></span>
            </div>

            <!-- Campo: Asunto -->
            <div class="form-group">
              <label for="asunto">
                Asunto
                <span aria-hidden="true" style="color:var(--color-error)">*</span>
              </label>
              <input
                type="text"
                id="asunto"
                name="asunto"
                placeholder="Ej: Propuesta de proyecto web"
                required
                data-minlength="5"
                data-maxlength="150"
                aria-required="true"
                aria-describedby="asunto-error"
              >
              <span id="asunto-error" class="error-msg" role="alert"></span>
            </div>

            <!-- Campo: Mensaje (con contador de caracteres) -->
            <div class="form-group">
              <label for="mensaje">
                Mensaje
                <span aria-hidden="true" style="color:var(--color-error)">*</span>
              </label>
              <textarea
                id="mensaje"
                name="mensaje"
                rows="5"
                placeholder="Cuéntame sobre tu proyecto o consulta..."
                required
                data-minlength="20"
                data-maxlength="1000"
                aria-required="true"
                aria-describedby="mensaje-error mensaje-counter"
              ></textarea>
              <span id="mensaje-error" class="error-msg" role="alert"></span>
              <!-- Contador de caracteres actualizado por JS -->
              <span id="mensaje-counter" class="char-counter" aria-live="polite">
                0 / 1000
              </span>
            </div>

            <!-- Botón de envío -->
            <button type="submit" class="btn btn--primary" style="width:100%">
              Enviar mensaje
            </button>

            <!-- Nota de campos obligatorios -->
            <p style="margin-top:0.75rem; font-size:0.82rem; color:var(--color-text-muted); text-align:center;">
              <span aria-hidden="true" style="color:var(--color-error)">*</span>
              Campos obligatorios
            </p>

          </form><!-- #contactForm -->

          <!-- Mensaje de éxito (oculto, JS lo muestra tras envío exitoso) -->
          <div id="formSuccess" class="form-success" aria-live="polite" aria-atomic="true">
            <div class="form-success__icon" aria-hidden="true"></div>
            <h3 class="form-success__title">¡Mensaje enviado con éxito!</h3>
            <p>Gracias por escribirme. Me pondré en contacto contigo pronto.</p>
          </div>

        </div><!-- .contact-form-wrapper -->

      </div><!-- .contact-wrapper -->

    </div><!-- .container -->
  </section>
  <!-- FIN SECCIÓN CONTACTO -->


</main>
<!-- FIN MAIN -->

<?php $conn->close(); ?>
<?php require_once 'includes/footer.php'; ?>
