<?php
declare(strict_types=1);
/**
 * Cabecera común + navegación. Antes de incluirla, define:
 *   $pageTitle  string  título del <title>
 *   $activeNav  string  inicio|quienes|avena|directorio|noticias|graficos|contacto
 *   $pageDesc   string  (opcional) meta description
 *   $pageHead   string  (opcional) etiquetas extra para el <head> de esa página
 */
$pageTitle = $pageTitle ?? 'Aproavena';
$activeNav = $activeNav ?? '';
$pageDesc  = $pageDesc  ?? 'Asociación de Procesadores de Avena de Chile A.G. Representamos a la industria procesadora de avena del país.';
$pageHead  = $pageHead  ?? '';

$navLinks = [
    ['inicio',     'Inicio',              ''],
    ['quienes',    'Quiénes somos',       'quienes-somos.php'],
    ['avena',      'La Avena',            'la-avena.php'],
    ['directorio', 'Directorio y socios', 'directorio.php'],
    ['noticias',   'Noticias',            'noticias.php'],
    ['graficos',   'Gráficos',            'avena-dashboard.php'],
    ['contacto',   'Contacto',            'contacto.php'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<link rel="icon" href="<?= e(url('favicon.ico')) ?>" sizes="32x32">
<link rel="icon" type="image/png" sizes="32x32" href="<?= e(url('assets/favicon-32.png')) ?>">
<link rel="icon" type="image/png" sizes="192x192" href="<?= e(url('assets/favicon-192.png')) ?>">
<link rel="apple-touch-icon" href="<?= e(url('assets/apple-touch-icon.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(url('assets/styles.css')) ?>">
<?php /* Ya viene escapado por quien lo define: son etiquetas, no texto. */ ?>
<?= $pageHead ?>
</head>
<body>

<header class="av-nav">
  <div class="av-nav__inner">
    <a href="<?= e(url()) ?>" class="av-nav__brand">
      <img src="<?= e(url('assets/logo-aproavena.png')) ?>" alt="Aproavena" class="av-nav__logo">
      <span class="av-nav__brand-text">Aproavena</span>
    </a>
    <nav class="av-nav__links">
      <?php foreach ($navLinks as [$key, $label, $href]): ?>
        <a class="av-link<?= $key === $activeNav ? ' av-link--active' : '' ?>"
           href="<?= e(url($href)) ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
