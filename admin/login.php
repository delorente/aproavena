<?php
declare(strict_types=1);
require __DIR__ . '/../inc/bootstrap.php';

if (usuario_actual()) {
    redirect('admin/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    // Freno simple a la fuerza bruta: 5 intentos por sesión, luego 60 s de espera.
    $intentos = $_SESSION['login_intentos'] ?? 0;
    $bloqueoHasta = $_SESSION['login_bloqueo'] ?? 0;

    if ($bloqueoHasta > time()) {
        $error = 'Demasiados intentos. Espera ' . ($bloqueoHasta - time()) . ' segundos.';
    } else {
        $usuario = autenticar((string)($_POST['email'] ?? ''), (string)($_POST['password'] ?? ''));

        if ($usuario) {
            unset($_SESSION['login_intentos'], $_SESSION['login_bloqueo']);
            $destino = $_SESSION['redirigir_a'] ?? '';
            unset($_SESSION['redirigir_a']);
            iniciar_sesion($usuario);
            // Solo se acepta un destino interno, nunca una URL externa.
            if ($destino && str_starts_with($destino, '/') && !str_starts_with($destino, '//')) {
                header('Location: ' . $destino);
                exit;
            }
            redirect('admin/');
        }

        $intentos++;
        $_SESSION['login_intentos'] = $intentos;
        if ($intentos >= 5) {
            $_SESSION['login_bloqueo']  = time() + 60;
            $_SESSION['login_intentos'] = 0;
        }
        $error = 'Correo o contraseña incorrectos.';
    }
}

$pageTitle = 'Ingresar al panel — Aproavena';
$adminTab  = '';
require __DIR__ . '/../inc/admin-header.php';
?>

<div class="av-eyebrow av-eyebrow--wide">Panel de socios</div>
<h1 class="av-heading av-fs-30 av-mb-32">Ingresar</h1>

<form class="av-admin-form" method="post" action="<?= e(url('admin/login.php')) ?>">
  <?= csrf_field() ?>
  <div>
    <label class="av-admin-label" for="l-email">Correo</label>
    <input class="av-input" id="l-email" type="email" name="email" required autocomplete="username"
           value="<?= e((string)($_POST['email'] ?? '')) ?>">
  </div>
  <div>
    <label class="av-admin-label" for="l-pass">Contraseña</label>
    <input class="av-input" id="l-pass" type="password" name="password" required autocomplete="current-password">
  </div>
  <button type="submit" class="av-btn-primary-sm av-self-start">Ingresar</button>
  <?php if ($error): ?><div class="av-error"><?= e($error) ?></div><?php endif; ?>
</form>

<?php require __DIR__ . '/../inc/admin-footer.php'; ?>
