-- ============================================================
-- PORTAFOLIO PROFESIONAL — Base de Datos
-- Asignatura: Diseño y Desarrollo Web + IA (TECLAB)
-- Autor: Matías McIntire
--
-- INSTRUCCIONES DE INSTALACIÓN:
--   1. Abrir phpMyAdmin
--   2. Importar este archivo (NO crear la BD a mano,
--      el script ya hace CREATE DATABASE)
--   3. Verificar que se hayan creado las 5 tablas:
--        - proyectos
--        - usuarios
--        - contactos
--        - perfil
--        - habilidades
--
-- ============================================================
-- !!! IMPORTANTE — USUARIO ADMIN POR DEFECTO (CAMBIAR TRAS EL PRIMER LOGIN) !!!
-- ============================================================
--   usuario:  admin
--   password: Admin2024!
--
--   >> Esta credencial es SOLO PARA DESARROLLO / SEMILLA INICIAL.
--      En el primer arranque despues de importar este script:
--        1. Iniciar sesion en /admin/login.php con admin / Admin2024!
--        2. Ir a /admin/change_password.php y cambiarla por una
--           contrasena fuerte (>=12 caracteres, mezcla de tipos).
--        3. NUNCA dejar Admin2024! activa en produccion.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `portafolio_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `portafolio_db`;

-- ------------------------------------------------------------
-- Tabla: proyectos
-- Almacena los proyectos del portafolio público
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id`              INT(11)      NOT NULL AUTO_INCREMENT,
  `titulo`          VARCHAR(255) NOT NULL,
  `descripcion`     TEXT         NOT NULL,
  `tecnologias`     VARCHAR(500) DEFAULT NULL COMMENT 'Lista separada por comas',
  `url_github`      VARCHAR(500) DEFAULT NULL,
  `url_produccion`  VARCHAR(500) DEFAULT NULL,
  `imagen`          VARCHAR(255) DEFAULT NULL,
  `destacado`       TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1 = aparece en home',
  `orden`           INT(11)      NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
                                          ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: usuarios
