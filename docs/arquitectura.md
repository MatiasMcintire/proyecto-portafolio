# Arquitectura del Portafolio Profesional

## ¿Qué se hizo?

Se diseñó una arquitectura modular para un portafolio web con PHP y MySQL. La estructura separa responsabilidades en carpetas con propósitos claros.

## Estructura de carpetas

```
proyecto-portafolio/
├── index.php              ← Página principal (público)
├── config/
│   └── db.php             ← Conexión a base de datos (PRIVADO)
├── includes/
│   ├── header.php         ← Cabecera reutilizable (DRY)
│   ├── footer.php         ← Pie de página reutilizable (DRY)
│   └── auth.php           ← Verificación de sesión admin
├── admin/
│   ├── login.php          ← Login del administrador
│   ├── logout.php         ← Cerrar sesión
│   ├── index.php          ← Dashboard CRUD
│   ├── add.php            ← Agregar proyecto
│   ├── edit.php           ← Editar proyecto
│   └── delete.php         ← Eliminar proyecto
├── api/
│   └── contact.php        ← Endpoint JSON para el formulario
├── assets/
│   ├── css/style.css      ← Estilos CSS propios (Flexbox + Grid)
│   ├── js/main.js         ← Validaciones JS + feedback dinámico
│   └── uploads/           ← Imágenes subidas por el admin
├── database/
│   └── portafolio.sql     ← Script de creación de BD
├── docs/                  ← Documentación técnica
├── robots.txt             ← SEO: instrucciones para bots
└── sitemap.xml            ← SEO: mapa del sitio
```

## ¿Por qué esta arquitectura?

### Separación de responsabilidades (Separation of Concerns)
Cada carpeta tiene una función específica:
- `config/` → solo configuración, nunca lógica
- `includes/` → componentes reutilizables (principio DRY: Don't Repeat Yourself)
- `admin/` → todo lo privado, protegido por autenticación
- `api/` → endpoints que devuelven JSON, no HTML

### Principio DRY
El `header.php` y `footer.php` se escriben una sola vez y se reutilizan en todas las páginas. Si necesitas cambiar la navegación, lo haces en un único archivo.

## Alternativas posibles

| Opción | Pros | Contras |
|--------|------|---------|
| Todo en un archivo | Simple para proyectos pequeños | Imposible de mantener |
| MVC completo | Máxima escalabilidad | Overkill para portafolio |
| **Esta arquitectura** | Balance entre simplicidad y organización | Nuestra elección |
| Framework (Laravel) | Muchas herramientas incluidas | Requiere aprender más |

## Relación con arquitectura profesional

Esta estructura es similar a lo que se usa en proyectos reales pequeños y medianos. Empresas como agencias web usan exactamente este patrón con PHP sin framework. A mayor escala, se migraría a MVC (Laravel/Symfony).

## Errores comunes a evitar

- ❌ Poner credenciales de DB directamente en index.php
- ❌ No separar el HTML del PHP (archivos mezclados sin estructura)
- ❌ No proteger las rutas admin con verificación de sesión
- ❌ Subir archivos sin validar tipo MIME y tamaño
