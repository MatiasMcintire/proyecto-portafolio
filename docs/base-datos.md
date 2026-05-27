# Base de Datos — Diseño y Documentación

## ¿Qué se hizo?

Se diseñó un esquema relacional con 5 tablas para el portafolio:

1. `proyectos` — los trabajos del portafolio
2. `usuarios` — el administrador del sistema
3. `contactos` — mensajes enviados por el formulario
4. `perfil` — datos biográficos del dueño del portafolio (fila única, id = 1)
5. `habilidades` — habilidades técnicas por categoría con nivel %

## Diagrama de tablas

```
┌─────────────────────────────────────┐
│           proyectos                 │
├──────────────┬──────────────────────┤
│ id           │ INT AUTO_INCREMENT PK │
│ titulo       │ VARCHAR(255) NOT NULL │
│ descripcion  │ TEXT NOT NULL         │
│ tecnologias  │ VARCHAR(500)          │
│ url_github   │ VARCHAR(500)          │
│ url_produccion│ VARCHAR(500)         │
│ imagen       │ VARCHAR(255)          │
│ destacado    │ TINYINT(1) DEFAULT 0  │
│ orden        │ INT DEFAULT 0         │
│ created_at   │ TIMESTAMP             │
│ updated_at   │ TIMESTAMP             │
└──────────────┴──────────────────────┘

┌─────────────────────────────────────┐
│           usuarios                  │
├──────────────┬──────────────────────┤
│ id           │ INT AUTO_INCREMENT PK │
│ username     │ VARCHAR(50) UNIQUE    │
│ password     │ VARCHAR(255) NOT NULL │
│ created_at   │ TIMESTAMP             │
└──────────────┴──────────────────────┘

┌─────────────────────────────────────┐
│           contactos                 │
├──────────────┬──────────────────────┤
│ id           │ INT AUTO_INCREMENT PK │
│ nombre       │ VARCHAR(100) NOT NULL │
│ email        │ VARCHAR(150) NOT NULL │
│ asunto       │ VARCHAR(200) NOT NULL │
│ mensaje      │ TEXT NOT NULL         │
│ leido        │ TINYINT(1) DEFAULT 0  │
│ ip           │ VARCHAR(45)           │
│ created_at   │ TIMESTAMP             │
└──────────────┴──────────────────────┘

┌──────────────────────────────────────────┐
│       perfil   (fila única, id = 1)      │
├────────────────────┬─────────────────────┤
│ id                 │ INT AUTO_INCREMENT PK│
│ nombre             │ VARCHAR(100)         │
│ titulo_profesional │ VARCHAR(150)         │
│ bio                │ TEXT                 │
│ email_contacto     │ VARCHAR(120)         │
│ telefono           │ VARCHAR(30)          │
│ ubicacion          │ VARCHAR(100)         │
│ github             │ VARCHAR(200)         │
│ linkedin           │ VARCHAR(200)         │
│ foto               │ VARCHAR(255)         │
│ updated_at         │ TIMESTAMP            │
└────────────────────┴─────────────────────┘

┌─────────────────────────────────────┐
│           habilidades               │
├──────────────┬──────────────────────┤
│ id           │ INT AUTO_INCREMENT PK │
│ categoria    │ VARCHAR(80) NOT NULL  │
│ nombre       │ VARCHAR(80) NOT NULL  │
│ nivel        │ TINYINT 0-100         │
│ icon_class   │ VARCHAR(100) devicon  │
│ orden        │ SMALLINT DEFAULT 0    │
│ visible      │ TINYINT(1) DEFAULT 1  │
│ created_at   │ TIMESTAMP             │
└──────────────┴──────────────────────┘
```

## Decisiones de diseño

### ¿Por qué `TINYINT(1)` para booleanos?
MySQL no tiene un tipo `BOOLEAN` nativo. `TINYINT(1)` es el estándar: `0 = false`, `1 = true`.

### ¿Por qué `utf8mb4` y no `utf8`?
`utf8` en MySQL es incompleto (solo soporta hasta 3 bytes por carácter). `utf8mb4` soporta el estándar completo incluyendo emojis. Siempre usar `utf8mb4`.

### ¿Por qué `VARCHAR(255)` para URLs y no `TEXT`?
Las URLs tienen un límite práctico de ~2000 caracteres. `VARCHAR(500)` es suficiente y permite indexar el campo. `TEXT` no puede ser indexado directamente.

### ¿Por qué `VARCHAR(255)` para imagen y no guardar el binario?
**Nunca guardar imágenes en la base de datos**. Se guarda solo el nombre del archivo. Las imágenes van en el sistema de archivos (`assets/uploads/`). Razones:
- Las imágenes en BD hacen las copias de seguridad enormes
- El servidor de archivos sirve imágenes más eficientemente
- Permite usar CDN en el futuro

### ¿Por qué `updated_at TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`?
Permite saber cuándo se modificó un registro sin necesidad de código adicional. MySQL lo actualiza automáticamente.

## Prepared Statements — ¿Qué son y por qué usarlos?

**Sin prepared statement (PELIGROSO):**
```php
// VULNERABLE a SQL Injection
$sql = "SELECT * FROM users WHERE username='$username'";
```

Si el usuario ingresa: `admin' OR '1'='1`, la consulta se convierte en:
```sql
SELECT * FROM users WHERE username='admin' OR '1'='1'
-- Retorna todos los usuarios
```

**Con prepared statement (SEGURO):**
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
```
El `?` es un placeholder. PHP envía la consulta y los datos por separado, MySQL nunca los mezcla, por lo que la inyección es imposible.

## Instalar en phpMyAdmin (pasos)

1. Abrir phpMyAdmin (local: `http://localhost/phpmyadmin`)
2. Clic en "Nueva base de datos"
3. Nombre: `portafolio_db`, Cotejamiento: `utf8mb4_unicode_ci`
4. Clic en "Crear"
5. Seleccionar la base de datos recién creada
6. Ir a la pestaña "Importar"
7. Seleccionar `bd.sql` (en la raíz del proyecto)
8. Clic en "Continuar"

## En el servidor (cPanel)

1. Ir a cPanel → MySQL Databases
2. Crear base de datos (ej: `usuario_portafolio`)
3. Crear usuario MySQL con contraseña segura
4. Asignar el usuario a la base de datos (todos los privilegios)
5. Ir a phpMyAdmin desde cPanel
6. Seleccionar la BD e importar `bd.sql` (en la raíz del proyecto)
7. Actualizar `config/db.php` con las credenciales

## Errores comunes

- Guardar contraseñas en texto plano — usar `password_hash()`
- No tener `utf8mb4` — los acentos y emojis se corrompen
- Usar `SELECT *` sin `LIMIT` en tablas grandes
- Concatenar variables directamente en SQL (SQL Injection)
