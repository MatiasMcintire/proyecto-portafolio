/**
 * main.js — Portafolio Profesional
 *
 * Módulos incluidos:
 *   1. Navegación (menú hamburger + active links)
 *   2. Funciones de validación reutilizables
 *   3. Manejo de errores en formulario (sin alert)
 *   4. Feedback dinámico: toast, success, reset
 *   5. Contador de caracteres en tiempo real
 *   6. Inicialización del formulario de contacto
 *
 * @author Tu Nombre
 * @version 1.0.0
 * @course Diseño y Desarrollo Web + IA
 */

'use strict'; // Activa modo estricto: mejor detección de errores


/* ═══════════════════════════════════════════════════════════
   MÓDULO 1: NAVEGACIÓN
   ═══════════════════════════════════════════════════════════ */

/**
 * Cierra el menú colapsado de Bootstrap al hacer clic en un enlace.
 * Bootstrap maneja el toggle/show/hide del navbar-collapse vía data-bs-toggle;
 * solo agregamos la UX de auto-cierre en mobile cuando el usuario navega.
 */
function initNavCollapse() {
  const collapseEl = document.getElementById('navMenu');
  if (!collapseEl || typeof bootstrap === 'undefined') return;

  collapseEl.querySelectorAll('a.nav-link').forEach(function (link) {
    link.addEventListener('click', function () {
      // Solo cerrar si está expandido (mobile)
      if (collapseEl.classList.contains('show')) {
        const instance = bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
        instance.hide();
      }
    });
  });
}

/**
 * Marca el enlace de navegación activo según la sección visible.
 * Usa IntersectionObserver (API moderna y eficiente).
 * No requiere calcular posición de scroll manualmente.
 */
function initActiveNav() {
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.navbar-nav a.nav-link[href*="#"]');

  if (!sections.length || !navLinks.length) return;

  const observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          // Quitar 'active' de todos los enlaces
          navLinks.forEach(function (link) {
            link.classList.remove('active');
          });

          // Agregar 'active' al enlace correspondiente a la sección
          navLinks.forEach(function (link) {
            const href = link.getAttribute('href');
            if (href && href.includes('#' + entry.target.id)) {
              link.classList.add('active');
            }
          });
        }
      });
    },
    { rootMargin: '-40% 0px -55% 0px' }
  );

  sections.forEach(function (section) {
    observer.observe(section);
  });
}


/* ═══════════════════════════════════════════════════════════
   MÓDULO 2: FUNCIONES DE VALIDACIÓN
   Cada función tiene una única responsabilidad (clean code).
   ═══════════════════════════════════════════════════════════ */

/**
 * Verifica que un campo no esté vacío.
 * Elimina espacios al inicio y al final antes de verificar.
 *
 * @param {string} value - Valor del campo a verificar.
 * @returns {boolean} true si el campo contiene texto.
 */
function validateRequired(value) {
  return value.trim().length > 0;
}

/**
 * Valida el formato de un correo electrónico.
 *
 * La expresión regular verifica:
 *   - Al menos un carácter antes del @
 *   - Exactamente un @
 *   - Al menos un carácter entre @ y el punto
 *   - Un punto seguido de al menos un carácter (dominio)
 *
 * @param {string} email - Email a validar.
 * @returns {boolean} true si el formato es correcto.
 *
 * @example
 *   validateEmail('nombre@dominio.com')  // true
 *   validateEmail('nombre@dominio')      // false (falta extensión)
 *   validateEmail('nombre dominio.com')  // false (tiene espacio)
 */
function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email.trim());
}

/**
 * Verifica que la cantidad de caracteres esté dentro del rango permitido.
 *
 * @param {string} value - Texto a verificar.
 * @param {number} min   - Cantidad mínima de caracteres requerida.
 * @param {number} max   - Cantidad máxima de caracteres permitida.
 * @returns {boolean} true si la longitud está dentro del rango.
 *
 * @example
 *   validateLength('Hola', 3, 100)  // true (4 caracteres, entre 3 y 100)
 *   validateLength('Hi', 5, 100)    // false (2 caracteres, menos del mínimo)
 */
function validateLength(value, min, max) {
  var length = value.trim().length;
  return length >= min && length <= max;
}

