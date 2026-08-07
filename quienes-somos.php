<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';

$objetivos = [
    'Informar a las autoridades sobre problemas y necesidades de sus asociados.',
    'Representar los intereses de sus asociados frente a todo tipo de organismos públicos y privados relacionados con la actividad de la asociación gremial.',
    'Propiciar el cultivo de avena en todo el país.',
    'Fortalecer y potenciar los atributos de calidad e inocuidad de los productos elaborados de la avena nacional.',
    'Difundir el consumo de avena.',
    'Ocuparse del estudio de problemas comunes de los asociados y cooperar en la solución de dificultades que se puedan presentar a la industria procesadora de avena en general.',
];

$pageTitle = 'Quiénes somos — Aproavena';
$activeNav = 'quienes';
$pageDesc  = 'La voz gremial de los procesadores de avena de Chile: objetivos, marco de acción y cifras de la industria.';
require __DIR__ . '/inc/header.php';
?>

<section class="av-hero av-hero--300">
  <img src="<?= e(url('assets/foto-campo-agricultor.webp')) ?>" alt="Agricultor revisando campo de avena" class="av-hero__img av-hero__img--low">
  <div class="av-hero__overlay av-hero__overlay--55"></div>
  <div class="av-hero__inner">
    <div class="av-eyebrow av-eyebrow--on-dark">Quiénes somos</div>
    <h1 class="av-hero-title av-fs-38">La voz gremial de los procesadores de avena</h1>
  </div>
</section>

<section class="av-container av-container--900 av-py-72">
  <p class="av-lead av-mb-20">La Asociación de Procesadores de Avena tiene dentro de sus objetivos promover el mejoramiento, optimización y desarrollo de las actividades frecuentes y asociadas al rubro del procesamiento industrial de la avena.</p>
  <p class="av-text av-text--16">Velamos por los intereses de todos nuestros asociados, dentro del marco de las leyes vigentes, actuando como interlocutor técnico y gremial frente a autoridades, organismos públicos y privados vinculados a la cadena de la avena en Chile.</p>
</section>

<section class="av-band">
  <div class="av-container av-py-64">
    <h2 class="av-heading av-fs-28 av-mb-36">Objetivos de la Asociación</h2>
    <div class="av-grid-2">
      <?php foreach ($objetivos as $i => $texto): ?>
        <div class="av-obj-card">
          <div class="av-obj-num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></div>
          <p class="av-obj-text"><?= e($texto) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="av-container av-py-64 av-split">
  <img src="<?= e(url('assets/foto-grano-manos.webp')) ?>" alt="Grano de avena en manos" class="av-figure av-figure--320">
  <div>
    <div class="av-eyebrow">Marco de acción</div>
    <h2 class="av-heading av-fs-26 av-mb-16">Una asociación gremial al servicio de sus socios</h2>
    <p class="av-text">Nos ocupamos del estudio de los problemas comunes de nuestros asociados y cooperamos en la solución de las dificultades que se puedan presentar a la industria procesadora de avena en general, siempre dentro del marco de las leyes vigentes en Chile.</p>
  </div>
</section>

<section class="av-band--dark">
  <div class="av-container av-py-72">
    <div class="av-eyebrow av-eyebrow--center av-eyebrow--on-dark">La industria de la avena chilena</div>
    <h2 class="av-heading av-heading--on-image av-fs-28 av-center av-mb-40">Un gremio que integra a los principales procesadores del país</h2>
    <div class="av-stats av-stats--on-dark">
      <div class="av-stat"><div class="av-stat__num">2021</div><div class="av-stat__label">Año de fundación de Aproavena</div></div>
      <div class="av-stat"><div class="av-stat__num">+70%</div><div class="av-stat__label">De las exportaciones de avena procesada del país</div></div>
      <div class="av-stat"><div class="av-stat__num">Ñuble<br>a Los Ríos</div><div class="av-stat__label">Presencia territorial de sus asociados</div></div>
      <div class="av-stat"><div class="av-stat__num">+500</div><div class="av-stat__label">Empleos directos y 2.000 indirectos</div></div>
    </div>
  </div>