-- Solo administrador del panel (password con bcrypt, no MD5)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)  NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL COMMENT 'password_hash() con PASSWORD_DEFAULT',
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: contactos
-- Mensajes recibidos desde el formulario público de contacto
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contactos` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `nombre`     VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `asunto`     VARCHAR(200) NOT NULL,
  `mensaje`    TEXT         NOT NULL,
  `leido`      TINYINT(1)   NOT NULL DEFAULT 0,
  `ip`         VARCHAR(45)  DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: perfil
-- Información biográfica del dueño del portafolio
-- Se gestiona desde /admin/profile.php
-- Solo hay UNA fila (id = 1)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `perfil` (
  `id`                  INT          NOT NULL AUTO_INCREMENT,
  `nombre`              VARCHAR(100) NOT NULL DEFAULT '',
  `titulo_profesional`  VARCHAR(150) NOT NULL DEFAULT '',
  `bio`                 TEXT,
  `email_contacto`      VARCHAR(120) DEFAULT '',
  `telefono`            VARCHAR(30)  DEFAULT '',
  `ubicacion`           VARCHAR(100) DEFAULT '',
  `github`              VARCHAR(200) DEFAULT '',
  `linkedin`            VARCHAR(200) DEFAULT '',
  `foto`                VARCHAR(255) DEFAULT '',
  `updated_at`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
                                              ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: habilidades
-- Habilidades técnicas agrupadas por categoría con nivel %
-- Se gestiona desde /admin/skills.php
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `habilidades` (
  `id`         INT              NOT NULL AUTO_INCREMENT,
  `categoria`  VARCHAR(80)      NOT NULL,
  `nombre`     VARCHAR(80)      NOT NULL,
  `nivel`      TINYINT UNSIGNED NOT NULL DEFAULT 80 COMMENT '0-100',
  `icono`      VARCHAR(10)      DEFAULT '⚙️' COMMENT 'Emoji para sección Tecnologías',
  `icon_class` VARCHAR(100)     DEFAULT ''  COMMENT 'Clase devicon o tabler-icons para sección Habilidades',
  `orden`      SMALLINT         DEFAULT 0,
  `visible`    TINYINT(1)       DEFAULT 1,
  `created_at` TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATOS INICIALES (seed)
-- ============================================================

-- Usuario admin (password: Admin2024!)
-- Hash generado con: password_hash('Admin2024!', PASSWORD_DEFAULT) en PHP 8.2
INSERT INTO `usuarios` (`username`, `password`) VALUES
('admin', '$2y$10$LG0mlrCgVrtB.rLW6I4AHealKWUOu2VxXnyFQCz9SaKnFvSl49PAW');

-- Perfil (fila única con id = 1, se completa desde /admin/profile.php)
INSERT IGNORE INTO `perfil` (`id`, `nombre`, `titulo_profesional`, `bio`) VALUES
(1,
 'Matías McIntire',
 'Técnico Universitario en Informática',
 'Estudiante de Técnico Universitario en Informática en la Universidad Católica de Temuco. Apasionado por el desarrollo web full-stack, con foco en PHP, MySQL y JavaScript. Este portafolio reúne mis proyectos del cursado.');

-- Proyectos de ejemplo
INSERT INTO `proyectos`
  (`titulo`, `descripcion`, `tecnologias`, `url_github`, `url_produccion`, `destacado`, `orden`)
VALUES
('Rift Royale — Plataforma de Torneos',
 'Plataforma web completa para gestionar torneos de League of Legends con premios en efectivo. Incluye sistema de equipos, brackets automáticos, verificación de identidad, gestión de pagos y panel de administración completo.',
 'Next.js 14,NestJS,TypeScript,PostgreSQL,Prisma,JWT,Tailwind CSS',
 'https://github.com/MatiasMcintire/rift-royale', '', 1, 1),
('Blog CMS — Sistema de Gestión de Contenidos',
 'Sistema CMS para publicar y gestionar artículos de blog. Implementa autenticación de usuarios, subida de imágenes, categorías y CRUD completo con PHP y MySQL.',
 'PHP,MySQL,Bootstrap 5,HTML5,CSS3,JavaScript',
 '', '', 1, 2),
('Portafolio Web Profesional',
 'Este mismo portafolio: diseñado con HTML semántico, CSS personalizado (Flexbox + Grid), validaciones JavaScript avanzadas y backend PHP + MySQL.',
 'PHP,MySQL,HTML5,CSS3,JavaScript,Bootstrap 5',
 '', '', 0, 3);

-- Habilidades de ejemplo (editables desde /admin/skills.php)
-- icon_class usa Devicons (https://devicon.dev) o Tabler Icons (ti ti-*).
INSERT INTO `habilidades`
  (`categoria`, `nombre`, `nivel`, `icono`, `icon_class`, `orden`) VALUES
-- Backend
('Backend',        'PHP 8',                       85, '🐘', 'devicon-php-plain colored',          1),
('Backend',        'MySQL',                       80, '🗄️', 'devicon-mysql-plain colored',         2),
('Backend',        'NestJS',                      70, '🦅', 'devicon-nestjs-plain colored',        3),
('Backend',        'REST APIs',                   80, '🔌', 'ti ti-api',                           4),
('Backend',        'Prepared Statements',         85, '🛡️', 'ti ti-shield-lock',                   5),
('Backend',        'OWASP Top 10 (básico)',       65, '⚠️', 'ti ti-shield-exclamation',            6),
('Backend',        'Seguridad en REST APIs',      70, '🔒', 'ti ti-lock',                          7),
-- Frontend
('Frontend',       'HTML5 Semántico',             90, '🌐', 'devicon-html5-plain colored',         1),
('Frontend',       'CSS3 + Flexbox',              85, '🎨', 'devicon-css3-plain colored',          2),
('Frontend',       'JavaScript ES6',              80, '⚡', 'devicon-javascript-plain colored',    3),
('Frontend',       'Next.js 14',                  65, '⚫', 'devicon-nextjsjs-plain',              4),
('Frontend',       'Tailwind CSS',                75, '💨', 'devicon-tailwindcss-plain colored',   5),
-- Base de Datos
('Base de Datos',  'MySQL / phpMyAdmin',          80, '🗄️', 'devicon-mysql-plain colored',         1),
('Base de Datos',  'PostgreSQL',                  65, '🐘', 'devicon-postgresql-plain colored',    2),
('Base de Datos',  'Prisma ORM',                  60, '🔷', 'ti ti-database',                      3),
('Base de Datos',  'Diseño relacional',           75, '📐', 'ti ti-relation-one-to-many',          4),
-- Herramientas
('Herramientas',   'Git / GitHub',                75, '🔧', 'devicon-git-plain colored',           1),
('Herramientas',   'VS Code',                     85, '💻', 'devicon-vscode-plain colored',        2),
('Herramientas',   'XAMPP / cPanel',              75, '🛠️', 'devicon-apache-plain colored',        3),
('Herramientas',   'Docker',                      55, '🐳', 'devicon-docker-plain colored',        4),
('Herramientas',   'Insomnia',                    70, '🌙', 'ti ti-moon',                          5),
('Herramientas',   'Kali Linux',                  65, '🐧', 'devicon-linux-plain',                 6),
('Herramientas',   'Nmap',                        60, '🔍', 'ti ti-scan',                          7),
('Herramientas',   'Metasploit Framework',        50, '💣', 'ti ti-terminal',                      8),
('Herramientas',   'Wireshark',                   55, '🦈', 'ti ti-wave-square',                   9);

-- ============================================================
-- MIGRACIÓN (solo si ya importaste una versión anterior de bd.sql)
-- ============================================================
-- Si ya tienes datos en `habilidades` y no quieres perderlos, en lugar
-- de re-importar este archivo completo, ejecuta solo este bloque en
-- phpMyAdmin → SQL:
--
-- ALTER TABLE `habilidades`
--   ADD COLUMN `icon_class` VARCHAR(100) DEFAULT '' AFTER `icono`;
--
-- Después, edita cada habilidad desde /admin/skills.php para asignarle
-- una clase devicon o tabler-icons (ej: "devicon-php-plain colored").
