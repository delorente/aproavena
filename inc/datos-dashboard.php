<?php
declare(strict_types=1);
/**
 * Entrega los archivos de datos del dashboard.
 *
 * La carpeta data/ está cerrada por su propio .htaccess, así que los archivos
 * solo salen por aquí, y solo los de la lista blanca.
 *
 * Se incluye desde avena-dashboard.php ANTES de bootstrap.php: estas peticiones
 * no necesitan sesión, y session_start() bloquea el archivo de sesión, lo que
 * serializaría las diez descargas que el navegador lanza en paralelo al abrir
 * la página.
 */

/** Nombres exactos permitidos. Cualquier otra cosa no se sirve. */
const DASHBOARD_DATOS = [
    'produccion.xlsx',
    'rendimiento.xlsx',
    'superficie.xlsx',
    'precios_a_productor_USA.xlsx',
    'precios_a_productor_CA.xlsx',
    'consumo_34.xlsx',
    'polinomio_costos.xlsx',
    'eeuu_stocks.xlsx',
    'exportaciones_resumidas.json',
    'importaciones_resumidas.json',
];

/** Cuánto puede reusar el navegador el archivo sin volver a preguntar. */
const DASHBOARD_CACHE_SEGUNDOS = 3600;

$f = isset($_GET['f']) ? basename((string)$_GET['f']) : '';

if ($f === '') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Falta el parámetro f');
}

if (!in_array($f, DASHBOARD_DATOS, true)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Archivo no permitido');
}

$path = __DIR__ . '/../data/' . $f;

if (!is_file($path)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Archivo no encontrado');
}

$mtime = (int)filemtime($path);
$size  = (int)filesize($path);
$etag  = '"' . dechex($mtime) . '-' . dechex($size) . '"';

// Caché real. Antes iba con no-store, así que los 11 MB de datos volvían a
// bajar en cada refresco. Al cambiar los archivos cambia el ETag y el navegador
// se entera igual, sin esperar a que expire.
header('Cache-Control: public, max-age=' . DASHBOARD_CACHE_SEGUNDOS);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
header('ETag: ' . $etag);
header('X-Content-Type-Options: nosniff');

// Revalidación: si el navegador ya lo tiene igual, 304 y nos ahorramos el cuerpo.
$sinCambios = false;

if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
    $sinCambios = in_array($etag, array_map('trim', explode(',', $_SERVER['HTTP_IF_NONE_MATCH'])), true);
} elseif (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    $desde = strtotime((string)$_SERVER['HTTP_IF_MODIFIED_SINCE']);
    $sinCambios = $desde !== false && $desde >= $mtime;
}

if ($sinCambios) {
    http_response_code(304);
    exit;
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

if ($ext === 'json') {
    header('Content-Type: application/json; charset=utf-8');
} elseif ($ext === 'xlsx') {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // El .xlsx ya es un zip: comprimirlo otra vez solo gasta CPU. El JSON en
    // cambio sí se comprime, y lo hace mod_deflate desde el .htaccess.
    header('Content-Length: ' . $size);
} else {
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . $size);
}

header('Content-Disposition: inline; filename="' . $f . '"');

readfile($path);
