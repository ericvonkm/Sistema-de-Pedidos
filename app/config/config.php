<?php
// Configuración global del sistema
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_pedidos_saas');
define('DB_USER', 'root');           // Cambiar según tu configuración
define('DB_PASS', '');               // Cambiar según tu configuración
define('DB_CHARSET', 'utf8mb4');

// Rutas del sistema
define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');
define('APP_PATH', ROOT . 'app/');
define('PUBLIC_PATH', ROOT . 'public/');
define('STORAGE_PATH', ROOT . 'storage/');
define('UPLOADS_PATH', PUBLIC_PATH . 'uploads/');

// URLs base
define('BASE_URL', 'http://localhost/app/public/');
define('ASSETS_URL', BASE_URL . 'public/assets/');
define('UPLOADS_URL', BASE_URL . 'public/uploads/');

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Pedidos SaaS');
define('APP_VERSION', '1.0.0');
define('DEFAULT_CONTROLLER', 'Home');
define('DEFAULT_METHOD', 'index');

// Seguridad
define('CSRF_TOKEN_NAME', '_token');
define('SESSION_NAME', 'saas_pedidos_session');
define('HASH_ALGO', PASSWORD_DEFAULT);

// Configuración de archivos
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('IMAGE_QUALITY', 85);
define('IMAGE_MAX_WIDTH', 2000);
define('IMAGE_MAX_HEIGHT', 2000);

// Timezone
date_default_timezone_set('America/Santiago');

// Iniciar sesión
session_name(SESSION_NAME);
session_start();

// Configuración de errores (desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);