/**
 * Verifica que un texto contenga solo letras (incluyendo acentos y ñ) y espacios.
 * Rechaza números, símbolos y caracteres especiales.
 * Útil para campos de nombre propio.
 *
 * @param {string} value - Texto a verificar.
 * @returns {boolean} true si contiene solo letras y espacios.
 */
function validateOnlyLetters(value) {
  var lettersRegex = /^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/;
  return lettersRegex.test(value.trim());
}


/* ═══════════════════════════════════════════════════════════
   MÓDULO 3: MANEJO DE ERRORES EN CAMPOS
   Muestra/oculta mensajes de error sin usar alert().
   ═══════════════════════════════════════════════════════════ */

/**
 * Muestra un mensaje de error en el campo especificado.
 * Agrega la clase CSS 'is-invalid' y hace visible el .error-msg.
 *
 * Por qué sin alert(): alert() bloquea el hilo de JS, interrumpe
 * al usuario y no permite estilos personalizados. Los mensajes
 * en línea son más usables y profesionales.
 *
 * @param {HTMLElement} input   - El campo de formulario con error.
 * @param {string}      message - Texto del mensaje de error.
 */
function showFieldError(input, message) {
  // Buscar el grupo del campo (padre .form-group)
  var formGroup = input.closest('.form-group');
  var errorEl   = formGroup ? formGroup.querySelector('.error-msg') : null;

  // Marcar el campo como inválido (cambia el borde a rojo vía CSS)
  input.classList.add('is-invalid');
  input.classList.remove('is-valid');

  // Mostrar el mensaje de error
  if (errorEl) {
    errorEl.textContent = message;
    errorEl.style.display = 'block';
  }
}

/**
 * Limpia el estado de error de un campo, marcándolo como válido.
 *
 * @param {HTMLElement} input - El campo de formulario a limpiar.
 */
function clearFieldError(input) {
  var formGroup = input.closest('.form-group');
  var errorEl   = formGroup ? formGroup.querySelector('.error-msg') : null;

  // Marcar como válido (borde verde vía CSS)
  input.classList.remove('is-invalid');
  input.classList.add('is-valid');

  // Ocultar el mensaje de error
  if (errorEl) {
    errorEl.style.display = 'none';
    errorEl.textContent   = '';
  }
}

/**
 * Valida un campo individual aplicando todas las reglas necesarias.
 * Las reglas se determinan por los atributos del campo:
 *   - required → campo obligatorio
 *   - type="email" → validación de formato email
 *   - name="nombre" → solo letras
 *   - data-minlength → mínimo de caracteres
 *   - data-maxlength → máximo de caracteres
 *
 * @param {HTMLElement} input - El campo a validar.
 * @returns {boolean} true si el campo pasa todas las validaciones.
 */
function validateField(input) {
  var value    = input.value;
  var type     = input.type;
  var name     = input.name;
  var minLen   = parseInt(input.dataset.minlength) || 0;
  var maxLen   = parseInt(input.dataset.maxlength) || Infinity;
  var required = input.hasAttribute('required');

  // Regla 1: Campo obligatorio
  if (required && !validateRequired(value)) {
    showFieldError(input, 'Este campo es obligatorio.');
    return false;
  }

  // Si el campo está vacío y no es obligatorio, no hay más que validar
  if (!required && value.trim() === '') {
    clearFieldError(input);
    return true;
  }

  // Regla 2: Formato de email
  if (type === 'email' && !validateEmail(value)) {
    showFieldError(input, 'Ingresa un correo válido. Ejemplo: nombre@dominio.com');
    return false;
  }

  // Regla 3: Solo letras (para campo nombre)
  if (name === 'nombre' && !validateOnlyLetters(value)) {
    showFieldError(input, 'El nombre solo puede contener letras y espacios.');
    return false;
  }

  // Regla 4: Longitud mínima
  if (minLen > 0 && value.trim().length < minLen) {
    showFieldError(input, 'Mínimo ' + minLen + ' caracteres. Llevas ' + value.trim().length + '.');
    return false;
  }

  // Regla 5: Longitud máxima
  if (maxLen < Infinity && value.trim().length > maxLen) {
    showFieldError(input, 'Máximo ' + maxLen + ' caracteres. Llevas ' + value.trim().length + '.');
    return false;
  }

  // Todas las reglas pasaron
  clearFieldError(input);
  return true;
}

