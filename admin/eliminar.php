<?php
declare(strict_types=1);
require __DIR__ . '/../inc/bootstrap.php';
require __DIR__ . '/../inc/upload.php';
requerir_login();

// Borrar solo por POST y con token válido.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/');
}
csrf_check();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect('admin/');
}

$st = db()->prepare('SELECT cover_url, pdf_url FROM noticias WHERE id = ? LIMIT 1');
$st->execute([$id]);
$noticia = $st->fetch();

if ($noticia) {
    db()->prepare('DELETE FROM noticias WHERE id = ?')->execute([$id]);
    // borrar_subida solo actúa sobre media/noticias/, así que las imágenes
    // de assets/ que vinieron con el sitio quedan intactas.
    borrar_subida($noticia['cover_url']);
    borrar_subida($noticia['pdf_url']);
    $_SESSION['flash'] = 'Noticia eliminada.';
}

redirect('admin/');
