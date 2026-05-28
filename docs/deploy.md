# Guía de Deploy — Servidor TECLAB (UCT)

## ¿Qué se hizo?

Documentación paso a paso para desplegar el portafolio en el hosting del curso
en `teclab.uct.cl`, usando un cliente SFTP (FileZilla, WinSCP o `sftp` por
consola) y phpMyAdmin para la base de datos.

## Entorno del servidor

| Pieza | Detalle |
|-------|---------|
| Sistema | Rocky Linux |
| Servidor web | Apache 2.4.62 (con `mod_userdir`) |
| PHP | 8.4.21 |
| BD | MariaDB (compatible MySQL) |
| Panel | phpMyAdmin web (no hay cPanel, no hay File Manager) |
| Acceso a archivos | **SFTP** en `teclab.uct.cl:55522` (no FTP plano) |
| URL pública | `https://teclab.uct.cl/~USUARIO/` (en este proyecto: `~mmcintire2025/`) |
| Directorio público | `/USUARIO/public_html/` |
| Bases de datos | **Pre-creadas** por TECLAB: `USUARIO_db1` y `USUARIO_db2`. NO se pueden crear nuevas BDs ni usuarios MySQL. El user MySQL es el mismo que el de SFTP/phpMyAdmin. |

> En esta guía `USUARIO` representa tu usuario TECLAB (ej. `mmcintire2025`).
> Reemplazalo en los comandos y URLs.

## Pasos completos

### PASO 1 — Conectarse por SFTP

1. Abrir FileZilla (o tu cliente SFTP).
2. Ir a **Archivo → Gestor de sitios** (Ctrl+S).
3. Crear sitio nuevo con:
   - **Protocolo**: **SFTP - SSH File Transfer Protocol** (no FTP).
   - **Servidor**: `teclab.uct.cl`
   - **Puerto**: `55522` (es obligatorio especificarlo; el SSH estándar 22 no está expuesto).
   - **Modo de acceso**: Normal.
   - **Usuario**: el usuario TECLAB que te asignó el curso.
   - **Contraseña**: la entregada con tu usuario.
4. Conectar. Al entrar verás el home del usuario (ej. `/mmcintire2025/`).

### PASO 2 — Subir los archivos al `public_html`

En FileZilla, panel izquierdo (local) abrir el repo `proyecto-portafolio/`, en
el panel derecho (servidor) entrar a `public_html/`.

**Qué subir** (es lo que el sitio necesita para funcionar):

```
index.php
robots.txt
sitemap.xml
includes/
admin/
api/
assets/css/
assets/js/
assets/uploads/        ← carpeta vacía con su .gitkeep (los permisos los seteás en el PASO 5)
config/db.example.php  ← opcional, plantilla para el PASO 3
```

**Qué NO subir** (es material académico o de versión, no aporta nada en
producción y en algunos casos no debería ser público):

```
config/db.php          ← NO existe en local con credenciales reales — se CREA en el servidor en el PASO 3
bd.sql                 ← se importa por phpMyAdmin en el PASO 4
bd_servidor.sql        ← variante usada solo para el import, no hace falta en producción
docs/                  ← documentación del repo (entrega académica)
prompts/               ← documento de uso de IA (entrega académica)
files/                 ← wireframes (PNG/PDF) (entrega académica)
README.md              ← información del repo
SESION_CONTINUACION.md ← notas internas (gitignored)
.git/                  ← carpeta de Git
.gitignore             ← archivo de Git
Rubrica_y_Escala_Ev_3.pdf
```

### PASO 3 — Crear `config/db.php` en el servidor

`config/db.php` está en `.gitignore` y **nunca** se sube por SFTP ni se commitea
(contiene la contraseña de la BD). Hay que crearlo directamente en el servidor.

Como TECLAB no tiene File Manager, se hace por SFTP:

1. En el panel derecho de FileZilla, entrar a `public_html/config/` (subiste el
   directorio `config/` solo con `db.example.php` en el PASO 2).
