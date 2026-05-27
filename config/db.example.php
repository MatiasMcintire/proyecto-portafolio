<?php
/**
 * config/db.example.php — Plantilla de configuración de base de datos
 *
 * El archivo real `config/db.php` está en `.gitignore` y NO se versiona
 * para no exponer credenciales en GitHub ni al subir por FTP.
 *
 * USO:
 *   - LOCAL  : copiar este archivo como `config/db.php` y dejar IS_LOCAL = true
 *   - SERVIDOR (cPanel): crear `public_html/config/db.php` directamente vía
 *     File Manager (NO subir por FTP), pegar este contenido, poner
 *     IS_LOCAL = false y completar con las credenciales reales del cPanel.
 *
 * Ver: docs/deploy.md
 */

// --- Zona horaria (Chile continental) ---
date_default_timezone_set('America/Santiago');

// --- Entorno ---
// Local:      true   (XAMPP, HTTP, usuario root sin pass)
// Producción: false  (cPanel, HTTPS, credenciales reales)
define('IS_LOCAL', true);

if (IS_LOCAL) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'portafolio_db');
} else {
    // Credenciales del servidor cPanel — REEMPLAZAR
    define('DB_HOST', 'localhost');
    define('DB_USER', 'USUARIO_CPANEL_AQUI');      // ej: matiasmcintire_usr
    define('DB_PASS', 'PASSWORD_CPANEL_AQUI');     // la que pusiste al crear el user MySQL
    define('DB_NAME', 'NOMBRE_BD_CPANEL_AQUI');    // ej: matiasmcintire_portafolio
}

// --- Conexión con manejo de errores diferenciado por entorno ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    if (IS_LOCAL) {
        die('Error de conexión: ' . $conn->connect_error);
    } else {
        // No filtrar detalles técnicos al usuario en producción
        error_log('DB Error: ' . $conn->connect_error);
        die('Error interno del servidor. Intenta más tarde.');
    }
}

// UTF-8 completo para acentos y caracteres especiales
$conn->set_charset('utf8mb4');
