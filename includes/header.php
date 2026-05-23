<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- SEO: Título dinámico por página -->
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Matias McIntire — Desarrollador Web</title>

  <!-- SEO: Meta descripción -->
  <meta name="description" content="<?= isset($pageDesc) ? htmlspecialchars($pageDesc) : 'Portafolio profesional de desarrollo web. HTML5, CSS3, JavaScript, PHP y MySQL.' ?>">

  <!-- SEO: Canonical URL -->
  <link rel="canonical" href="https://tu-dominio.cl/">

  <!-- Accesibilidad: Idioma declarado -->
  <!-- lang="es" en <html> cumple criterio WCAG 3.1.1 -->

  <!-- Preconnect para Google Fonts (performance) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 (framework CSS — base, sobreescrito por style.css) -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

  <!-- CSS propio (Flexbox + Grid + selectores avanzados, overrides de Bootstrap) -->
  <link rel="stylesheet" href="<?= isset($cssPath) ? $cssPath : '' ?>assets/css/style.css">

  <!-- Devicons: logos de tecnologías -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css">

  <!-- Tabler Icons: iconos genéricos (seguridad, herramientas sin logo oficial) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
</head>

<body>

<!-- ============================================================
     HEADER — Navegación principal (Bootstrap 5.3 navbar)
     role="banner" → landmark ARIA para lectores de pantalla
     ============================================================ -->
<header role="banner">
  <nav class="navbar navbar-expand-lg sticky-top navbar-custom" aria-label="Navegación principal">
    <div class="container">

      <!-- Logo / brand -->
      <a class="navbar-brand nav__logo"
         href="<?= isset($rootPath) ? $rootPath : '' ?>index.php"
         aria-label="Inicio">
        Matias<span>McIntire</span>
      </a>

      <!-- Botón toggler de Bootstrap (visible <992px) -->
      <button class="navbar-toggler"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#navMenu"
              aria-controls="navMenu"
              aria-expanded="false"
              aria-label="Abrir menú de navegación">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menú principal (colapsable en mobile, lo gestiona bootstrap.bundle.js) -->
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto" role="list">
          <li class="nav-item"><a class="nav-link" href="<?= isset($rootPath) ? $rootPath : '' ?>index.php#inicio">Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= isset($rootPath) ? $rootPath : '' ?>index.php#biografia">Biografía</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= isset($rootPath) ? $rootPath : '' ?>index.php#habilidades">Habilidades</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= isset($rootPath) ? $rootPath : '' ?>index.php#tecnologias">Tecnologías</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= isset($rootPath) ? $rootPath : '' ?>index.php#proyectos">Proyectos</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= isset($rootPath) ? $rootPath : '' ?>index.php#contacto">Contacto</a></li>
          <li class="nav-item">
            <a class="nav-link nav__login"
               href="<?= isset($rootPath) ? $rootPath : '' ?>admin/login.php"
               aria-label="Iniciar sesión como administrador">
              <i data-lucide="lock" width="14" height="14" aria-hidden="true"></i>
              Iniciar sesión
            </a>
          </li>
        </ul>
      </div>

    </div>
  </nav>
</header>
<!-- FIN HEADER -->
