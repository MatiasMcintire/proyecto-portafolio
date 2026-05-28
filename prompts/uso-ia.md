# Uso de Inteligencia Artificial en el desarrollo del portafolio

**Autor:** Matías McIntire
**Asignatura:** Diseño y Desarrollo Web + IA — Técnico Universitario en Informática (UCT)
**Evaluación:** N° 3 — Portafolio profesional

Este documento describe cómo utilicé herramientas de IA generativa durante el
desarrollo del portafolio, qué prompts funcionaron, qué tuve que corregir y qué
aprendí del proceso. Lo escribí después de terminar el código, repasando mi
historial de conversaciones y los commits, para que refleje el flujo real y no
una versión idealizada.

---

## a. Herramientas de IA utilizadas

| Herramienta            | Para qué la usé                                                   | Frecuencia |
| ---------------------- | ----------------------------------------------------------------- | ---------- |
| **Claude (Anthropic)** | Diseño de esquema SQL, validaciones server-side, refactors PHP    | Diario     |
| **ChatGPT (GPT-4)**    | Generación de boilerplate HTML/CSS, dudas puntuales de sintaxis   | 2-3 veces por semana |
| **GitHub Copilot**     | Autocompletado dentro de VS Code (sobre todo en JS y comentarios) | Continuo, mientras escribía |

No usé un solo modelo para todo. Aprendí rápido que **cada herramienta tiene su
fuerte**: Claude maneja mejor el contexto largo cuando le pego varios archivos
juntos, ChatGPT es más rápido para preguntas sueltas y Copilot acelera el tipeo
pero no entiende la arquitectura global del proyecto.

Para la documentación interna también probé generar imágenes/diagramas con
Whimsical AI, pero no terminé incluyéndolas en la entrega: los diagramas
generados eran demasiado genéricos.

---

## b. Prompts utilizados

A continuación 12 de los prompts que más impacto tuvieron en el código final.
Los reproduzco tal como los escribí (con typos y todo), porque me parece más
honesto que limpiarlos a posteriori.

### Prompt 1 — Esquema inicial de la base de datos

> "Necesito el SQL para un portafolio web simple en MySQL. Tabla de proyectos
> (titulo, descripcion, tecnologias separadas por comas, link a github, imagen,
> destacado si/no), tabla de usuarios solo para el admin con username y
> password, y tabla de contactos para guardar mensajes del formulario. Dame un
> archivo .sql que cree la base de datos y las tablas con charset utf8mb4."
>
> — Claude, 2026-05-04

### Prompt 2 — Login con prepared statements

> "Tengo esta tabla `usuarios` con username y password. Quiero un login.php
> seguro: prepared statement para el SELECT, password_verify (NO md5 por favor),
> y que regenere el session id. Si falla, mensaje genérico que no diga si el
> usuario existe."
>
> — Claude, 2026-05-06

### Prompt 3 — Validación JS del formulario de contacto

> "JS vanilla (sin jQuery) que valide el formulario de contacto en tiempo real:
> nombre solo letras y espacios, email con regex razonable, asunto entre 5 y
> 150 chars, mensaje entre 20 y 1000. Que muestre los errores debajo de cada
> input y deshabilite el submit hasta que todo esté ok. Quiero que use
> addEventListener('input') para validar mientras tipeo."
>
> — ChatGPT, 2026-05-07

### Prompt 4 — Endpoint PHP del formulario que devuelva JSON

> "El JS del prompt anterior envía con fetch a /api/contact.php. Escribime ese
> endpoint: que valide DE NUEVO en el servidor (no confiar en el cliente),
> guarde en la tabla contactos con prepared statement, y responda JSON con
> success true/false + message. Usar filter_var para sanitizar."
>
> — Claude, 2026-05-07

### Prompt 5 — Layout responsive con CSS Grid