/**
 * Valida todos los campos requeridos de un formulario.
 * Retorna false si alguno falla, y enfoca el primer campo inválido.
 *
 * @param {HTMLFormElement} form - Formulario a validar.
 * @returns {boolean} true si todos los campos son válidos.
 */
function validateForm(form) {
  var fields = form.querySelectorAll('input[required], textarea[required]');
  var isValid = true;

  fields.forEach(function (field) {
    if (!validateField(field)) {
      isValid = false;
    }
  });

  // Enfocar el primer campo con error (mejora UX)
  if (!isValid) {
    var firstInvalid = form.querySelector('.is-invalid');
    if (firstInvalid) {
      firstInvalid.focus();
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  return isValid;
}


/* ═══════════════════════════════════════════════════════════
   MÓDULO 4: FEEDBACK DINÁMICO
   Reemplaza alert() con mensajes visuales estilizados.
   ═══════════════════════════════════════════════════════════ */

/**
 * Muestra una notificación toast temporal en la esquina de la pantalla.
 *
 * Por qué toast en lugar de alert():
 *   - No bloquea la página ni el hilo de JavaScript
 *   - Se puede estilizar con CSS
 *   - Desaparece automáticamente
 *   - Mejor experiencia de usuario (UX)
 *
 * @param {string}  message             - Texto a mostrar.
 * @param {'success'|'error'} [type]    - Tipo de toast (color).
 * @param {number}  [duration=4000]     - Tiempo visible en milisegundos.
 */
function showToast(message, type, duration) {
  type     = type     || 'success';
  duration = duration || 4000;

  var toast = document.getElementById('toast');
  if (!toast) return;

  var icons = { success: '✓', error: '✕' };

  // Limpiar contenido anterior
  toast.innerHTML = '';
  toast.className = 'toast toast--' + type;

  // Crear ícono
  var iconEl = document.createElement('span');
  iconEl.textContent  = icons[type] || '•';
  iconEl.style.fontWeight = '700';
  iconEl.style.fontSize   = '1.1em';

  // Crear texto
  var textEl = document.createElement('span');
  textEl.textContent = message;

  toast.appendChild(iconEl);
  toast.appendChild(textEl);

  // Mostrar con animación (requestAnimationFrame evita que se "saltee" la transición)
  requestAnimationFrame(function () {
    toast.classList.add('is-visible');
  });

  // Ocultar automáticamente después de 'duration' ms
  setTimeout(function () {
    toast.classList.remove('is-visible');
  }, duration);
}

/**
 * Oculta el formulario con fade-out y muestra el mensaje de éxito.
 * Implementa el comportamiento de "ocultar formulario tras envío"
 * requerido por la rúbrica.
 *
 * @param {HTMLFormElement} form      - El formulario a ocultar.
 * @param {HTMLElement}     successEl - El elemento de éxito a mostrar.
 */
function showFormSuccess(form, successEl) {
  // Transición de opacidad (fade-out)
  form.style.opacity    = '0';
  form.style.transition = 'opacity 0.3s ease';

  setTimeout(function () {
    form.style.display = 'none';

    // Mostrar el mensaje de éxito
    if (successEl) {
      successEl.classList.add('is-visible');
      successEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }, 300);
}

/**
 * Resetea el formulario: limpia valores, errores y clases de validación.
 * Útil si el usuario quiere enviar otro mensaje.
 *
 * @param {HTMLFormElement} form - El formulario a resetear.
 */
function resetForm(form) {
  // Limpiar valores de todos los campos
  form.reset();

  // Quitar clases de validación visual
  var validatedFields = form.querySelectorAll('.is-invalid, .is-valid');
  validatedFields.forEach(function (field) {
    field.classList.remove('is-invalid', 'is-valid');
  });

  // Ocultar mensajes de error
  var errorMessages = form.querySelectorAll('.error-msg');
  errorMessages.forEach(function (msg) {
    msg.style.display = 'none';
    msg.textContent   = '';
  });

  // Resetear contadores de caracteres
  var counters = form.querySelectorAll('.char-counter');
  counters.forEach(function (counter) {
    var group = counter.closest('.form-group');
    if (!group) return;
    var input = group.querySelector('[data-maxlength]');
    if (input) {
      counter.textContent = '0 / ' + input.dataset.maxlength;
      counter.classList.remove('is-limit');
    }
  });
}


/* ═══════════════════════════════════════════════════════════
   MÓDULO 5: CONTADOR DE CARACTERES
   ═══════════════════════════════════════════════════════════ */

/**
 * Inicializa contadores de caracteres para campos con data-maxlength.
 * Se actualiza en tiempo real mientras el usuario escribe (evento input).
 * Cambia de color cuando se acerca al límite (90% del máximo).
 */
function initCharCounters() {
  var inputs = document.querySelectorAll('[data-maxlength]');

  inputs.forEach(function (input) {
    var maxLen  = parseInt(input.dataset.maxlength);
    var group   = input.closest('.form-group');
    var counter = group ? group.querySelector('.char-counter') : null;

    if (!counter) return;

    // Estado inicial
    counter.textContent = '0 / ' + maxLen;

    // Actualizar en cada tecla presionada
    input.addEventListener('input', function () {
      var current  = input.value.length;
      var nearLimit = current >= Math.floor(maxLen * 0.9);

      counter.textContent = current + ' / ' + maxLen;
      counter.classList.toggle('is-limit', nearLimit);
    });
  });
}


/* ═══════════════════════════════════════════════════════════
   MÓDULO 6: FORMULARIO DE CONTACTO
   ═══════════════════════════════════════════════════════════ */

/**
 * Inicializa el formulario de contacto con:
 *   - Validación en tiempo real (blur + input)
 *   - Envío asíncrono (fetch API)
 *   - Feedback dinámico (toast + mensaje de éxito)
 *   - Sin recarga de página (preventDefault)
 */
function initContactForm() {
  var form      = document.getElementById('contactForm');
  var successEl = document.getElementById('formSuccess');

  if (!form) return;

  // Validación en tiempo real al salir de cada campo (evento blur)
  var fields = form.querySelectorAll('input, textarea');
  fields.forEach(function (field) {

    // Validar cuando el usuario sale del campo
    field.addEventListener('blur', function () {
      validateField(field);
    });

    // Mientras escribe: limpiar error si el campo ya era inválido
    // (mejor UX: el error desaparece en cuanto el usuario corrige)
    field.addEventListener('input', function () {
      if (field.classList.contains('is-invalid')) {
        validateField(field);
      }
    });
  });

  // Manejo del envío del formulario
  form.addEventListener('submit', function (event) {
    // Prevenir el envío tradicional (que recargaría la página)
    event.preventDefault();

    // Validar TODOS los campos antes de enviar
    if (!validateForm(form)) {
      showToast('Corrige los errores marcados en rojo antes de continuar.', 'error');
      return;
    }

    // Cambiar botón a estado de carga
    var submitBtn    = form.querySelector('[type="submit"]');
    var originalText = submitBtn.textContent;
    submitBtn.disabled     = true;
    submitBtn.textContent  = 'Enviando...';

    // Recopilar datos del formulario
    var formData = new FormData(form);

    // Enviar datos al backend PHP con fetch (sin recargar la página)
    fetch(form.getAttribute('action'), {
      method: 'POST',
      body: formData
    })
    .then(function (response) {
      if (!response.ok) {
        throw new Error('Error HTTP: ' + response.status);
      }
      return response.json();
    })
    .then(function (result) {
      if (result.success) {
        // Mostrar mensaje de éxito y ocultar formulario
        showFormSuccess(form, successEl);
        showToast('¡Mensaje enviado! Me pondré en contacto pronto.', 'success', 5000);
      } else {
        // Mostrar error del servidor
        showToast(result.message || 'No se pudo enviar. Intenta más tarde.', 'error');
        submitBtn.disabled    = false;
        submitBtn.textContent = originalText;
      }
    })
    .catch(function (error) {
      console.error('Error al enviar formulario:', error);
      showToast('Error de conexión. Verifica tu internet e intenta nuevamente.', 'error');
      submitBtn.disabled    = false;
      submitBtn.textContent = originalText;
    });
  });
}


/* ═══════════════════════════════════════════════════════════
   PUNTO DE ENTRADA PRINCIPAL
   Se ejecuta cuando el DOM está completamente construido.
   ═══════════════════════════════════════════════════════════ */

/**
 * DOMContentLoaded: se dispara cuando el HTML está parseado,
 * antes de que las imágenes y CSS estén completamente cargados.
 * Es más rápido que window.onload.
 */
document.addEventListener('DOMContentLoaded', function () {
  initNavCollapse();
  initActiveNav();
  initCharCounters();
  initContactForm();
});
