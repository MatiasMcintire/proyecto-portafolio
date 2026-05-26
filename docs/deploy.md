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

### PASO 2: Actualizar config/db.php

Cambiar las credenciales en `config/db.php`:

```php
// Cambiar IS_LOCAL a false
define('IS_LOCAL', false);

// Completar con los datos de cPanel:
define('DB_HOST', 'localhost');
define('DB_USER', 'usuario_portafolio_usr');  // El usuario que creaste
define('DB_PASS', 'tu_password_seguro');       // La contraseña que pusiste
define('DB_NAME', 'usuario_portafolio');       // La BD que creaste
```

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

**Qué subir:**
```
- index.php
- config/db.php  (con las credenciales actualizadas)
- includes/
- admin/
- api/
- assets/css/
- assets/js/
- assets/uploads/  (la carpeta vacía)
- robots.txt
- sitemap.xml
```

**Qué NO subir:**
```
- database/portafolio.sql  (ya lo importaste en phpMyAdmin)
- docs/                    (documentación interna, no va al servidor)
- .git/                    (carpeta de git)
```

### PASO 5: Permisos de carpeta uploads

La carpeta `assets/uploads/` necesita permisos de escritura para que PHP pueda subir imágenes.

En FileZilla:
1. Clic derecho en `assets/uploads/`
2. "Permisos de archivo..."
3. Establecer permisos: **755** (o marcar todos los permisos de lectura + escritura para propietario)

### PASO 6: Generar contraseña del admin

En el servidor, ejecutar una vez este script (luego borrarlo):

```php
<?php
// setup_admin.php — ELIMINAR DESPUÉS DE USAR
$hash = password_hash('TuContraseñaSegura2024!', PASSWORD_DEFAULT);
echo $hash;
?>
```

Copiar el hash generado y actualizar en phpMyAdmin:
```sql
UPDATE usuarios SET password = 'HASH_GENERADO' WHERE username = 'admin';
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