> "CSS para una grilla de tarjetas de proyectos. 1 columna en mobile, 2 en
> tablet (>=600px), 3 en desktop (>=900px). Cada tarjeta con imagen arriba,
> título, descripción y chips de tecnologías. Que el hover levante la tarjeta
> con sombra. Sin Bootstrap todavía, solo CSS puro."
>
> — ChatGPT, 2026-05-08

### Prompt 6 — CRUD admin: listado con búsqueda

> "Página admin/index.php que liste todos los proyectos en una tabla. Que tenga
> un input de búsqueda que filtre por título o tecnologías (búsqueda en cliente,
> sin recargar). Botones editar/eliminar por fila. El de eliminar tiene que
> pedir confirmación con un confirm() y mandar a delete.php?id=X."
>
> — Claude, 2026-05-10

### Prompt 7 — Subida de imagen con validación de tipo

> "En admin/profile.php quiero permitir subir una foto de perfil. Tiene que
> validar que sea jpg/png/gif/webp, máximo 2MB, y guardarla en assets/uploads/
> con un nombre tipo perfil_<timestamp>.<ext>. Importante: validar el tipo MIME
> real, NO solo la extensión (alguien podría subir un .php renombrado a .jpg).
> Si había una foto anterior, borrarla."
>
> — Claude, 2026-05-13

### Prompt 8 — Tabla de habilidades con barras de progreso

> "Quiero agregar una tabla habilidades (categoria, nombre, nivel de 0 a 100,
> icono emoji, orden, visible). Mostrarla en el portafolio agrupada por
> categoría, con una barra de progreso CSS por habilidad. En el admin, un CRUD
> normal pero el listado tiene que ordenarse por categoría y después por orden."
>
> — Claude, 2026-05-14

### Prompt 9 — Tabla perfil de una sola fila

> "Idea: en lugar de hardcodear mi bio en el index.php, quiero una tabla perfil
> con nombre, título, bio, email, teléfono, ubicación, github, linkedin, foto.
> Pero solo va a haber UNA fila (id=1). ¿Cómo hago para que el INSERT inicial
> se haga una sola vez y después el formulario admin solo haga UPDATE?"
>
> — ChatGPT, 2026-05-14

### Prompt 10 — Header y footer reutilizables

> "Estoy repitiendo el `<head>` y la navbar en index.php, en login.php y en las
> páginas del admin. Refactorizá para que use includes/header.php y
> includes/footer.php, recibiendo variables como $pageTitle y $pageDesc. Que el
> admin pueda tener su propio header distinto."
>
> — Claude, 2026-05-15

### Prompt 11 — robots.txt y sitemap.xml

