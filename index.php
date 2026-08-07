<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';

$ultimas = db()->query(
    'SELECT id, title, cover_url, published_at
       FROM noticias
      WHERE published = 1
      ORDER BY published_at DESC, id DESC
      LIMIT 3'
)->fetchAll();

$objetivos = [
    'Informar a las autoridades sobre problemas y necesidades de sus asociados.',
    'Representar los intereses de los asociados frente a organismos públicos y privados.',
    'Propiciar el cultivo de avena en todo el país.',
    'Fortalecer los atributos de calidad e inocuidad de los productos elaborados de avena nacional.',
    'Difundir el consumo de avena.',
    'Estudiar problemas comunes de los asociados y cooperar en la solución de dificultades del rubro.',
];

// Sin altura por logo: el tamaño lo normaliza .av-partner__img en el CSS.
$socios = [
    ['assets/socio-eg.webp',      'Empresas Gorbea'],
    ['assets/socio-itata.webp',   'Itata'],
    ['assets/socio-aeg.webp',     'AEG Nutrición'],
    ['assets/socio-agrotop.webp', 'Agrotop'],
];

$pageTitle = 'Aproavena — Asociación de Procesadores de Avena de Chile A.G.';
$activeNav = 'inicio';
$pageDesc  = 'Reunimos a la industria procesadora de avena de Chile para velar por sus intereses, fortalecer la calidad de sus productos y propiciar el cultivo del cereal en todo el país.';
require __DIR__ . '/inc/header.php';
?>

<section class="av-hero av-hero--560">
  <img src="<?= e(url('assets/foto-campo-cielo.webp')) ?>" alt="Campo de avena bajo cielo abierto" class="av-hero__img">
  <div class="av-hero__overlay av-hero__overlay--gradient"></div>
  <div class="av-hero__inner">
    <div class="av-eyebrow av-eyebrow--on-dark av-mb-14">Asociación de Procesadores de Avena de Chile A.G.</div>
    <h1 class="av-hero-title av-fs-46 av-mw-15ch av-mb-18">Impulsando el cultivo y procesamiento experto de la avena chilena</h1>
    <p class="av-hero-text av-mw-52ch av-mb-28">Reunimos a la industria procesadora de avena de Chile para velar por sus intereses, fortalecer la calidad de sus productos y propiciar el cultivo del cereal en todo el país.</p>
    <div class="av-hero-actions">
      <a href="<?= e(url('quienes-somos.php')) ?>" class="av-btn av-btn--primary">Conoce la Asociación</a>
      <a href="<?= e(url('noticias.php')) ?>" class="av-btn av-btn--outline">Ver noticias</a>
    </div>
  </div>
</section>

<section class="av-container av-py-72">
  <div class="av-eyebrow">Nuestros objetivos</div>
  <h2 class="av-heading av-fs-30 av-mb-40 av-mw-34ch">Trabajamos por una industria avenera más fuerte, técnica y representada</h2>
  <div class="av-objgrid js-reveal">
    <?php foreach ($objetivos as $i => $texto): ?>
      <div class="av-obj-cell av-reveal" style="--av-reveal-delay: <?= $i * 90 ?>ms">
        <div class="av-obj-num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></div>
        <p class="av-obj-text"><?= e($texto) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="av-band">
  <div class="av-container av-py-72 av-split">
    <div>
      <div class="av-eyebrow">La avena</div>
      <h2 class="av-heading av-fs-30 av-mb-18">Expertos en cultivo, procesamiento y calidad del grano</h2>
      <p class="av-text av-mb-26">Desde la siembra en el sur de Chile hasta el producto elaborado, acompañamos los estándares técnicos que hacen de la avena nacional un cereal de referencia en calidad e inocuidad.</p>
      <a href="<?= e(url('la-avena.php')) ?>" class="av-more">Ver el proceso completo →</a>
    </div>
    <img src="<?= e(url('assets/foto-espigas-avena.webp')) ?>" alt="Espigas de avena" class="av-figure av-figure--340">
  </div>
</section>

<section class="av-band--dark">
  <div class="av-container av-py-64">
    <div class="av-eyebrow av-eyebrow--center av-eyebrow--on-dark">La industria en cifras</div>
    <div class="av-stats av-stats--on-dark">
      <div class="av-stat"><div class="av-stat__num">+70%</div><div class="av-stat__label">De las exportaciones de avena procesada del país</div></div>
      <div class="av-stat"><div class="av-stat__num">97.000 ha</div><div class="av-stat__label">Cultivadas a nivel nacional (2025)</div></div>
      <div class="av-stat"><div class="av-stat__num">203.000 t</div><div class="av-stat__label">Exportadas al año</div></div>
      <div class="av-stat"><div class="av-stat__num">+500</div><div class="av-stat__label">Empleos directos y 2.000 indirectos</div></div>
    </div>
    <p class="av-stat-note av-stat-note--on-dark"><a href="<?= e(url('quienes-somos.php')) ?>" class="av-more av-more--on-dark">Conoce más sobre la industria →</a></p>
  </div>
</section>

<section class="av-container av-py-64">
  <div class="av-eyebrow av-eyebrow--center">Nuestros asociados</div>
  <div class="av-partners">
    <?php foreach ($socios as [$logo, $nombre]): ?>
      <span class="av-partner"><img src="<?= e(url($logo)) ?>" alt="<?= e($nombre) ?>" class="av-partner__img" loading="lazy"></span>
    <?php endforeach; ?>
    <span class="av-partner av-partner--text">Carozzi</span>
  </div>
  <div class="av-center av-mt-22">
    <a href="<?= e(url('directorio.php')) ?>" class="av-more av-more--sm">Ver directorio completo de socios →</a>
  </div>
</section>

<section class="av-band--news">
  <div class="av-container av-py-64">
    <div class="av-news-head">
      <h2 class="av-heading av-fs-28">Últimas noticias</h2>
      <a href="<?= e(url('noticias.php')) ?>" class="av-more av-more--sm">Ver todas →</a>
    </div>
    <div class="av-grid-3">
      <?php foreach ($ultimas as $n): ?>
        <a href="<?= e(url('noticia.php?id=' . (int)$n['id'])) ?>" class="av-news-link">
          <img src="<?= e(portada($n['cover_url'])) ?>" alt="<?= e($n['title']) ?>" class="av-news-img av-news-img--170">
          <div class="av-news-date av-mb-6"><?= e(fecha_es($n['published_at'])) ?></div>
          <div class="av-news-title av-news-title--18"><?= e($n['title']) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<script>
// Aparición progresiva de los objetivos al entrar en pantalla.
// Sustituye al IntersectionObserver que antes vivía dentro del componente React.
(function () {
  var grid = document.querySelector('.js-reveal');
  if (!grid) return;
  var cells = grid.querySelectorAll('.av-reveal');
  if (!('IntersectionObserver' in window)) {
    cells.forEach(function (c) { c.classList.add('av-reveal--on'); });
    return;
  }
  var io = new IntersectionObserver(function (entries) {
    if (entries[0].isIntersecting) {
      cells.forEach(function (c) { c.classList.add('av-reveal--on'); });
      io.disconnect();
    }
  }, { threshold: 0.2 });
  io.observe(grid);
})();
</script>

<?php require __DIR__ . '/inc/footer.php'; ?>