2. **Opción A — editar in-place desde FileZilla:**
   - Clic derecho en `db.example.php` → **Renombrar** → `db.php`.
   - Clic derecho en `db.php` → **Ver/Editar** (FileZilla abre el editor que
     tengas configurado).
3. **Opción B — preparar en local:**
   - Copiar `config/db.example.php` a un `config/db.php` local **temporal**.
   - Editarlo con las credenciales reales.
   - Subirlo por SFTP.
   - **Borrar el `db.php` local** antes de seguir trabajando (para no
     commitearlo por error — ya está en `.gitignore`, pero mejor no tenerlo
     físicamente en el repo local).
4. Contenido a poner en `config/db.php`:

```php
define('IS_LOCAL', false);                     // IMPORTANTE: false en producción
define('DB_HOST', 'localhost');
define('DB_USER', 'USUARIO');                  // mismo user que SFTP/phpMyAdmin
define('DB_PASS', 'tu_password_teclab');       // misma pass que SFTP/phpMyAdmin
define('DB_NAME', 'USUARIO_db1');              // una de las 2 BDs pre-creadas
```

5. Verificar permisos: **644** (Apache corre como otro usuario y necesita poder
   leer el archivo; 600 lo bloquearía). En FileZilla: clic derecho → "Permisos
   de archivo..." → `644`.

### PASO 4 — Importar la BD en phpMyAdmin

phpMyAdmin se accede vía web (no por cPanel). La URL te la da TECLAB; entrás
con el mismo usuario y password del SFTP.

1. Abrir phpMyAdmin web.
2. En la lista de la izquierda seleccionar `USUARIO_db1` (o `_db2`, lo que
   hayas puesto en `config/db.php`).
3. Pestaña **Importar** → **Choose File**.
4. **Problema conocido**: `bd.sql` del repo arranca con
   `CREATE DATABASE IF NOT EXISTS portafolio_db` + `USE portafolio_db`. En
   TECLAB el usuario MySQL no tiene permiso para crear/usar bases distintas a
   las pre-creadas → el import falla con **error 1044 (Access denied)**.

   **Solución:** generar una copia de `bd.sql` con esas dos líneas
   comentadas. Desde la terminal del repo local:

   ```bash
   sed 's|^CREATE DATABASE|-- CREATE DATABASE|; s|^USE |-- USE |' bd.sql > bd_servidor.sql
   ```

   Este `bd_servidor.sql` ya está en `.gitignore` para que no se commitee.
5. Subir `bd_servidor.sql` desde phpMyAdmin → Continuar.
6. Verificar que las 5 tablas aparezcan: `proyectos`, `usuarios`, `contactos`,
   `perfil`, `habilidades`.

### PASO 5 — Permisos de `assets/uploads/`

PHP necesita escribir en `assets/uploads/` para guardar las fotos de perfil y
las imágenes de los proyectos.

1. En FileZilla, clic derecho en `assets/uploads/` → "Permisos de archivo...".
2. Marcar **755** (rwxr-xr-x).
3. **No** subir foto desde el admin todavía: hacelo después del PASO 6 cuando
   la pass del admin sea segura.

### PASO 6 — Cambiar la contraseña del admin (OBLIGATORIO)

`bd.sql` trae el admin con la pass por defecto `Admin2024!`, que es **pública**
en el repo. Hay que cambiarla inmediatamente apenas el sitio esté arriba —
desde la propia UI del panel, sin scripts temporales.

1. Abrir `https://teclab.uct.cl/~USUARIO/admin/login.php`.
2. Iniciar sesión con:
   - Usuario: `admin`
   - Contraseña: `Admin2024!`
3. En el sidebar ir a **Configuración → Contraseña** (o entrar directo a
   `https://teclab.uct.cl/~USUARIO/admin/change_password.php`).
4. Poner una nueva contraseña fuerte (≥ 12 caracteres, mezcla de tipos).
5. Cerrar sesión y volver a entrar con la pass nueva para verificar.
6. (Opcional) Cambiar también el `username` del admin desde phpMyAdmin si
   querés un perfil más discreto:
   ```sql
   UPDATE usuarios SET username = 'nuevo_user' WHERE username = 'admin';
   ```

