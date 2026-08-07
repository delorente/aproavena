<?php
declare(strict_types=1);
require __DIR__ . '/../inc/bootstrap.php';
require __DIR__ . '/../inc/upload.php';
requerir_login();

$id      = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$errores = [];

// --- Cargar la noticia si se está editando -----------------------------------
if ($id) {
    $st = db()->prepare('SELECT * FROM noticias WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    $noticia = $st->fetch();
    if (!$noticia) {
        http_response_code(404);
        exit('Noticia no encontrada.');
    }
} else {
    $noticia = [
        'id' => null, 'title' => '', 'summary' => '', 'body' => '',
        'cover_url' => null, 'pdf_url' => null,
        'published' => 1, 'published_at' => date('Y-m-d'),
    ];
}

// --- Guardar -----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $noticia['title']        = trim((string)($_POST['title'] ?? ''));
    $noticia['summary']      = trim((string)($_POST['summary'] ?? ''));
    $noticia['body']         = trim((string)($_POST['body'] ?? ''));
    $noticia['published_at'] = trim((string)($_POST['published_at'] ?? ''));
    $noticia['published']    = isset($_POST['published']) ? 1 : 0;

    if ($noticia['title'] === '' || mb_strlen($noticia['title']) > 255) {
        $errores[] = 'El título es obligatorio (máximo 255 caracteres).';
    }
    if ($noticia['summary'] === '') {
        $errores[] = 'La bajada es obligatoria.';
    }
    if ($noticia['body'] === '') {
        $errores[] = 'El cuerpo de la noticia es obligatorio.';
    }
    $fechaOk = DateTime::createFromFormat('Y-m-d', $noticia['published_at']);
    if (!$fechaOk || $fechaOk->format('Y-m-d') !== $noticia['published_at']) {
        $errores[] = 'La fecha de publicación no es válida.';
    }

    // Archivos nuevos (opcionales). Se guardan las rutas viejas para borrarlas
    // solo si todo lo demás sale bien.
    $coverAnterior = $noticia['cover_url'];
    $pdfAnterior   = $noticia['pdf_url'];
    $coverNuevo    = null;
    $pdfNuevo      = null;

    if (!$errores) {
        try {
            $coverNuevo = guardar_subida($_FILES['cover'] ?? [], 'imagen');
            $pdfNuevo   = guardar_subida($_FILES['pdf'] ?? [], 'pdf');
        } catch (RuntimeException $ex) {
            $errores[] = $ex->getMessage();
        }
    }

    if (!$errores) {
        if ($coverNuevo) {
            $noticia['cover_url'] = $coverNuevo;
        } elseif (!empty($_POST['quitar_cover'])) {
            $noticia['cover_url'] = null;
        }

        if ($pdfNuevo) {
            $noticia['pdf_url'] = $pdfNuevo;
        } elseif (!empty($_POST['quitar_pdf'])) {
            $noticia['pdf_url'] = null;
        }

        if ($id) {
            db()->prepare(
                'UPDATE noticias
                    SET title = ?, summary = ?, body = ?, cover_url = ?, pdf_url = ?,
                        published = ?, published_at = ?
                  WHERE id = ?'
            )->execute([
                $noticia['title'], $noticia['summary'], $noticia['body'],
                $noticia['cover_url'], $noticia['pdf_url'],
                $noticia['published'], $noticia['published_at'], $id,
            ]);
            $_SESSION['flash'] = 'Noticia actualizada.';
        } else {
            db()->prepare(
                'INSERT INTO noticias (title, summary, body, cover_url, pdf_url, published, published_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            )->execute([
                $noticia['title'], $noticia['summary'], $noticia['body'],
                $noticia['cover_url'], $noticia['pdf_url'],
                $noticia['published'], $noticia['published_at'],
            ]);
            $_SESSION['flash'] = 'Noticia creada.';
        }

        // Ya guardado: recién ahora se borran los archivos reemplazados.
        if ($coverNuevo && $coverAnterior !== $coverNuevo) {
            borrar_subida($coverAnterior);
        }
        if ($pdfNuevo && $pdfAnterior !== $pdfNuevo) {
            borrar_subida($pdfAnterior);
        }

        redirect('admin/');
    }
}

