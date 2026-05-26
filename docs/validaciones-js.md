# Validaciones JavaScript — Documentación Técnica

## ¿Qué se hizo?

Se implementaron validaciones completas del formulario de contacto usando JavaScript puro (sin librerías externas), cubriendo todos los criterios de la rúbrica:

- Campos obligatorios
- Validación de email con expresión regular
- Restricción de caracteres (mínimo y máximo)
- Mensajes de error sin `alert()`
- Feedback dinámico (mensaje de éxito + reseteo de formulario)
- Contador de caracteres en tiempo real

## ¿Por qué sin `alert()`?

La rúbrica pide **feedback dinámico** explícitamente. `alert()` tiene varios problemas:

1. **Bloquea el hilo de JavaScript** — nada más puede ejecutarse mientras está abierto.
2. **No se puede personalizar** — siempre tiene el mismo estilo de cada navegador.
3. **Mala experiencia de usuario (UX)** — interrumpe el flujo de trabajo.
4. **No es profesional** — en aplicaciones reales nunca se usa para errores de formulario.

**Alternativa implementada:**
- Mensajes en línea bajo cada campo (`.error-msg`)
- Toast de notificación que aparece/desaparece automáticamente
- Mensaje de éxito que reemplaza el formulario visualmente

## Funciones principales

### `validateRequired(value)`
```javascript
// Verifica que el campo no esté vacío
function validateRequired(value) {
    return value.trim().length > 0;
}
```
- `.trim()` elimina espacios al inicio y final
- Un campo con solo espacios en blanco se considera vacío

### `validateEmail(email)`
```javascript
// Expresión regular para validar email
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
```

**Desglose de la regex:**
- `^` → inicio del string
- `[^\s@]+` → uno o más caracteres que NO sean espacio ni @
- `@` → el símbolo arroba
- `[^\s@]+` → uno o más caracteres que NO sean espacio ni @
- `\.` → un punto literal (el `\` escapa el punto especial de regex)
- `[^\s@]+` → la extensión del dominio (.com, .cl, etc.)
- `$` → fin del string

### `validateLength(value, min, max)`
```javascript
function validateLength(value, min, max) {
    var length = value.trim().length;
    return length >= min && length <= max;
}
```
- Combina validación de mínimo Y máximo en una sola función
- Usa `&&` (AND): ambas condiciones deben cumplirse

### `validateOnlyLetters(value)`
```javascript
const lettersRegex = /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/;
```
- Acepta letras mayúsculas y minúsculas, acentos, ñ y espacios
- Rechaza números y símbolos en campos de nombre

## Validación en tiempo real vs al enviar

| Momento | Evento | Propósito |
|---------|--------|-----------|
| Al salir del campo | `blur` | Validar cuando el usuario termina de escribir |
| Mientras escribe | `input` | Limpiar el error si ya fue corregido |
| Al enviar | `submit` | Validación final completa antes de enviar |

## Flujo del formulario

```
Usuario llena el formulario
        ↓
Hace clic en "Enviar"
        ↓
validateForm() valida todos los campos
        ↓
¿Hay errores?
    Sí → showFieldError() + showToast() de error → el usuario corrige
    No → fetch() envía datos a api/contact.php
              ↓
         PHP valida y guarda en MySQL
              ↓
         Devuelve JSON { success: true }
              ↓
         showFormSuccess() oculta form + muestra mensaje éxito
         showToast() de éxito
```

## Buenas prácticas aplicadas

1. **Función única por validación** — cada función hace exactamente una cosa (Single Responsibility Principle)
2. **JSDoc en cada función** — documentación en el código que explica el propósito y parámetros
3. **`'use strict'`** — activa el modo estricto de JavaScript, que detecta errores comunes
4. **`DOMContentLoaded`** — espera a que el HTML esté listo antes de ejecutar JS (evita errores de "elemento no encontrado")
5. **fetch() en lugar de XMLHttpRequest** — API moderna, más legible con Promesas

## Errores comunes

- Usar `document.getElementById('campo').value == ""` — no elimina espacios en blanco
- Confiar solo en la validación del cliente — siempre validar también en PHP
- Usar `alert()` para mostrar errores de formulario
- Validar solo al enviar, no en tiempo real (mala UX)
