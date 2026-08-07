<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';

$errores = [];
$viejo   = ['nombre' => '', 'correo' => '', 'empresa' => '', 'mensaje' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    // Trampa para bots: el campo está oculto por CSS, una persona nunca lo llena.
    // Fingimos éxito para no darle pistas al bot sobre por qué falló.
    if (trim((string)($_POST['sitio_web'] ?? '')) !== '') {
        $_SESSION['contacto_ok'] = true;
        redirect('contacto.php');
    }

    $nombre  = trim((string)($_POST['nombre']  ?? ''));
    $correo  = trim((string)($_POST['correo']  ?? ''));
    $empresa = trim((string)($_POST['empresa'] ?? ''));
    $mensaje = trim((string)($_POST['mensaje'] ?? ''));
    $viejo   = compact('nombre', 'correo', 'empresa', 'mensaje');

    if ($nombre === '' || mb_strlen($nombre) > 120) {
        $errores['nombre'] = 'Indícanos tu nombre.';
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || mb_strlen($correo) > 190) {
        $errores['correo'] = 'Revisa tu dirección de correo.';
    }
    if (mb_strlen($empresa) > 160) {
        $errores['empresa'] = 'El nombre de la organización es demasiado largo.';
    }
    if ($mensaje === '') {
        $errores['mensaje'] = 'Escríbenos tu mensaje.';
    } elseif (mb_strlen($mensaje) > 5000) {
        $errores['mensaje'] = 'El mensaje es demasiado largo (máximo 5.000 caracteres).';
    }

    if (!$errores) {
        // 1) Guardar primero. Si el correo del VPS falla, el mensaje no se pierde.
        $st = db()->prepare(
            'INSERT INTO mensajes (nombre, correo, empresa, mensaje, ip)
             VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([
            $nombre,
            $correo,
            $empresa !== '' ? $empresa : null,
            $mensaje,
            substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
        ]);
        $mensajeId = (int)db()->lastInsertId();

        // 2) Intentar el envío por correo.
        $asunto = '=?UTF-8?B?' . base64_encode('Contacto desde el sitio Aproavena') . '?=';
        $cuerpo = "Nombre:  {$nombre}\n"
                . "Correo:  {$correo}\n"
                . "Empresa: " . ($empresa !== '' ? $empresa : '—') . "\n"
                . "Fecha:   " . date('d-m-Y H:i') . "\n\n"
                . "-----\n{$mensaje}\n";

        // El From debe ser del propio dominio o SPF/DKIM lo marcan como spam.
        // El correo del visitante va en Reply-To, que es donde corresponde.
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: Sitio Aproavena <' . $config['mail']['from'] . '>',
            'Reply-To: ' . str_replace(["\r", "\n"], '', $correo),
        ];

        $enviado = @mail(
            $config['mail']['to'],
            $asunto,
            $cuerpo,
            implode("\r\n", $headers),
            '-f' . $config['mail']['from']
        );

        if ($enviado) {
            db()->prepare('UPDATE mensajes SET enviado = 1 WHERE id = ?')->execute([$mensajeId]);
        } else {
            error_log("Aproavena · mensaje #{$mensajeId} guardado pero mail() falló.");
        }

        // Patrón POST → Redirect → GET: al recargar no se reenvía el formulario.
        $_SESSION['contacto_ok'] = true;
        redirect('contacto.php');
    }
}

$enviadoOk = !empty($_SESSION['contacto_ok']);
unset($_SESSION['contacto_ok']);

$pageTitle = 'Contacto — Aproavena';
$activeNav = 'contacto';
$pageDesc  = 'Escríbenos: prensa, potenciales socios o coordinación institucional.';
require __DIR__ . '/inc/header.php';
?>

<section class="av-container av-pt-56 av-pb-100 av-contact">
  <div class="av-contact__aside">
    <img src="<?= e(url('assets/foto-grano-manos.webp')) ?>" alt="" class="av-contact__aside-img">
    <div class="av-contact__aside-inner">
      <div class="av-contact__eyebrow">Contacto</div>
      <h1 class="av-contact__aside-title">Hablemos sobre la industria de la avena</h1>
      <p class="av-contact__aside-text">Prensa, potenciales socios o coordinación institucional: te respondemos a la brevedad.</p>
    </div>
    <div class="av-contact__meta">
      <div class="av-contact__meta-item">
        <div class="av-contact__meta-label">Correo</div>
        <a href="mailto:contacto@aproavena.cl" class="av-contact__meta-value">contacto@aproavena.cl</a>
      </div>
      <div class="av-contact__meta-item">
        <div class="av-contact__meta-label">Ubicación</div>
        <span class="av-contact__meta-value">Chillán, Región de Ñuble, Chile</span>
      </div>
    </div>
  </div>

  <div class="av-contact__main">
    <?php if ($enviadoOk): ?>
      <div class="av-sent">
        <div class="av-sent__check">✓</div>
        <h2 class="av-sent__title">Mensaje enviado</h2>
        <p class="av-sent__text">Gracias por escribirnos. Te responderemos a la brevedad al correo que nos dejaste.</p>
        <a href="<?= e(url('contacto.php')) ?>" class="av-btn-ghost av-self-start av-mt-10">Enviar otro mensaje</a>
      </div>
    <?php else: ?>
      <?php if ($errores): ?>
        <div class="av-notice av-notice--error">Revisa los campos marcados y vuelve a enviar.</div>
      <?php endif; ?>
      <form class="av-form" method="post" action="<?= e(url('contacto.php')) ?>" novalidate>
        <?= csrf_field() ?>
        <div class="av-form__row">
          <div class="av-field">
            <label for="f-nombre">NOMBRE</label>
            <input id="f-nombre" name="nombre" type="text" required maxlength="120" value="<?= e($viejo['nombre']) ?>">
            <?php if (isset($errores['nombre'])): ?><span class="av-field__error"><?= e($errores['nombre']) ?></span><?php endif; ?>
          </div>
          <div class="av-field">
            <label for="f-correo">CORREO</label>
            <input id="f-correo" name="correo" type="email" required maxlength="190" value="<?= e($viejo['correo']) ?>">
            <?php if (isset($errores['correo'])): ?><span class="av-field__error"><?= e($errores['correo']) ?></span><?php endif; ?>
          </div>
        </div>
        <div class="av-field">
          <label for="f-empresa">EMPRESA U ORGANIZACIÓN</label>
          <input id="f-empresa" name="empresa" type="text" maxlength="160" value="<?= e($viejo['empresa']) ?>">
        </div>
        <div class="av-field">
          <label for="f-mensaje">MENSAJE</label>
          <textarea id="f-mensaje" name="mensaje" required rows="5" maxlength="5000" class="av-resize-v"><?= e($viejo['mensaje']) ?></textarea>
          <?php if (isset($errores['mensaje'])): ?><span class="av-field__error"><?= e($errores['mensaje']) ?></span><?php endif; ?>
        </div>
        <div class="av-hp" aria-hidden="true">
          <label>No llenar este campo<input type="text" name="sitio_web" tabindex="-1" autocomplete="off"></label>
        </div>
        <button type="submit" class="av-btn av-btn--primary av-btn--submit av-self-start">Enviar mensaje →</button>
      </form>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