> "Generame robots.txt y sitemap.xml para el portafolio. En robots.txt bloquear
> /admin/ y /api/. En sitemap.xml incluir la home con prioridad 1.0 y las
> secciones (#inicio, #habilidades, #proyectos, #contacto) con prioridad 0.8.
> Lang es-CL."
>
> — ChatGPT, 2026-05-17

### Prompt 12 — Documentación técnica para entregar

> "Tengo 4 archivos .md que quiero crear en /docs: arquitectura, base-datos,
> validaciones-js y deploy. Para cada uno, dame un esqueleto con secciones y la
> info que tendría que rellenar. Tono didáctico, como apuntes universitarios."
>
> — Claude, 2026-05-17

---

## c. Resultados generados

Los prompts anteriores produjeron código que terminó (con modificaciones) en el
proyecto. Algunos resultados literales:

### De Prompt 2 — login.php (núcleo de autenticación)

La IA me dio esta porción tal cual, y la conservé porque está bien:

```php
// admin/login.php
$stmt = $conn->prepare("SELECT id, username, password FROM usuarios WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['admin_user'] = $user['username'];
    $_SESSION['admin_id']   = $user['id'];
    header('Location: index.php');
    exit;
}
```

### De Prompt 4 — api/contact.php (endpoint JSON)

```php
$stmt = $conn->prepare(
    "INSERT INTO contactos (nombre, email, asunto, mensaje, ip)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param('sssss', $nombre, $email, $asunto, $mensaje, $ip);
```

### De Prompt 7 — admin/profile.php (subida con MIME real)

```php
$allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$fi      = finfo_open(FILEINFO_MIME_TYPE);
$mime    = finfo_file($fi, $_FILES['foto']['tmp_name']);
finfo_close($fi);

if (!in_array($mime, $allowed)) {
    $error = 'Solo se permiten imágenes JPG, PNG, GIF o WEBP.';
}
```

### De Prompt 8 — admin/skills.php (consulta ordenada)

```php
$habilidades = $conn->query(
    "SELECT * FROM habilidades ORDER BY categoria ASC, orden ASC, nombre ASC"
);
```

### De Prompt 9 — perfil de una sola fila

ChatGPT me sugirió el truco de `INSERT IGNORE` con id explícito, que terminé
usando en `bd.sql`:

```sql
INSERT IGNORE INTO `perfil` (`id`, `nombre`, ...) VALUES (1, 'Matías McIntire', ...);
```

Esto crea la fila la primera vez y nunca duplica, así el formulario admin
siempre hace UPDATE WHERE id = 1.

---

## d. Ajustes realizados (pifias de la IA y mis correcciones)

Esta es la sección más importante para mí: **lo que la IA me devolvió que NO
funcionaba o era directamente peligroso**, y cómo lo arreglé. Si fuera 100%
copiar y pegar, el código tendría agujeros graves.

### Pifia 1 — Claude propuso MD5 en una iteración inicial

En una versión temprana del prompt 2 (yo había pedido "algo simple para
empezar"), me devolvió:

```php
// MAL — versión inicial generada
if ($user['password'] === md5($password)) { ... }
```

MD5 es **trivialmente reversible con rainbow tables** y no tiene salt. En la
asignatura ya habíamos visto que se debe usar `password_hash()` (bcrypt).
Reescribí el prompt agregando explícitamente "NO md5 por favor" y el siguiente
intento usó `password_verify()` correctamente. Aprendizaje: **hay que pedirle
seguridad explícita, no asumir que la elige sola**.

### Pifia 2 — ChatGPT armó un SELECT con concatenación

Primera versión del listado admin (prompt 6) tenía:

```php
// MAL — vulnerable a SQL Injection
$q   = $_GET['q'] ?? '';
$sql = "SELECT * FROM proyectos WHERE titulo LIKE '%$q%'";
$res = $conn->query($sql);
```

Esto es **SQL Injection de manual**. Lo corregí pasando a prepared statement
con placeholder:

```php
// OK — Corregido
$stmt = $conn->prepare("SELECT * FROM proyectos WHERE titulo LIKE ?");
$like = '%' . $q . '%';
$stmt->bind_param('s', $like);
```

Al final terminé moviendo la búsqueda al cliente (filtrado JS sobre la tabla
ya cargada), pero la lección quedó: **revisar cada `$conn->query()` con
variables y cambiarlo por prepare/bind_param**.

### Pifia 3 — XSS en el listado de proyectos

La primera versión del HTML del listado imprimía directamente:

```php
// MAL — XSS si alguien guarda <script> en la descripción
echo "<td>" . $row['descripcion'] . "</td>";
```

Aunque el único que puede meter datos es el admin (yo), si me hackean el login
podrían inyectar JS que afecte a futuras visitas. Lo envolví todo en
`htmlspecialchars()`:

```php
// OK — Corregido
echo "<td>" . htmlspecialchars($row['descripcion'], ENT_QUOTES, 'UTF-8') . "</td>";
```

### Pifia 4 — Validación de imagen solo por extensión

Primera versión de la subida (prompt 7, antes de aclarar) hacía:

```php
// MAL — fácil de saltar renombrando archivo.php a archivo.jpg
$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
if (!in_array($ext, ['jpg','png','gif','webp'])) { ... }
```

El problema es que `pathinfo` solo mira el nombre del archivo, no el contenido
real. Alguien podía subir un `shell.php` renombrado a `shell.jpg` y luego
acceder a `assets/uploads/shell.jpg.php` o similar. Forcé en el siguiente
prompt usar `finfo_file()` con el MIME real del contenido, que es lo que
quedó en `admin/profile.php`.

### Pifia 5 — Emojis rotos en la BD

Después de importar `bd.sql` por primera vez en local, los emojis de la
columna `icono` de habilidades se guardaban como `?`. Claude no había puesto
`$conn->set_charset('utf8mb4')` en `config/db.php` y la conexión usaba
`latin1` por defecto. Lo agregué a mano:

```php
// OK — Agregado a config/db.php
$conn->set_charset('utf8mb4');
```

Detalle pequeño pero que rompía toda la sección habilidades visualmente.

### Pifia 6 — Sin manejo de errores en la conexión

La primera versión de `config/db.php` que me dio Claude moría con
`die($conn->connect_error)`, lo cual **muestra credenciales y rutas al usuario
final** en producción si la BD se cae. Lo separé en dos ramas según el flag
`IS_LOCAL`:

```php
// OK — En producción: log al servidor, mensaje genérico al usuario
if (IS_LOCAL) {
    die('Error de conexión: ' . $conn->connect_error);
} else {
    error_log('DB Error: ' . $conn->connect_error);
    die('Error interno del servidor. Intenta más tarde.');
}
```

### Pifia 7 — Copilot autocompletó `eval()`

Esta me sorprendió: estaba escribiendo una función que ordenaba habilidades por
una columna dinámica recibida por GET, y Copilot me sugirió:

```js
// MAL — Copilot autocompletó esto
const sortKey = new URLSearchParams(location.search).get('sort');
data.sort((a, b) => eval(`a.${sortKey} > b.${sortKey} ? 1 : -1`));
```

`eval` con input del usuario es directamente XSS/RCE. Lo cambié a un acceso por
índice con whitelist:

```js
// OK — Corregido
const ALLOWED_KEYS = ['nombre', 'categoria', 'nivel', 'orden'];
const sortKey = ALLOWED_KEYS.includes(rawKey) ? rawKey : 'nombre';
data.sort((a, b) => a[sortKey] > b[sortKey] ? 1 : -1);
```

### Pifia 8 — Validación solo en cliente

Para el formulario de contacto (prompt 3), la primera entrega de ChatGPT solo
validaba en JS. Yo asumí que con eso bastaba para "no recibir basura", pero el
docente nos había repetido que **server-side siempre, sin excepciones**. Tuve
que repetir el prompt explícitamente pidiendo la validación duplicada en PHP
(prompt 4). La validación JS quedó como mejora de UX, no como barrera de
seguridad. Esto está documentado en
[`docs/validaciones-js.md`](../docs/validaciones-js.md).

### Pifia 9 — Mezcla de mysqli y PDO

En distintas conversaciones, Claude a veces me daba snippets con mysqli y otras
con PDO. El proyecto entero usa mysqli, así que tuve que reescribir un par de
funciones que llegaron en PDO. Lección: cuando arranco una sesión nueva con la
IA, **conviene decirle desde el primer mensaje "el proyecto usa mysqli
procedural, no PDO"**, así no mezcla.

### Pifia 10 — Sugirió guardar la sesión sin `httponly`

En el prompt 2, la cookie de sesión salía con configuración por defecto. Después
de leer un poco más sobre cookies seguras, terminé centralizando el
`session_start()` en `includes/csrf.php` para que el hardening aplique sí o sí
antes de cualquier acceso a `$_SESSION`:

```php
// OK — En includes/csrf.php, antes del primer session_start()
ini_set('session.use_strict_mode', '1');
$secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $secure,    // true en producción HTTPS, false en XAMPP local
    'httponly' => true,       // JS no puede leer la cookie
    'samesite' => 'Strict',   // cookie no se envía cross-site
]);
session_start();
```

`includes/auth.php` y `admin/login.php` ahora hacen `require_once 'csrf.php'`
antes de tocar `$_SESSION`, así no hay forma de abrir sesión saltándose el
hardening. Detalle clave: si `csrf.php` no es el primer archivo que llama a
`session_start()`, los parámetros se ignoran silenciosamente.

---

## e. Reflexión crítica

### Utilidad real

La IA me **aceleró muchísimo el trabajo rutinario**: el boilerplate de
formularios, el SQL inicial, los headers reutilizables, los esqueletos de los
.md de documentación. Cosas que hubieran tardado 2-3 horas las hice en 20-30
minutos. Para un proyecto evaluativo con plazos ajustados, esa diferencia es
decisiva.

También me ayudó como **tutor a demanda**: cuando no entendía por qué un
`bind_param` con `'sssi'` me daba error, le pegaba el snippet y la explicación
era inmediata. Eso reemplazó muchas búsquedas en Stack Overflow.

### Ventajas concretas

1. **Velocidad** para tareas repetitivas (CRUD, validaciones, configuraciones).
2. **Cobertura amplia**: me sugirió usar `session_regenerate_id()`, `finfo_file`
   o `password_hash` cuando yo no los conocía aún.
3. **Refactor sin miedo**: cuando refactoricé el header reutilizable, le pegué
   los 3 archivos viejos y me devolvió la versión deduplicada sin perder nada.
4. **Documentación**: los esqueletos de los archivos en `/docs` son obra suya.

### Limitaciones que sentí

1. **No conoce mi proyecto entero**. Si no le pego el archivo, inventa rutas,
   nombres de tablas o variables que no existen. Varias veces me dio código
   que llamaba a `$db` cuando mi variable es `$conn`.
2. **No detecta sus propios huecos de seguridad** salvo que se lo pidas
   explícitamente. Las pifias 1, 2, 4 y 7 las habría dejado pasar si no
   estuviera leyendo el código antes de pegarlo.
3. **Inconsistencia entre sesiones**. Lo que decidimos en una conversación se
   olvida en la siguiente. Tuve que mantener un archivo de "convenciones"
   mental para ir reanclando cada vez (mysqli procedural, bcrypt, no PDO,
   utf8mb4, etc.).
4. **Copilot es peligroso si no leés lo que sugiere**. La pifia 7 (`eval`)
   habría entrado al repo si yo aceptaba el Tab a ciegas. Después de eso me
   acostumbré a leer cada sugerencia antes de aceptarla.
5. **Diseño visual genérico**. Cuando le pedí "una paleta de colores moderna
   para portafolio", me dio los mismos tonos azules/violetas que veo en todos
   los demos. Para los colores finales preferí elegir yo.

### Lo más importante que aprendí

La IA es **un asistente potente pero no autónomo**. El estudiante (yo) sigue
siendo responsable de:

- Entender el código que entrega antes de pegarlo.
- Revisar la seguridad (SQL injection, XSS, validación server-side, manejo de
  archivos).
- Mantener la coherencia arquitectónica entre archivos.
- Probar todo en navegador real, no asumir que funciona.

Cuando lo trato como un **par junior al que reviso el código**, funciona muy
bien. Cuando lo trato como un **oráculo del que copio y pego sin leer**, se
cuelan bugs y agujeros de seguridad como los nueve que documenté arriba.

Para mi siguiente proyecto pienso seguir usándola, pero con dos cambios:

1. **Empezar cada conversación nueva con un mensaje de contexto**: stack,
   convenciones, archivos clave. Eso reduce las inconsistencias.
2. **Pedirle explícitamente revisión de seguridad** antes de dar por cerrado
   cualquier endpoint, formulario o subida de archivos.

---

**Total de prompts documentados:** 12
**Pifias detectadas y corregidas:** 10
**Aprobado por:** Matías McIntire — 2026-05-21
