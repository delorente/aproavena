<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$noticia = null;
if ($id) {
    $st = db()->prepare(
        'SELECT id, title, summary, body, cover_url, pdf_url, published_at
           FROM noticias
          WHERE id = ? AND published = 1
          LIMIT 1'
    );
    $st->execute([$id]);
    $noticia = $st->fetch() ?: null;
}

if (!$noticia) {
    http_response_code(404);
    $pageTitle = 'Noticia no encontrada — Aproavena';
    $activeNav = 'noticias';
    require __DIR__ . '/inc/header.php';
    ?>
    <section class="av-container av-container--780 av-py-90 av-center">
      <h1 class="av-heading av-fs-26">Noticia no encontrada</h1>
      <a href="<?= e(url('noticias.php')) ?>" class="av-link-accent">← Volver a noticias</a>
    </section>
    <?php
    require __DIR__ . '/inc/footer.php';
    exit;
}

$pageTitle = $noticia['title'] . ' — Aproavena';
$activeNav = 'noticias';
$pageDesc  = extracto($noticia['summary'], 160);
require __DIR__ . '/inc/header.php';
?>

<section class="av-hero av-hero--340">
  <img src="<?= e(portada($noticia['cover_url'])) ?>" alt="<?= e($noticia['title']) ?>" class="av-hero__img">
  <div class="av-hero__overlay av-hero__overlay--50"></div>
</section>

<section class="av-container av-container--780 av-pt-48 av-pb-90">
  <a href="<?= e(url('noticias.php')) ?>" class="av-back">← Volver a noticias</a>
  <div class="av-article-date"><?= e(fecha_es($noticia['published_at'])) ?></div>
  <h1 class="av-heading av-fs-32 av-mb-22"><?= e($noticia['title']) ?></h1>
  <?php // nl2br sobre texto ya escapado: respeta los saltos de línea del panel
        // sin permitir que se cuele HTML desde el formulario. ?>
  <p class="av-article-body"><?= nl2br(e($noticia['body'])) ?></p>
  <?php if (!empty($noticia['pdf_url'])): ?>
    <a href="<?= e(url($noticia['pdf_url'])) ?>" target="_blank" rel="noopener" class="av-pdf-link">Leer publicación original (PDF) →</a>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
