<?php
declare(strict_types=1);
/** Cabecera del panel. Requiere $pageTitle y $adminTab (noticias|mensajes). */
$pageTitle = $pageTitle ?? 'Panel — Aproavena';
$adminTab  = $adminTab  ?? 'noticias';
$u         = usuario_actual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($pageTitle) ?></title>
<link rel="icon" href="<?= e(url('favicon.ico')) ?>" sizes="32x32">
<link rel="icon" type="image/png" sizes="32x32" href="<?= e(url('assets/favicon-32.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('assets/styles.css')) ?>">
</head>
<body>

<header class="av-nav">
  <div class="av-nav__inner">
    <a href="<?= e(url()) ?>" class="av-nav__brand">
      <img src="<?= e(url('assets/logo-aproavena.png')) ?>" alt="Aproavena" class="av-nav__logo">
      <span class="av-nav__brand-text">Aproavena</span>
    </a>
    <button type="button" class="av-nav__toggle" aria-label="Abrir menú"
            aria-controls="av-nav-links" aria-expanded="false">
      <span class="av-nav__burger"></span>
    </button>
    <nav class="av-nav__links" id="av-nav-links">
      <a class="av-link<?= $adminTab === 'noticias' ? ' av-link--active' : '' ?>" href="<?= e(url('admin/')) ?>">Noticias</a>
      <a class="av-link<?= $adminTab === 'mensajes' ? ' av-link--active' : '' ?>" href="<?= e(url('admin/mensajes.php')) ?>">Mensajes</a>
      <a class="av-link" href="<?= e(url()) ?>" target="_blank" rel="noopener">Ver sitio ↗</a>
    </nav>
  </div>
</header>
<script src="<?= e(asset('assets/nav.js')) ?>" defer></script>

<section class="av-container av-container--1000 av-pt-56 av-pb-100">
  <?php if ($u): ?>
    <div class="av-admin-session">
      <div class="av-admin-session__who">Sesión: <?= e($u['email']) ?></div>
      <form method="post" action="<?= e(url('admin/logout.php')) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="av-btn-ghost--sm">Cerrar sesión</button>
      </form>
    </div>
  <?php endif; ?>