</section>

<section class="av-container av-py-72">
  <div class="av-eyebrow av-eyebrow--center">Magnitud de la industria (2025)</div>
  <h2 class="av-heading av-fs-27 av-center av-mb-40">La avena chilena en cifras</h2>
  <div class="av-stats">
    <div class="av-stat"><div class="av-stat__num">97.000</div><div class="av-stat__label">Hectáreas cultivadas</div></div>
    <div class="av-stat"><div class="av-stat__num">508.000 t</div><div class="av-stat__label">Producidas al año</div></div>
    <div class="av-stat"><div class="av-stat__num">203.000 t</div><div class="av-stat__label">Exportadas al año</div></div>
    <div class="av-stat"><div class="av-stat__num">95%</div><div class="av-stat__label">De las exportaciones es avena procesada</div></div>
  </div>
  <p class="av-stat-note">Precio de exportación FOB: avena pelada US$ 596,0 / t · avena en hojuela US$ 648,0 / t.</p>
</section>

<section class="av-band">
  <div class="av-container av-container--900 av-py-72">
    <div class="av-eyebrow av-eyebrow--center">Exportaciones por producto</div>
    <h2 class="av-heading av-fs-27 av-center av-mb-40">Qué exporta la industria</h2>
    <div class="av-exp">
      <div>
        <div class="av-exp-head"><span class="av-exp-name">Hojuelas</span><span class="av-exp-val">58,33%</span></div>
        <div class="av-exp-track"><div class="av-exp-fill" style="width: 58.33%;"></div></div>
      </div>
      <div>
        <div class="av-exp-head"><span class="av-exp-name">Avena pelada y harina</span><span class="av-exp-val">37,72%</span></div>
        <div class="av-exp-track"><div class="av-exp-fill" style="width: 37.72%;"></div></div>
      </div>
      <div>
        <div class="av-exp-head"><span class="av-exp-name">Derivados</span><span class="av-exp-val">3,90%</span></div>
        <div class="av-exp-track"><div class="av-exp-fill" style="width: 3.9%;"></div></div>
      </div>
      <div>
        <div class="av-exp-head"><span class="av-exp-name">Barras de avena</span><span class="av-exp-val">0,05%</span></div>
        <div class="av-exp-track"><div class="av-exp-fill" style="width: 0.5%;"></div></div>
      </div>
    </div>
    <p class="av-table-note av-center av-mt-22">La avena procesada representa el 95% del valor exportado por la industria.</p>
  </div>
</section>

<section class="av-container av-container--900 av-py-72">
  <div class="av-eyebrow av-eyebrow--center">Mercados clave</div>
  <h2 class="av-heading av-fs-27 av-center av-mb-36">Destinos de la avena chilena</h2>
  <div class="av-markets">
    <div class="av-market">
      <h3 class="av-market__title">Avena pelada</h3>
      <ul class="av-market__list"><li>Perú</li><li>Guatemala</li><li>Colombia</li></ul>
    </div>
    <div class="av-market">
      <h3 class="av-market__title">Avena en hojuelas</h3>
      <ul class="av-market__list"><li>Colombia</li><li>Perú</li><li>Venezuela</li></ul>
    </div>
  </div>
</section>

<section class="av-band">
  <div class="av-container av-container--900 av-py-72 av-center">
    <div class="av-eyebrow av-eyebrow--center">Mirada al futuro</div>
    <h2 class="av-heading av-fs-27 av-mb-20">Seguimos creando valor para Chile y el mundo</h2>
    <p class="av-text av-mw-68ch av-mx-auto av-mb-32">Integramos cada eslabón de la cadena comercial en una alianza público–privada que impulsa el crecimiento de la industria de la avena.</p>
    <div class="av-grid-3">
      <div class="av-prop-card"><p class="av-text">Impulsar la innovación y el valor agregado en los productos de avena.</p></div>
      <div class="av-prop-card"><p class="av-text">Aumentar las exportaciones y abrir nuevos mercados internacionales.</p></div>
      <div class="av-prop-card"><p class="av-text">Fortalecer la imagen país a través de la avena chilena.</p></div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