### PASO 7 — Verificar el deploy

1. Abrir `https://teclab.uct.cl/~USUARIO/` en el navegador.
2. Verificar que carga la home, las habilidades aparecen con sus íconos y los
   proyectos destacados se muestran.
3. Probar el formulario de contacto: enviar un mensaje real y entrar al admin
   para confirmar que llegó.
4. Probar el CRUD: agregar un proyecto y verificar que aparece en la home.
5. Probar la subida de foto en `admin/profile.php` (después del cambio de pass
   del PASO 6).

## Errores frecuentes y soluciones

| Error | Causa | Solución |
|-------|-------|----------|
| Página en blanco / HTTP 500 | Error PHP sin mostrar | TECLAB no expone los logs de Apache por SFTP. Subir un `_debug.php` temporal con `ini_set('display_errors', '1'); error_reporting(E_ALL);` y `include 'index.php';` para ver el error, **y borrarlo después**. |
| Error 1044 al importar `bd.sql` | El user MySQL no puede crear/usar otras BDs | Usar `bd_servidor.sql` con `CREATE DATABASE` y `USE` comentados (ver PASO 4). |
| Error 1045 al cargar la home | Credenciales DB incorrectas en `config/db.php` | Verificar `DB_USER`/`DB_PASS`/`DB_NAME` (los 3 son el mismo user que SFTP/phpMyAdmin, no inventar). |
| Imágenes no se suben desde el admin | Permisos de `assets/uploads/` | `chmod 755` desde FileZilla (PASO 5). |
| Sesión no se mantiene tras login | El navegador rechaza la cookie | Verificar que estés entrando por `https://` (no `http://`) — la cookie tiene flag `secure`. |
| Acentos rotos en la BD | Conexión no es utf8mb4 | `config/db.php` debe terminar con `$conn->set_charset('utf8mb4');` (ya viene así por defecto). |

## Estructura final en el servidor

```
/USUARIO/public_html/
├── index.php
├── robots.txt
├── sitemap.xml
├── config/
│   └── db.php             ← credenciales reales (644)
├── includes/
├── admin/
├── api/
└── assets/
    ├── css/
    ├── js/
    └── uploads/           ← 755 (escribible por PHP)
```

## Actualizar el sitio (workflow recurrente)

Cuando hagas cambios:

1. Editar los archivos localmente (en VS Code).
2. Probar en XAMPP (`http://localhost/proyecto-portafolio/`).
3. Hacer commit + push a GitHub.
4. Subir **solo los archivos modificados** por SFTP (no resubas la carpeta
   entera — es lento y arriesga sobrescribir `config/db.php`).
5. Si cambió el esquema de la BD (alguna `ALTER TABLE` o `CREATE TABLE`), no
   reimportar `bd.sql` (perderías los datos): generar un dump del local con
   `mysqldump portafolio_db > dump.sql`, adaptar el `CREATE DATABASE` /`USE`
   como en el PASO 4, y aplicar solo los cambios incrementales en phpMyAdmin.
6. Verificar el cambio en `https://teclab.uct.cl/~USUARIO/`.

## Seguridad adicional en producción

`config/db.php` queda dentro de `public_html/`, así que si Apache deja de
interpretar PHP por alguna razón, el archivo se serviría como texto. Apache de
TECLAB sí interpreta PHP, así que el riesgo es bajo, pero como cinturón y
tiradores podés agregar un `.htaccess` en `public_html/`:

```apache
# .htaccess — Reglas de seguridad básicas
Options -Indexes

<Files "db.php">
    Order Allow,Deny
    Deny from all
</Files>

<FilesMatch "\.(sql|md|example\.php)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

Esto evita listados de directorios y bloquea acceso directo a `db.php`,
archivos `.sql`, `.md` y `db.example.php` aunque alguien adivine la URL.
