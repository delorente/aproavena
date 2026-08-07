<?php
declare(strict_types=1);
require __DIR__ . '/../inc/bootstrap.php';
requerir_login();

// Marcar como leído / eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $accion = (string)($_POST['accion'] ?? '');

    if ($id && $accion === 'leido') {
        db()->prepare('UPDATE mensajes SET leido = 1 WHERE id = ?')->execute([$id]);
    } elseif ($id && $accion === 'eliminar') {
        db()->prepare('DELETE FROM mensajes WHERE id = ?')->execute([$id]);
        $_SESSION['flash'] = 'Mensaje eliminado.';
    }
    redirect('admin/mensajes.php');
}

$mensajes = db()->query(
    'SELECT id, nombre, correo, empresa, mensaje, enviado, leido, created_at
       FROM mensajes
      ORDER BY created_at DESC'
)->fetchAll();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$pageTitle = 'Mensajes — Panel Aproavena';
$adminTab  = 'mensajes';
require __DIR__ . '/../inc/admin-header.php';
?>

<div class="av-eyebrow av-eyebrow--wide">Panel de socios</div>
<h1 class="av-heading av-fs-30 av-mb-32">Mensajes de contacto</h1>

<?php if ($flash): ?>
  <div class="av-notice av-notice--ok"><?= e($flash) ?></div>
<?php endif; ?>

<?php if (!$mensajes): ?>
  <p class="av-text">Todavía no hay mensajes.</p>
<?php else: ?>
  <div class="av-rows">
    <?php foreach ($mensajes as $m): ?>
      <article class="av-msg<?= $m['leido'] ? '' : ' av-msg--nuevo' ?>">
        <div class="av-msg__head">
          <div>
            <span class="av-msg__from"><?= e($m['nombre']) ?></span>
            <?php if ($m['empresa']): ?>
              <span class="av-msg__org">· <?= e($m['empresa']) ?></span>
            <?php endif; ?>
          </div>
          <div class="av-msg__date">
            <?= e(date('d-m-Y H:i', strtotime($m['created_at']))) ?>
            <?php if (!$m['enviado']): ?>
              <span class="av-msg__warn" title="Se guardó, pero el servidor no pudo enviar el aviso por correo">· sin enviar</span>
            <?php endif; ?>
          </div>
        </div>
        <a class="av-msg__mail" href="mailto:<?= e($m['correo']) ?>?subject=<?= e(rawurlencode('Re: contacto desde el sitio Aproavena')) ?>"><?= e($m['correo']) ?></a>
        <p class="av-msg__body"><?= nl2br(e($m['mensaje'])) ?></p>
        <div class="av-row-item__actions">
          <?php if (!$m['leido']): ?>
            <form method="post" action="<?= e(url('admin/mensajes.php')) ?>">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
              <input type="hidden" name="accion" value="leido">
              <button type="submit" class="av-btn-mini">Marcar como leído</button>
            </form>
          <?php endif; ?>
          <form method="post" action="<?= e(url('admin/mensajes.php')) ?>"
                onsubmit="return confirm('¿Eliminar este mensaje?');">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
            <input type="hidden" name="accion" value="eliminar">
            <button type="submit" class="av-btn-mini av-btn-mini--danger">Eliminar</button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../inc/admin-footer.php'; ?>
