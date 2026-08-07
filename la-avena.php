<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';

$nutrientes = [
    ['Energía', '389 kcal', '19%'],
    ['Proteínas', '16,9 g', '34%'],
    ['Grasas totales', '6,9 g', '—'],
    ['Ácidos grasos insaturados', '65% del total', '—'],
    ['Hidratos de carbono', '66 g', '—'],
    ['Fibra dietaria total', '10,6 g', '42%'],
    ['Betaglucanos (fibra soluble)', '3–4 g', '—'],
    ['Hierro', '4,7 mg', '26%'],
    ['Magnesio', '177 mg', '45%'],
    ['Zinc', '3,9 mg', '36%'],
    ['Fósforo', '523 mg', '52%'],
    ['Calcio', '54 mg', '5%'],
    ['Vitaminas del complejo B (B1, B5, B6)', 'Aporte significativo', '—'],
    ['Vitamina E', '0,4 mg', '—'],
];

$propiedades = [
    ['Vitaminas y minerales', 'Destaca por su alto contenido en hierro, magnesio, selenio, calcio, zinc, fósforo, vitamina E y vitaminas del complejo B, esenciales para el metabolismo, la energía y la función inmunológica.'],
    ['Grasas saludables', 'Contiene principalmente grasas insaturadas y ácido linoleico, beneficiosos para la salud cardiovascular. Cerca del 65% de sus lípidos son grasas buenas.'],
    ['Hidratos de carbono y fibra', 'Aporta energía sostenida gracias a carbohidratos de absorción lenta. Su fibra soluble (betaglucanos) ayuda a la saciedad, el tránsito intestinal y a estabilizar el azúcar en sangre.'],
    ['Proteínas de alta calidad', 'Sus proteínas vegetales, combinadas con lácteos o legumbres, alcanzan un valor biológico similar al de la carne o los huevos.'],
    ['Aminoácidos esenciales', 'Aporta seis de los ocho aminoácidos esenciales, que contribuyen al crecimiento y la reparación de tejidos y estimulan la función hepática.'],
];

$usos = [
    ['Consumo humano', 'Copos, harina, granola, barras energéticas, bebidas vegetales y productos de panadería.'],
    ['Uso industrial', 'Ingrediente para el desarrollo de alimentos funcionales y fortificados.'],
    ['Uso pecuario', 'Excelente fuente de energía y proteínas en la alimentación animal.'],
    ['Cosmética natural', 'Base de productos calmantes e hidratantes para piel sensible.'],
];

$pageTitle = 'La Avena — Aproavena';
$activeNav = 'avena';
$pageDesc  = 'Del cultivo al grano procesado: composición nutricional, propiedades, beneficios y usos de la avena chilena.';
require __DIR__ . '/inc/header.php';
?>

<section class="av-hero av-hero--300">
  <img src="<?= e(url('assets/foto-espigas-avena.webp')) ?>" alt="Espigas de avena maduras" class="av-hero__img">
  <div class="av-hero__overlay av-hero__overlay--50"></div>
  <div class="av-hero__inner">
    <div class="av-eyebrow av-eyebrow--on-dark">La Avena</div>
    <h1 class="av-hero-title av-fs-38">Del cultivo al grano procesado</h1>
  </div>
</section>

<section class="av-container av-py-72 av-split">
  <div>
    <div class="av-eyebrow">Cultivo</div>
    <h2 class="av-heading av-fs-27 av-mb-16">Un cereal adaptado al sur de Chile</h2>
    <p class="av-text av-mb-14">La avena se cultiva principalmente en las regiones del centro-sur del país, en suelos y climas que favorecen un grano de alta calidad nutricional. Aproavena trabaja para propiciar su cultivo en todo el territorio nacional, apoyando a productores y fortaleciendo la cadena agrícola.</p>
    <p class="av-text">El seguimiento técnico de la siembra y cosecha es clave para asegurar un abastecimiento estable a la industria procesadora asociada.</p>
  </div>
  <img src="<?= e(url('assets/foto-campo-agricultor.webp')) ?>" alt="Agricultor en campo de avena" class="av-figure av-figure--340">
</section>

<section class="av-band">
  <div class="av-container av-py-72 av-split">
    <img src="<?= e(url('assets/foto-procesamiento-grano.webp')) ?>" alt="Selección y procesamiento del grano de avena" class="av-figure av-figure--340">
    <div>
      <div class="av-eyebrow">Procesamiento</div>
      <h2 class="av-heading av-fs-27 av-mb-16">Estándares de calidad e inocuidad</h2>
      <p class="av-text av-mb-14">Una vez cosechada, la avena pasa por procesos de limpieza, descascarado, laminado y otros tratamientos industriales que la transforman en los productos que llegan a la mesa de los chilenos: copos, harinas, avena instantánea y más.</p>
      <p class="av-text">Nuestros asociados trabajan permanentemente en fortalecer los atributos de calidad e inocuidad de estos productos, en línea con los objetivos de la Asociación.</p>
    </div>
  </div>
</section>

