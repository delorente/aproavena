<?php
declare(strict_types=1);

/** Escapa para HTML. Se usa en TODA salida de datos variables. */
function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** URL absoluta desde la raíz del sitio, respetando base_path. */
function url(string $path = ''): string
{
    global $config;
    return ($config['base_path'] ?? '') . '/' . ltrim($path, '/');
}

/** Redirige y corta la ejecución. */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/** "2022-04-18" → "18 de abril de 2022" (sin depender de intl ni locales). */
function fecha_es(?string $sqlDate): string
{
    if (!$sqlDate) {
        return '';
    }
    $ts = strtotime($sqlDate);
    if ($ts === false) {
        return '';
    }
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
              'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

    return (int)date('j', $ts) . ' de ' . $meses[(int)date('n', $ts) - 1] . ' de ' . date('Y', $ts);
}

/** Portada de una noticia, con imagen por defecto si no tiene. */
function portada(?string $coverUrl): string
{
    return url($coverUrl ?: 'assets/foto-procesamiento-grano.webp');
}

/** Recorta un texto a $max caracteres sin cortar palabras. */
function extracto(string $texto, int $max = 160): string
{
    $texto = trim(preg_replace('/\s+/', ' ', $texto));
    if (mb_strlen($texto) <= $max) {
        return $texto;
    }
    $corte = mb_substr($texto, 0, $max);
    $sp    = mb_strrpos($corte, ' ');

    return ($sp !== false ? mb_substr($corte, 0, $sp) : $corte) . '…';
}


// --- CSRF --------------------------------------------------------------------

/** Token de la sesión; se genera una vez y se reutiliza. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Campo oculto listo para pegar dentro de un <form>. */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/**
 * Valida el token de un POST. Corta con 403 si no cuadra.
 * hash_equals compara en tiempo constante para no filtrar el token.
 */
function csrf_check(): void
{
    $enSesion = $_SESSION['csrf'] ?? '';
    $enviado  = $_POST['_csrf']   ?? '';

    // El token de la sesión debe existir. Sin esta comprobación, una sesión
    // recién creada (token vacío) frente a un POST sin token daría
    // hash_equals('', '') === true y la validación pasaría de largo.
    if (!is_string($enSesion) || $enSesion === ''
        || !is_string($enviado) || !hash_equals($enSesion, $enviado)) {
        http_response_code(403);
        exit('Sesión expirada o solicitud no válida. Vuelve atrás y reintenta.');
    }
}
