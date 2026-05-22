<?php
/**
 * index.php — Página principal del portafolio
 *
 * Estructura semántica HTML5 completa:
 *   <header> → navegación principal
 *   <main>   → contenido principal
 *     <section id="inicio">     → Hero
 *     <section id="habilidades"> → Skills
 *     <section id="proyectos">  → Proyectos desde DB
 *     <section id="contacto">   → Formulario de contacto
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
          <span class="hero__name">Matias McIntire</span>
        </h1>

        <!-- Subtítulo descriptivo -->
        <p>
          Desarrollador web con enfoque en <strong>PHP</strong>,
          <strong>MySQL</strong> y <strong>JavaScript</strong>.
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
       SECCIÓN 2: HABILIDADES TÉCNICAS
       Usa <section> + <h2> (jerarquía correcta después del h1)
       Las habilidades se presentan como listas semánticas <ul><li>
       ──────────────────────────────────────────────────────────── -->
  <section id="habilidades" class="section" aria-labelledby="skills-heading">
    <div class="container">

      <h2 id="skills-heading" class="section__title">Habilidades Técnicas</h2>
      <p class="section__subtitle">Tecnologías con las que trabajo día a día, todo bajo estricta autodisciplina</p>

      <!-- Grid de tarjetas de habilidad -->
      <div class="skills-grid">

        <!-- Tarjeta: Backend -->
        <article class="skill-card" aria-label="Habilidades de Backend">
          <div class="skill-card__icon" aria-hidden="true">
            <i data-lucide="server" width="28" height="28"></i>
          </div>
          <h3 class="skill-card__title">Backend</h3>
          <ul role="list">
            <li><i class="devicon-php-plain colored"></i> PHP 8</li>
            <li><i class="devicon-mysql-plain colored"></i> MySQL</li>
            <li><i class="devicon-nestjs-plain colored"></i> NestJS</li>
            <li><i class="ti ti-api"></i> REST APIs</li>
            <li><i class="ti ti-shield-lock"></i> Prepared Statements</li>
            <li><i class="ti ti-shield-exclamation"></i> OWASP Top 10 (básico)</li>
            <li><i class="ti ti-lock"></i> Seguridad en REST APIs</li>
          </ul>
        </article>

        <!-- Tarjeta: Frontend -->
        <article class="skill-card" aria-label="Habilidades de Frontend">
          <div class="skill-card__icon" aria-hidden="true">
            <i data-lucide="monitor" width="28" height="28"></i>
          </div>
          <h3 class="skill-card__title">Frontend</h3>
          <ul role="list">
            <li><i class="devicon-html5-plain colored"></i> HTML5 Semántico</li>
            <li><i class="devicon-css3-plain colored"></i> CSS3 + Flexbox</li>
            <li><i class="devicon-javascript-plain colored"></i> JavaScript ES6</li>
            <li><i class="devicon-nextjs-plain"></i> Next.js 14</li>
            <li><i class="devicon-tailwindcss-plain colored"></i> Tailwind CSS</li>
          </ul>
        </article>

        <!-- Tarjeta: Base de datos -->
        <article class="skill-card" aria-label="Habilidades de Base de Datos">
          <div class="skill-card__icon" aria-hidden="true">
            <i data-lucide="database" width="28" height="28"></i>
          </div>
          <h3 class="skill-card__title">Base de Datos</h3>
          <ul role="list">
            <li><i class="devicon-mysql-plain colored"></i> MySQL / phpMyAdmin</li>
            <li><i class="devicon-postgresql-plain colored"></i> PostgreSQL</li>
            <li>Prisma ORM</li>
            <li>Diseño relacional</li>
          </ul>
        </article>

        <!-- Tarjeta: Herramientas -->
        <article class="skill-card" aria-label="Herramientas de desarrollo">
          <div class="skill-card__icon" aria-hidden="true">
            <i data-lucide="wrench" width="28" height="28"></i>
          </div>
          <h3 class="skill-card__title">Herramientas</h3>
          <ul role="list">
            <li><i class="devicon-git-plain colored"></i> Git / GitHub</li>
            <li><i class="devicon-vscode-plain colored"></i> VS Code</li>
            <li><i class="devicon-apache-plain colored"></i> XAMPP / cPanel</li>
            <li><i class="devicon-docker-plain colored"></i> Docker</li>
            <li><i class="ti ti-moon"></i> Insomnia</li>
            <li><i class="devicon-linux-plain"></i> Kali Linux</li>
            <li><i class="ti ti-scan"></i> Nmap</li>
            <li><i class="ti ti-terminal"></i> Metasploit Framework</li>
            <li><i class="ti ti-wave-square"></i> Wireshark</li>
          </ul>
        </article>

      </div><!-- .skills-grid -->

    </div><!-- .container -->
  </section>
  <!-- FIN SECCIÓN HABILIDADES -->


  <!-- ──────────────────────────────────────────────────────────
       SECCIÓN 3: PROYECTOS
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

            <!-- <article> → contenido autónomo (semántica correcta para proyectos) -->
            <article class="project-card" aria-label="<?= htmlspecialchars($proyecto['titulo']) ?>">

              <!-- Imagen del proyecto (o placeholder si no tiene) -->
              <div class="project-card__img">
                <?php if (!empty($proyecto['imagen']) && file_exists('assets/uploads/' . $proyecto['imagen'])): ?>
                  <img
                    src="assets/uploads/<?= htmlspecialchars($proyecto['imagen']) ?>"
                    alt="Captura del proyecto <?= htmlspecialchars($proyecto['titulo']) ?>"
                    loading="lazy"
                  >
                <?php else: ?>
                  <div class="img-placeholder" aria-hidden="true">💻</div>
                <?php endif; ?>
              </div>

              <!-- Contenido de la tarjeta -->
              <div class="project-card__body">

                <h3 class="project-card__title">
                  <?= htmlspecialchars($proyecto['titulo']) ?>
                </h3>

                <p>
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
       SECCIÓN 4: CONTACTO
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
              📧 <a href="mailto:Motka2269@gmail.com">Motka2269@gmail.com</a>
            </p>
            <p>
              💼 <a href="https://github.com/MatiasMcintire" target="_blank" rel="noopener">
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
