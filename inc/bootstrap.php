<?php
// Aproavena · arranque común. Todo archivo público empieza con:
//   require __DIR__ . '/inc/bootstrap.php';

declare(strict_types=1);

// --- Configuración -----------------------------------------------------------
$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Falta inc/config.php. Copia inc/config.example.php y complétalo.');
}
$config = require $configFile;

if (!empty($config['debug'])) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);   // se registran en el log, no se muestran
}

date_default_timezone_set('America/Santiago');
mb_internal_encoding('UTF-8');

// --- Ruta base ---------------------------------------------------------------
// Con base_path a null se deduce sola comparando la carpeta del proyecto con el
// DocumentRoot. Así el mismo código funciona en http://localhost/proyectos/
// aproavena/ y en la raíz del dominio del VPS, sin tocar la configuración.
// Un valor explícito en config.php (aunque sea '') manda siempre.
if (!isset($config['base_path']) || $config['base_path'] === null) {
    $docRoot  = str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $proyecto = str_replace('\\', '/', (string)realpath(__DIR__ . '/..'));

    $config['base_path'] = ($docRoot !== '' && $proyecto !== '' && str_starts_with($proyecto, $docRoot))
        ? rtrim(substr($proyecto, strlen($docRoot)), '/')
        : '';
}

require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/auth.php';   // solo define funciones; no fuerza login

// --- Sesión ------------------------------------------------------------------
// Se inicia siempre: el token CSRF del formulario de contacto también la usa.
if (session_status() === PHP_SESSION_NONE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => ($config['base_path'] ?: '') . '/',
        'httponly' => true,   // el JS no puede leer la cookie de sesión
        'secure'   => $https, // solo viaja por HTTPS cuando hay HTTPS
        'samesite' => 'Lax',  // corta el CSRF por navegación externa
    ]);
    session_name('aproavena_sess');
    session_start();
}
