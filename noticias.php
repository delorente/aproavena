<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';

$noticias = db()->query(
    'SELECT id, title, summary, cover_url, published_at
       FROM noticias
      WHERE published = 1
      ORDER BY published_at DESC, id DESC'
)->fetchAll();

$pageTitle = 'Noticias — Aproavena';
$activeNav = 'noticias';
$pageDesc  = 'Novedades del sector avenero chileno y de las empresas asociadas a Aproavena.';
require __DIR__ . '/inc/header.php';
?>

<section class="av-container av-pt-64 av-pb-24 av-row-between">
  <div>
    <div class="av-eyebrow av-eyebrow--wide">Noticias</div>
    <h1 class="av-heading av-fs-34">Novedades del sector avenero</h1>
  </div>
</section>

<section class="av-container av-pt-24 av-pb-90">
  <?php if (!$noticias): ?>
    <p class="av-text">Aún no hay noticias publicadas.</p>
  <?php else: ?>
    <div class="av-grid-3">
      <?php foreach ($noticias as $n): ?>
        <a href="<?= e(url('noticia.php?id=' . (int)$n['id'])) ?>" class="av-news-card">
          <img src="<?= e(portada($n['cover_url'])) ?>" alt="<?= e($n['title']) ?>" class="av-news-img av-news-img--180">
          <div class="av-news-body">
            <div class="av-news-date av-mb-8"><?= e(fecha_es($n['published_at'])) ?></div>
            <div class="av-news-title av-news-title--19 av-mb-10"><?= e($n['title']) ?></div>
            <p class="av-news-excerpt"><?= e($n['summary']) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
