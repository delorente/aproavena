<?php
declare(strict_types=1);

/**
 * Conexión PDO única y perezosa: se abre la primera vez que alguien la pide.
 * Las páginas puramente estáticas nunca tocan MySQL.
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    global $config;
    $c = $config['db'];

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $c['host'], $c['name'], $c['charset']);

    try {
        $pdo = new PDO($dsn, $c['user'], $c['pass'], [
            // Excepciones en vez de errores silenciosos.
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Prepared statements reales en el servidor, no emulados: es lo que
            // hace que una inyección SQL no sea posible aunque el dato venga sucio.
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('Aproavena · fallo de conexión a MySQL: ' . $e->getMessage());
        http_response_code(503);
        exit('El sitio no está disponible en este momento.');
    }

    return $pdo;
}
