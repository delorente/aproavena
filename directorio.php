<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';

// Los socios son contenido estable: viven aquí y no en la base de datos.
// Si algún día el cliente necesita editarlos solo, se pasan a una tabla.
// Sin altura por logo: el tamaño lo normaliza .av-socio__img en el CSS.
$socios = [
    [
        'name'  => 'Empresas Gorbea',
        'logo'  => 'assets/socio-eg.webp',
        'desc'  => 'Empresa procesadora con presencia histórica en la industria alimentaria del sur de Chile.',
    ],
    [
        'name'  => 'Itata',
        'logo'  => 'assets/socio-itata.webp',
        'desc'  => 'Procesadora vinculada a la producción agrícola de la cuenca del Itata.',
    ],
    [
        'name'  => 'AEG Nutrición',
        'logo'  => 'assets/socio-aeg.webp',
        'desc'  => 'Especialista en nutrición e ingredientes a base de avena y otros cereales.',
    ],
    [
        'name'  => 'Agrotop',
        'logo'  => 'assets/socio-agrotop.webp',
        'desc'  => 'Empresa agroindustrial enfocada en el desarrollo de proveedores y productores locales.',
    ],
    [
        'name'  => 'Carozzi',
        'logo'  => null,
        'desc'  => 'Una de las principales empresas de alimentos de Chile, con línea de productos elaborados en base a avena.',
    ],
];

$pageTitle = 'Directorio y socios — Aproavena';
$activeNav = 'directorio';
$pageDesc  = 'Empresas procesadoras de avena que integran la asociación gremial Aproavena.';
require __DIR__ . '/inc/header.php';
?>

<section class="av-container av-pt-64 av-pb-24">
  <div class="av-eyebrow av-eyebrow--wide">Directorio y socios</div>
  <h1 class="av-heading av-fs-34 av-mb-16">Empresas asociadas a Aproavena</h1>
  <p class="av-text av-mw-68ch">Estas son las empresas procesadoras de avena que integran nuestra asociación gremial, representando distintas zonas y escalas de la industria nacional.</p>
</section>

<section class="av-container av-pt-32 av-pb-90 av-stack-24">
  <?php foreach ($socios as $i => $s): ?>
    <div class="av-socio">
      <div class="av-socio__logo">
        <?php if ($s['logo']): ?>
          <img src="<?= e(url($s['logo'])) ?>" alt="<?= e($s['name']) ?>" class="av-socio__img" loading="lazy">
        <?php else: ?>
          <span class="av-socio__logo-text"><?= e($s['name']) ?></span>
        <?php endif; ?>
      </div>
      <div class="av-socio__body">
        <div class="av-socio__head">
          <span class="av-socio__index"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
          <h2 class="av-socio__name"><?= e($s['name']) ?></h2>
        </div>
        <p class="av-socio__desc"><?= e($s['desc']) ?></p>
      </div>
    </div>
  <?php endforeach; ?>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
