<?php declare(strict_types=1); ?>
<footer class="av-footer">
  <div class="av-footer__inner">
    <div>
      <div class="av-footer__brand">
        <img src="<?= e(url('assets/logo-aproavena.png')) ?>" alt="Aproavena" class="av-footer__logo">
        <span class="av-footer__brand-text">Aproavena</span>
      </div>
      <p class="av-footer__desc">Asociación de Procesadores de Avena de Chile A.G.</p>
    </div>
    <div class="av-footer__col">
      <div class="av-footer__heading">Navegación</div>
      <div class="av-footer__list">
        <a href="<?= e(url('quienes-somos.php')) ?>" class="av-footer__link">Quiénes somos</a>
        <a href="<?= e(url('la-avena.php')) ?>" class="av-footer__link">La Avena</a>
        <a href="<?= e(url('directorio.php')) ?>" class="av-footer__link">Directorio y socios</a>
        <a href="<?= e(url('noticias.php')) ?>" class="av-footer__link">Noticias</a>
      </div>
    </div>
    <div class="av-footer__col">
      <div class="av-footer__heading">Contacto</div>
      <div class="av-footer__list av-footer__list--contact">
        <span>contacto@aproavena.cl</span>
        <span>Chillán, Región de Ñuble, Chile</span>
        <a href="<?= e(url('contacto.php')) ?>" class="av-footer__cta">Escríbenos →</a>
        <a href="https://www.instagram.com/aproavena/" target="_blank" rel="noopener" class="av-footer__ig">
          <span class="av-ig-icon">
            <span class="av-ig-icon__lens"></span>
            <span class="av-ig-icon__dot"></span>
          </span>
          Instagram
        </a>
      </div>
    </div>
  </div>
  <div class="av-footer__copy">© <?= date('Y') ?> Aproavena — Asociación de Procesadores de Avena de Chile A.G.</div>
</footer>

</body>
</html>