<section class="av-container av-py-72 av-center">
  <h2 class="av-heading av-fs-26 av-mb-16">¿Por qué consumir avena?</h2>
  <p class="av-text av-mw-62ch av-mx-auto av-mb-32">La avena es reconocida por su aporte de fibra, energía y proteína vegetal. Difundir su consumo es uno de los objetivos permanentes de Aproavena, en beneficio de la alimentación de las familias chilenas.</p>
  <img src="<?= e(url('assets/foto-grano-manos.webp')) ?>" alt="Grano de avena en manos" class="av-figure av-figure--mh380">
</section>

<section class="av-band">
  <div class="av-container av-container--900 av-py-72 av-center">
    <div class="av-eyebrow av-eyebrow--center">Un cereal completo y versátil</div>
    <h2 class="av-heading av-fs-30 av-mb-20">La avena, uno de los cereales más completos y equilibrados</h2>
    <p class="av-text av-mw-68ch av-mx-auto">Su alto valor nutricional, versatilidad y beneficios para la salud la han convertido en un alimento clave tanto para el consumo humano como para la producción agropecuaria sustentable. En Chile, su cultivo aporta rotación de suelos, eficiencia hídrica y sostenibilidad, siendo un componente clave de la agricultura regenerativa.</p>
  </div>
</section>

<section class="av-container av-py-72">
  <div class="av-eyebrow av-eyebrow--center">Composición nutricional</div>
  <h2 class="av-heading av-fs-27 av-center av-mb-36">Por cada 100 g de avena cruda</h2>
  <div class="av-ntable-wrap av-mw-780 av-mx-auto">
    <table class="av-ntable">
      <thead>
        <tr><th>Nutriente</th><th>Cantidad</th><th>% Valor Diario*</th></tr>
      </thead>
      <tbody>
        <?php foreach ($nutrientes as [$nombre, $cantidad, $vd]): ?>
          <tr><td><?= e($nombre) ?></td><td><?= e($cantidad) ?></td><td><?= e($vd) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p class="av-table-note">*Porcentaje calculado sobre una dieta de 2.000 kcal diarias. Fuente: USDA FoodData Central / FAO.</p>
  </div>
</section>

<section class="av-band">
  <div class="av-container av-py-72">
    <div class="av-eyebrow av-eyebrow--center">Propiedades nutricionales</div>
    <h2 class="av-heading av-fs-27 av-center av-mb-36">Qué aporta cada porción</h2>
    <div class="av-grid-3">
      <?php foreach ($propiedades as [$titulo, $texto]): ?>
        <div class="av-prop-card">
          <h3 class="av-prop-card__title"><?= e($titulo) ?></h3>
          <p class="av-text"><?= e($texto) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="av-container av-py-72">
  <div class="av-eyebrow av-eyebrow--center">Beneficios para la salud</div>
  <h2 class="av-heading av-fs-27 av-center av-mb-36">Por qué la avena hace bien</h2>
  <div class="av-benefits av-mw-780 av-mx-auto">
    <p class="av-benefit"><strong>Controla el colesterol</strong>, gracias a la fibra soluble que reduce el colesterol LDL (“malo”).</p>
    <p class="av-benefit"><strong>Regula el azúcar en sangre</strong>, evitando alzas bruscas de glucosa.</p>
    <p class="av-benefit"><strong>Favorece la concentración y el rendimiento</strong>, al liberar energía de forma gradual.</p>
    <p class="av-benefit"><strong>Ayuda a controlar el peso</strong>, promoviendo saciedad y evitando picoteos.</p>
    <p class="av-benefit"><strong>Favorece la digestión</strong>, previniendo el estreñimiento.</p>
    <p class="av-benefit"><strong>Actúa como antioxidante natural</strong>, por sus avenantramidas, compuestos exclusivos de la avena con efecto antiinflamatorio y vasodilatador.</p>
  </div>
</section>

<section class="av-band">
  <div class="av-container av-py-72">
    <div class="av-eyebrow av-eyebrow--center">Usos y aplicaciones</div>
    <h2 class="av-heading av-fs-27 av-center av-mb-36">De la mesa a la industria</h2>
    <div class="av-grid-2">
      <?php foreach ($usos as [$titulo, $texto]): ?>
        <div class="av-prop-card">
          <h3 class="av-prop-card__title"><?= e($titulo) ?></h3>
          <p class="av-text"><?= e($texto) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="av-container av-container--900 av-py-72">
  <div class="av-eyebrow av-eyebrow--center">Datos curiosos</div>
  <h2 class="av-heading av-fs-27 av-center av-mb-36">Lo que quizás no sabías de la avena</h2>
  <div class="av-benefits">
    <p class="av-benefit">Los betaglucanos de la avena están reconocidos por la EFSA (Autoridad Europea de Seguridad Alimentaria) por su efecto reductor del colesterol.</p>
    <p class="av-benefit">Un desayuno con avena aporta más del 20% de la fibra diaria recomendada.</p>
    <p class="av-benefit">Hoy la avena es uno de los cereales más demandados en el desarrollo de productos saludables y <em>plant-based</em>.</p>
    <p class="av-benefit">Su cultivo aporta rotación de suelos, eficiencia hídrica y sostenibilidad a la agricultura regenerativa.</p>
  </div>
  <p class="av-table-note av-center av-mt-22">Fuentes: USDA FoodData Central (2024) · FAO — Nutrient Database · MINSAL — Guías Alimentarias para la Población Chilena · European Food Safety Authority (EFSA).</p>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
