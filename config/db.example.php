<?php
/**
 * config/db.example.php — Plantilla de configuración de base de datos
 *
 * El archivo real `config/db.php` está en `.gitignore` y NO se versiona
 * para no exponer credenciales en GitHub ni al subir por FTP.
 *
 * USO:
 *   - LOCAL    : copiar este archivo como `config/db.php` y dejar IS_LOCAL = true
 *   - SERVIDOR : crear `public_html/config/db.php` directamente en el servidor
 *     (NO subir el db.php real al repo), pegar este contenido, poner
 *     IS_LOCAL = false y completar con las credenciales reales de la BD.
 *
 * Ver: docs/deploy.md
 */

// --- Zona horaria (Chile continental) ---
date_default_timezone_set('America/Santiago');

// --- Entorno ---
// Local:      true   (XAMPP, HTTP, usuario root sin pass)
// Producción: false  (servidor del curso, HTTPS, credenciales reales)
define('IS_LOCAL', true);

if (IS_LOCAL) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'portafolio_db');
} else {
    // Credenciales del servidor del curso — REEMPLAZAR
    define('DB_HOST', 'localhost');
    define('DB_USER', 'USUARIO_AQUI');     // en TECLAB: el mismo user de SFTP/phpMyAdmin (ej: mmcintire2025)
    define('DB_PASS', 'PASSWORD_AQUI');    // en TECLAB: la misma pass de SFTP/phpMyAdmin
    define('DB_NAME', 'NOMBRE_BD_AQUI');   // en TECLAB: una BD prefabricada (ej: mmcintire2025_db1)
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