$pageTitle = ($id ? 'Editar' : 'Nueva') . ' noticia — Panel Aproavena';
$adminTab  = 'noticias';
require __DIR__ . '/../inc/admin-header.php';
?>

<div class="av-eyebrow av-eyebrow--wide">Panel de socios</div>
<h1 class="av-heading av-fs-30 av-mb-32"><?= $id ? 'Editar noticia' : 'Nueva noticia' ?></h1>

<?php if ($errores): ?>
  <div class="av-notice av-notice--error">
    <?php foreach ($errores as $msg): ?>
      <div><?= e($msg) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="av-card-box">
  <form class="av-stack-16" method="post" enctype="multipart/form-data"
        action="<?= e(url('admin/noticia.php' . ($id ? '?id=' . $id : ''))) ?>">
    <?= csrf_field() ?>

    <div>
      <label class="av-admin-label" for="f-title">Título</label>
      <input class="av-input" id="f-title" type="text" name="title" maxlength="255" required
             value="<?= e($noticia['title']) ?>">
    </div>

    <div>
      <label class="av-admin-label" for="f-summary">Bajada / resumen</label>
      <textarea class="av-input av-resize-v" id="f-summary" name="summary" rows="2" required><?= e($noticia['summary']) ?></textarea>
    </div>

    <div>
      <label class="av-admin-label" for="f-body">Cuerpo de la noticia</label>
      <textarea class="av-input av-resize-v" id="f-body" name="body" rows="8" required><?= e($noticia['body']) ?></textarea>
      <span class="av-file-note">Los saltos de línea se respetan en el sitio.</span>
    </div>

    <div class="av-form-2col">
      <div>
        <label class="av-admin-label" for="f-fecha">Fecha de publicación</label>
        <input class="av-input" id="f-fecha" type="date" name="published_at" required
               value="<?= e($noticia['published_at']) ?>">
      </div>
      <div>
        <label class="av-admin-label" for="f-cover">Imagen de portada</label>
        <input class="av-input" id="f-cover" type="file" name="cover" accept="image/jpeg,image/png,image/webp,image/gif">
        <span class="av-file-note">JPG, PNG, WebP o GIF · máximo 8 MB</span>
      </div>
    </div>

    <?php if (!empty($noticia['cover_url'])): ?>
      <div>
        <img src="<?= e(url($noticia['cover_url'])) ?>" alt="Portada actual" class="av-cover-preview">
        <label class="av-checkbox-row">
          <input type="checkbox" name="quitar_cover" value="1"> Quitar la portada actual
        </label>
      </div>
    <?php endif; ?>

    <div>
      <label class="av-admin-label" for="f-pdf">PDF adjunto (opcional)</label>
      <input class="av-input" id="f-pdf" type="file" name="pdf" accept="application/pdf">
      <span class="av-file-note">Se muestra como «Leer publicación original (PDF)» · máximo 20 MB</span>
      <?php if (!empty($noticia['pdf_url'])): ?>
        <label class="av-checkbox-row">
          <input type="checkbox" name="quitar_pdf" value="1"> Quitar el PDF actual
          (<a href="<?= e(url($noticia['pdf_url'])) ?>" target="_blank" rel="noopener">ver</a>)
        </label>
      <?php endif; ?>
    </div>

    <label class="av-checkbox-row">
      <input type="checkbox" name="published" value="1" <?= $noticia['published'] ? 'checked' : '' ?>>
      Publicada (visible en el sitio)
    </label>

    <div class="av-btn-row">
      <button type="submit" class="av-btn-primary-sm"><?= $id ? 'Guardar cambios' : 'Publicar noticia' ?></button>
      <a href="<?= e(url('admin/')) ?>" class="av-btn-secondary-sm">Cancelar</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../inc/admin-footer.php'; ?>
