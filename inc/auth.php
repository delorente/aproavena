<?php
declare(strict_types=1);

/** ¿Hay sesión iniciada? */
function usuario_actual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

/** Puerta del panel: se llama al inicio de cada archivo de /admin. */
function requerir_login(): array
{
    $u = usuario_actual();
    if (!$u) {
        $_SESSION['redirigir_a'] = $_SERVER['REQUEST_URI'] ?? '';
        redirect('admin/login.php');
    }
    return $u;
}

/**
 * Verifica credenciales. Devuelve el usuario o null.
 * Ante correo inexistente ejecuta igual un password_verify de relleno para que
 * el tiempo de respuesta no delate qué correos existen.
 */
function autenticar(string $email, string $password): ?array
{
    $st = db()->prepare('SELECT id, email, nombre, password_hash FROM usuarios WHERE email = ? LIMIT 1');
    $st->execute([mb_strtolower(trim($email))]);
    $u = $st->fetch();

    if (!$u) {
        password_verify($password, '$2y$12$usuarioinexistenteusuarioinexistenteusuarioinexiste');
        return null;
    }
    if (!password_verify($password, $u['password_hash'])) {
        return null;
    }

    // Si el hash quedó con un coste antiguo, se actualiza al vuelo.
    if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
        $nuevo = password_hash($password, PASSWORD_DEFAULT);
        db()->prepare('UPDATE usuarios SET password_hash = ? WHERE id = ?')->execute([$nuevo, $u['id']]);
    }

    unset($u['password_hash']);
    return $u;
}

/** Abre la sesión del panel. */
function iniciar_sesion(array $usuario): void
{
    // Id nuevo tras autenticar: evita la fijación de sesión.
    session_regenerate_id(true);
    $_SESSION['usuario'] = $usuario;
}

/** Cierra la sesión por completo. */
function cerrar_sesion(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => $p['samesite'] ?? 'Lax',
        ]);
    }
    session_destroy();
}
