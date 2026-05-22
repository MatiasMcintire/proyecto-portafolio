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

  <!-- CSS propio (Flexbox + Grid + selectores avanzados) -->
  <link rel="stylesheet" href="<?= isset($cssPath) ? $cssPath : '' ?>assets/css/style.css">

  <!-- Devicons: logos de tecnologías -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css">

  <!-- Tabler Icons: iconos genéricos (seguridad, herramientas sin logo oficial) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
</head>

<body>

<!-- ============================================================
     HEADER — Navegación principal
     role="banner" → landmark ARIA para lectores de pantalla
     ============================================================ -->
<header class="nav" role="banner">
  <div class="container">

    <!-- Logo / nombre -->
    <a href="<?= isset($rootPath) ? $rootPath : '' ?>index.php" class="nav__logo" aria-label="Inicio">
      Matias<span>McIntire</span>
    </a>

    <!-- Botón hamburger (visible solo en mobile) -->
    <button
      class="nav__toggle"
      id="navToggle"
      aria-label="Abrir menú de navegación"
      aria-expanded="false"
      aria-controls="navMenu"
    >
      <span></span>
      <span></span>
      <span></span>
    </button>

    <!-- Menú principal -->
    <nav role="navigation" aria-label="Navegación principal">
      <ul class="nav__menu" id="navMenu" role="list">
        <li><a href="<?= isset($rootPath) ? $rootPath : '' ?>index.php#inicio">Inicio</a></li>
        <li><a href="<?= isset($rootPath) ? $rootPath : '' ?>index.php#habilidades">Habilidades</a></li>
        <li><a href="<?= isset($rootPath) ? $rootPath : '' ?>index.php#proyectos">Proyectos</a></li>
        <li><a href="<?= isset($rootPath) ? $rootPath : '' ?>index.php#contacto">Contacto</a></li>
      </ul>
    </nav>

  </div>
</header>
<!-- FIN HEADER -->
