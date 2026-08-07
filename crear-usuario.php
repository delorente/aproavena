<?php
declare(strict_types=1);

/**
 * Crea o actualiza un usuario del panel.
 *
 *   php crear-usuario.php correo@aproavena.cl "Nombre Apellido"
 *
 * Pide la contraseña por teclado para que no quede en el historial del shell.
 * Solo funciona por consola: si alguien lo abre por el navegador, no hace nada.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script solo puede ejecutarse desde la consola.');
}

require __DIR__ . '/inc/bootstrap.php';

$email  = $argv[1] ?? '';
$nombre = $argv[2] ?? null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Uso: php crear-usuario.php correo@dominio.cl \"Nombre Apellido\"\n");
    exit(1);
}
$email = mb_strtolower(trim($email));

// Lectura sin eco cuando el sistema lo permite.
function leer_password(string $prompt): string
{
    echo $prompt;
    if (DIRECTORY_SEPARATOR !== '\\' && shell_exec('which stty')) {
        $modo = trim((string)shell_exec('stty -g'));
        shell_exec('stty -echo');
        $pass = trim((string)fgets(STDIN));
        shell_exec('stty ' . $modo);
        echo "\n";
        return $pass;
    }
    // En Windows/WAMP se escribe visible; se avisa arriba.
    return trim((string)fgets(STDIN));
}

$pass1 = leer_password("Contraseña (mínimo 10 caracteres): ");
if (mb_strlen($pass1) < 10) {
    fwrite(STDERR, "La contraseña debe tener al menos 10 caracteres.\n");
    exit(1);
}
$pass2 = leer_password("Repite la contraseña: ");
if (!hash_equals($pass1, $pass2)) {
    fwrite(STDERR, "Las contraseñas no coinciden.\n");
    exit(1);
}

$hash = password_hash($pass1, PASSWORD_DEFAULT);

$st = db()->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
$st->execute([$email]);

if ($st->fetch()) {
    db()->prepare('UPDATE usuarios SET password_hash = ?, nombre = COALESCE(?, nombre) WHERE email = ?')
        ->execute([$hash, $nombre, $email]);
    echo "Contraseña actualizada para {$email}\n";
} else {
    db()->prepare('INSERT INTO usuarios (email, nombre, password_hash) VALUES (?, ?, ?)')
        ->execute([$email, $nombre, $hash]);
    echo "Usuario creado: {$email}\n";
}
