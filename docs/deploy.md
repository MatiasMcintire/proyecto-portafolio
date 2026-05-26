# Guía de Deploy — Servidor cPanel con FileZilla

## ¿Qué se hizo?

Documentación paso a paso para subir el portafolio al servidor del curso usando FileZilla (FTP) y configurar la base de datos en phpMyAdmin de cPanel.

## Pasos completos

### PASO 1: Preparar la base de datos en cPanel

1. Abrir el panel cPanel de tu servidor
2. Buscar **MySQL Databases** (o Bases de Datos MySQL)
3. Crear base de datos:
   - Nombre: `portafolio` (quedará como `usuario_portafolio`)
4. Crear usuario MySQL:
   - Usuario: `portafolio_usr`
   - Contraseña: una contraseña segura (mínimo 12 caracteres)
5. Asignar usuario a la base de datos:
   - Seleccionar usuario y base de datos
   - Marcar **TODOS LOS PRIVILEGIOS**
6. Ir a **phpMyAdmin** desde cPanel
7. Seleccionar la base de datos recién creada
8. Ir a la pestaña **Importar**
9. Seleccionar el archivo `bd.sql` (está en la raíz del proyecto)
10. Hacer clic en **Continuar**

### PASO 2: Crear config/db.php en el servidor

`config/db.php` está en `.gitignore` y **no se sube por FTP ni GitHub** para no
exponer credenciales. Hay que crearlo directamente en el servidor:

1. En cPanel abrir **File Manager** y navegar a `public_html/config/`
2. Crear archivo nuevo: `db.php`
3. Copiar el contenido íntegro de `config/db.example.php` (lo tenés en tu copia
   local del repo, ya versionado como plantilla)
4. Pegarlo en el `db.php` recién creado y completar con las credenciales reales:

```php
define('IS_LOCAL', false);  // IMPORTANTE: false en producción
define('DB_HOST', 'localhost');
define('DB_USER', 'usuario_portafolio_usr');   // el user MySQL del paso 1
define('DB_PASS', 'tu_password_seguro');       // la pass del paso 1
define('DB_NAME', 'usuario_portafolio');       // la BD del paso 1
```

5. Guardar y cerrar
6. Verificar permisos: 644 (lectura para el servidor, no ejecución)

### PASO 3: Conectar FileZilla al servidor

1. Abrir FileZilla
2. Ir a **Archivo → Gestor de sitios** (o Ctrl+S)
3. Hacer clic en "Nuevo sitio"
4. Completar:
   - **Protocolo**: FTP - Protocolo de Transferencia de Archivos
   - **Servidor**: el host FTP del curso (ej: `ftp.teclab.uct.cl`)
   - **Cifrado**: Requerir FTP explícito sobre TLS (si está disponible)
   - **Modo de acceso**: Normal
   - **Usuario**: tu usuario FTP del curso
   - **Contraseña**: tu contraseña FTP
5. Clic en **Conectar**

### PASO 4: Subir los archivos

En FileZilla verás dos paneles:
- **Izquierda**: tus archivos locales (busca `proyecto-portafolio/`)
- **Derecha**: el servidor (navega a `public_html/` o `www/`)

**Qué subir** (lo que el sitio necesita para funcionar en producción):
```
- index.php
- includes/
- admin/
- api/
- assets/css/
- assets/js/
- assets/uploads/      (la carpeta vacía con su .gitkeep — permisos 755 después)
- robots.txt
- sitemap.xml
```

**Qué NO subir** (académico, de versión, o que se crea en el servidor):
```
- bd.sql                  (ya lo importaste en phpMyAdmin en el PASO 1)
- config/db.php           (NO existe en local con credenciales reales — se CREA en el servidor, ver PASO 2)
- config/db.example.php   (es plantilla del repo, no la necesita el servidor)
- docs/                   (documentación interna del repo / entrega)
- prompts/                (documento de uso de IA, para la entrega académica)
- files/                  (wireframes en PNG/PDF, no van al servidor)
- README.md               (información del repo, no la necesita el servidor)
- .git/                   (carpeta de Git)
- .gitignore              (archivo de Git, no relevante en producción)
```

**Qué SÍ crear directamente en el servidor** (vía cPanel File Manager):
- `config/db.php` con las credenciales reales (copiar el contenido de `config/db.example.php` y completar, ver PASO 2)

### PASO 5: Permisos de carpeta uploads

La carpeta `assets/uploads/` necesita permisos de escritura para que PHP pueda subir imágenes.

En FileZilla:
1. Clic derecho en `assets/uploads/`
2. "Permisos de archivo..."
3. Establecer permisos: **755** (o marcar todos los permisos de lectura + escritura para propietario)

### PASO 6: Cambiar la contraseña del admin (OBLIGATORIO)

`bd.sql` deja el admin con la contraseña por defecto `Admin2024!`. Hay que
cambiarla apenas el sitio esté arriba — el panel ya tiene una pantalla segura
para hacerlo, no hace falta ningún script temporal.

1. Abrir `https://teclab.uct.cl/~tu-usuario/admin/login.php`
2. Iniciar sesión con:
   - Usuario: `admin`
   - Contraseña: `Admin2024!`
3. En el sidebar ir a **Configuración → Contraseña**, o entrar directo a:
   `https://teclab.uct.cl/~tu-usuario/admin/change_password.php`
4. Ingresar la nueva contraseña (mínimo 12 caracteres, mezcla de tipos)
5. Cerrar sesión y volver a entrar con la pass nueva para verificar
6. (Opcional) Cambiar también el `username` del admin si querés un perfil más
   discreto, vía phpMyAdmin:
   ```sql
   UPDATE usuarios SET username = 'nuevo_user' WHERE username = 'admin';
   ```

### PASO 7: Verificar el deploy

1. Abrir `https://teclab.uct.cl/~usuario/` en el navegador
2. Verificar que carga la página y los proyectos aparecen
3. Probar el formulario de contacto
4. Ir a `https://teclab.uct.cl/~usuario/admin/login.php`
5. Iniciar sesión con las credenciales
6. Verificar CRUD de proyectos

## Errores frecuentes y soluciones

| Error | Causa | Solución |
|-------|-------|----------|
| Página en blanco | Error PHP sin mostrar | Revisar logs en cPanel → Errors |
| "Error de conexión DB" | Credenciales incorrectas | Verificar config/db.php |
| Imágenes no se suben | Permisos de carpeta | Chmod 755 en uploads/ |
| Admin redirige al login | Sesiones no funcionan | Verificar session_start() en auth.php |
| Acentos se ven mal | Charset incorrecto | Verificar utf8mb4 en DB y PHP |

## Estructura en el servidor

```
public_html/
├── index.php
├── config/
│   └── db.php        ← Con credenciales reales
├── includes/
├── admin/
├── api/
├── assets/
│   └── uploads/      ← Permisos 755
├── robots.txt
└── sitemap.xml
```

## Actualizar el sitio (workflow)

Cuando hagas cambios:
1. Editar los archivos localmente (en VS Code)
2. Probar en XAMPP (localhost)
3. Subir SOLO los archivos modificados con FileZilla
4. Verificar en el navegador en el servidor

## Seguridad adicional en producción

Agregar un archivo `.htaccess` en la raíz para proteger carpetas sensibles:

```apache
# .htaccess — Reglas de seguridad Apache
Options -Indexes

# Proteger archivos sensibles
<Files "db.php">
    Order Allow,Deny
    Deny from all
</Files>
```
