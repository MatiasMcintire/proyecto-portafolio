-- ============================================================
-- PORTAFOLIO PROFESIONAL — Base de Datos
-- Asignatura: Programación de Sitios Dinámicos Web (TECLAB UCT)
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
-- USUARIO ADMIN POR DEFECTO:
--   usuario:  admin
--   password: Admin2024!
--   (cambiar tras el primer login en /admin/change_password.php)
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
  `icono`      VARCHAR(10)      DEFAULT '⚙️',
  `orden`      SMALLINT         DEFAULT 0,
  `visible`    TINYINT(1)       DEFAULT 1,
  `created_at` TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATOS INICIALES (seed)
-- ============================================================

-- Usuario admin (password: Admin2024!)
INSERT INTO `usuarios` (`username`, `password`) VALUES
('admin', '$2y$12$WJ5r4N2nAp.VdFEqTfDzBOBQmpXSHEZiVJUriMn4lFkrLCh2pjF7a');

-- Perfil (fila única con id = 1, se completa desde /admin/profile.php)
INSERT IGNORE INTO `perfil` (`id`, `nombre`, `titulo_profesional`, `bio`) VALUES
(1,
 'Matías McIntire',
 'Estudiante de Tecnicatura Universitaria en Informática',
 'Estudiante de TUI Informática en TECLAB (Universidad Católica de Temuco). Apasionado por el desarrollo web full-stack, con foco en PHP, MySQL y JavaScript. Este portafolio reúne mis proyectos del cursado.');

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
INSERT INTO `habilidades` (`categoria`, `nombre`, `nivel`, `icono`, `orden`) VALUES
('Frontend',     'HTML5',      90, '🌐', 1),
('Frontend',     'CSS3',       85, '🎨', 2),
('Frontend',     'JavaScript', 80, '⚡', 3),
('Backend',      'PHP',        80, '🐘', 1),
('Backend',      'MySQL',      75, '🗄️', 2),
('Herramientas', 'Git',        70, '🔧', 1),
('Herramientas', 'XAMPP',      75, '💻', 2);
