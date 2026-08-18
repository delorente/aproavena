/* ==========================================================================
   Aproavena — Menú de navegación en móvil
   --------------------------------------------------------------------------
   Bajo el punto de corte del CSS la lista de enlaces se oculta y aparece el
   botón hamburguesa. Este archivo solo alterna la clase is-open y mantiene
   aria-expanded en sincronía; el aspecto lo pone styles.css.

   El ancho del corte no se repite aquí a propósito: se deduce de si el botón
   está visible, así el número vive en un solo sitio (la media query).

   Lo cargan tanto inc/header.php (sitio) como inc/admin-header.php (panel):
   los dos usan el mismo componente .av-nav.
   ========================================================================== */
(function () {
  var btn  = document.querySelector('.av-nav__toggle');
  var menu = document.getElementById('av-nav-links');
  if (!btn || !menu) return;

  function setOpen(abierto) {
    menu.classList.toggle('is-open', abierto);
    btn.setAttribute('aria-expanded', abierto ? 'true' : 'false');
  }

  btn.addEventListener('click', function () {
    setOpen(!menu.classList.contains('is-open'));
  });

  // Al tocar un enlace el menú estorba: se cierra antes de navegar.
  menu.addEventListener('click', function (ev) {
    if (ev.target.closest('a')) setOpen(false);
  });

  document.addEventListener('keydown', function (ev) {
    if (ev.key === 'Escape' && menu.classList.contains('is-open')) {
      setOpen(false);
      btn.focus();
    }
  });

  // Al girar el teléfono o pasar a escritorio el menú vuelve a ser una barra:
  // si se quedara la clase, reaparecería desplegado al volver a móvil.
  // Que el botón esté oculto es justo la señal de que el CSS volvió a modo
  // escritorio, sin tener que conocer el ancho del corte.
  window.addEventListener('resize', function () {
    if (getComputedStyle(btn).display === 'none') setOpen(false);
  });
})();
