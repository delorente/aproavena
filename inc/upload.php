<?php
declare(strict_types=1);

/**
 * Guarda un archivo subido en media/noticias/ y devuelve su ruta relativa
 * (por ejemplo "media/noticias/a1b2c3d4.jpg"), o null si no se subió nada.
 * Lanza RuntimeException con un mensaje presentable si el archivo no sirve.
 *
 * $tipo: 'imagen' | 'pdf'
 */
function guardar_subida(array $file, string $tipo): ?string
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException(match ($file['error']) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE =>
                'El archivo supera el tamaño máximo permitido por el servidor.',
            UPLOAD_ERR_PARTIAL => 'La subida se interrumpió. Reinténtalo.',
            default            => 'No se pudo subir el archivo.',
        });
    }

    // Debe venir realmente de una subida HTTP, no ser una ruta del servidor.
    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Archivo no válido.');
    }

    $maxBytes = $tipo === 'pdf' ? 20 * 1024 * 1024 : 8 * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        throw new RuntimeException(sprintf(
            'El archivo pesa %.1f MB y el máximo es %d MB.',
            $file['size'] / 1048576,
            (int)($maxBytes / 1048576)
        ));
    }

    // El tipo se decide leyendo el contenido, no la extensión ni lo que
    // declara el navegador: ambos los controla quien sube el archivo.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = (string)$finfo->file($file['tmp_name']);

    $permitidos = $tipo === 'pdf'
        ? ['application/pdf' => 'pdf']
        : [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

    if (!isset($permitidos[$mime])) {
        throw new RuntimeException($tipo === 'pdf'
            ? 'El archivo debe ser un PDF.'
            : 'La imagen debe ser JPG, PNG, WebP o GIF.');
    }

    $dir = __DIR__ . '/../media/noticias';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('No se pudo crear la carpeta de destino.');
    }
    if (!is_writable($dir)) {
        throw new RuntimeException('La carpeta media/noticias no tiene permisos de escritura.');
    }

    // Nombre generado por nosotros: descarta cualquier nombre malicioso.
    $nombre  = date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $permitidos[$mime];
    $destino = $dir . '/' . $nombre;

    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        throw new RuntimeException('No se pudo guardar el archivo en el servidor.');
    }
    @chmod($destino, 0644);

    return 'media/noticias/' . $nombre;
}

/**
 * Borra un archivo previamente subido. Solo actúa dentro de media/noticias/,
 * así una ruta manipulada no puede alcanzar otros archivos del servidor.
 */
function borrar_subida(?string $rutaRelativa): void
{
    if (!$rutaRelativa || !str_starts_with($rutaRelativa, 'media/noticias/')) {
        return;
    }
    $base = realpath(__DIR__ . '/../media/noticias');
    $full = realpath(__DIR__ . '/../' . $rutaRelativa);

    if ($base && $full && str_starts_with($full, $base) && is_file($full)) {
        @unlink($full);
    }
}
