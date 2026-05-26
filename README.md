# Portafolio Web Profesional

Portafolio personal autoadministrable desarrollado como Evaluación 3 de la
asignatura **Diseño y Desarrollo Web + IA** — Técnico
en Informática, TECLAB (Universidad Católica de Temuco).

- **Repositorio:** https://github.com/MatiasMcintire/proyecto-portafolio
- **Producción:** _pendiente de deploy en `teclab.uct.cl/~usuario/`_
- **Documento de uso de IA:** [`prompts/uso-ia.md`](prompts/uso-ia.md)

El sitio público presenta biografía, habilidades, proyectos y un formulario de
contacto. Un panel de administración privado (`/admin/`) permite gestionar todo
el contenido sin tocar código: CRUD de proyectos, edición de perfil, ABM de
habilidades y bandeja de mensajes recibidos.

---

## Stack

- **Frontend:** HTML5 semántico, CSS3 (Flexbox + Grid), JavaScript ES6+
- **Backend:** PHP 8 (sin frameworks)
- **Base de datos:** MySQL 8 / MariaDB 10
- **Bootstrap:** 5.3.3 (integrado vía CDN — navbar collapse, cards, progress, form-control, grid, alerts)
- **Servidor local:** XAMPP / LAMP
- **Producción:** cPanel en `teclab.uct.cl/~usuario/` (ver [`docs/deploy.md`](docs/deploy.md))

## Estructura del proyecto

```
proyecto-portafolio/
├── admin/              Panel privado (login, CRUD, perfil, habilidades)
├── api/                Endpoints PHP (formulario de contacto)
├── assets/
│   ├── css/            Estilos públicos y del panel
│   ├── js/             Validaciones de formularios
│   └── uploads/        Imágenes subidas desde el panel
├── config/
│   └── db.php          Credenciales y conexión MySQL (NO se sube a Git)
├── docs/               Documentación técnica
│   ├── arquitectura.md
│   ├── base-datos.md
│   ├── validaciones-js.md
│   └── deploy.md
├── files/              Wireframes (HTML, PDF, PNG)
├── includes/           Header, footer y middleware de autenticación
├── prompts/            Documentación del uso de IA (Fase 2.2)
├── bd.sql              Esquema completo + datos seed (importar en phpMyAdmin)
├── index.php           Página pública del portafolio
├── robots.txt
└── sitemap.xml
```

## Instalación local (XAMPP)

1. Clonar el repositorio dentro de `htdocs/`:
   ```bash
   cd /opt/lampp/htdocs       # Linux
   git clone https://github.com/MatiasMcintire/proyecto-portafolio.git
   ```
2. Iniciar Apache y MySQL desde el panel de XAMPP.
3. Abrir `http://localhost/phpmyadmin/` e **importar `bd.sql`**.
   El script crea la base `portafolio_db` con sus 5 tablas y datos de ejemplo.
4. Verificar `config/db.php`. En desarrollo local debe quedar:
   ```php
   define('IS_LOCAL', true);   // DB_USER=root, DB_PASS=''
   ```
5. Visitar `http://localhost/proyecto-portafolio/`.

### Acceso al panel de administración

- URL: `http://localhost/proyecto-portafolio/admin/login.php`
- Usuario: `admin`
- Password: `Admin2024!`

> Cambiar la contraseña tras el primer login en
> `/admin/change_password.php`. En producción, **nunca** dejar la contraseña
> por defecto.

## Despliegue en producción

Ver instrucciones detalladas en [`docs/deploy.md`](docs/deploy.md).
Resumen:

1. Cambiar `config/db.php` → `define('IS_LOCAL', false)` y completar
   credenciales del cPanel.
2. Importar `bd.sql` al phpMyAdmin del servidor.
3. Subir archivos por FTP/SFTP al directorio `public_html/`.
4. Regenerar el password admin (no dejar `Admin2024!`).

## Base de datos

5 tablas (ver [`docs/base-datos.md`](docs/base-datos.md) y `bd.sql`):

| Tabla         | Propósito                                                |
| ------------- | -------------------------------------------------------- |
| `proyectos`   | Proyectos del portafolio (CRUD desde `/admin/`)          |
| `usuarios`    | Cuenta de administrador (bcrypt vía `password_hash()`)   |
| `contactos`   | Mensajes del formulario público                          |
| `perfil`      | Datos biográficos del dueño del sitio                    |
| `habilidades` | Habilidades técnicas agrupadas por categoría con nivel % |

## Wireframes

Ubicados en [`files/`](files/):

- `wireframe.pdf` — vista general en PDF
- `wireframe.html` — versión navegable
- `wireframe_public.png` — sitio público
- `wireframe_admin.png` — panel de administración
- `wireframe_login.png` — pantalla de login

## Documentación adicional

- [`prompts/uso-ia.md`](prompts/uso-ia.md) — uso documentado de IA (herramientas, prompts, ajustes, reflexión)
- [`docs/arquitectura.md`](docs/arquitectura.md) — decisiones técnicas
- [`docs/base-datos.md`](docs/base-datos.md) — modelo de datos
- [`docs/validaciones-js.md`](docs/validaciones-js.md) — validaciones de formularios
- [`docs/deploy.md`](docs/deploy.md) — guía de despliegue

## Autor

**Matías McIntire**
Estudiante de Técnico en Informática — TECLAB UCT
Asignatura: Diseño y Desarrollo Web + IA
Docente: Cristian Iglesias Vera
