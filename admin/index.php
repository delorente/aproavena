<?php
declare(strict_types=1);
require __DIR__ . '/../inc/bootstrap.php';
requerir_login();

$noticias = db()->query(
    'SELECT id, title, published, published_at
       FROM noticias
      ORDER BY published_at DESC, id DESC'
)->fetchAll();

$sinLeer = (int)db()->query('SELECT COUNT(*) FROM mensajes WHERE leido = 0')->fetchColumn();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$pageTitle = 'Noticias — Panel Aproavena';
$adminTab  = 'noticias';
require __DIR__ . '/../inc/admin-header.php';
?>

<div class="av-eyebrow av-eyebrow--wide">Panel de socios</div>
<h1 class="av-heading av-fs-30 av-mb-32">Administrar noticias</h1>

<?php if ($flash): ?>
  <div class="av-notice av-notice--ok"><?= e($flash) ?></div>
<?php endif; ?>

<?php if ($sinLeer > 0): ?>
  <div class="av-notice">
    Tienes <strong><?= $sinLeer ?></strong> mensaje<?= $sinLeer === 1 ? '' : 's' ?> de contacto sin leer.
    <a href="<?= e(url('admin/mensajes.php')) ?>" class="av-link-accent">Ver mensajes →</a>
  </div>
<?php endif; ?>

<div class="av-btn-row av-mb-32">
  <a href="<?= e(url('admin/noticia.php')) ?>" class="av-btn-primary-sm">+ Nueva noticia</a>
</div>

<h2 class="av-list-title">Noticias cargadas (<?= count($noticias) ?>)</h2>

<?php if (!$noticias): ?>
  <p class="av-text">Todavía no hay noticias. Crea la primera con el botón de arriba.</p>
<?php else: ?>
  <div class="av-rows">
    <?php foreach ($noticias as $n): ?>
      <div class="av-row-item">
        <div>
          <div class="av-row-item__title"><?= e($n['title']) ?></div>
          <div class="av-row-item__meta">
            <?= e(fecha_es($n['published_at'])) ?> ·
            <?= $n['published'] ? 'Publicada' : 'Borrador' ?>
          </div>
        </div>
        <div class="av-row-item__actions">
          <a class="av-btn-mini" href="<?= e(url('admin/noticia.php?id=' . (int)$n['id'])) ?>">Editar</a>
          <form method="post" action="<?= e(url('admin/eliminar.php')) ?>"
                onsubmit="return confirm('¿Eliminar «<?= e(addslashes($n['title'])) ?>»? Esta acción no se puede deshacer.');">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
            <button type="submit" class="av-btn-mini av-btn-mini--danger">Eliminar</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../inc/admin-footer.php'; ?>
