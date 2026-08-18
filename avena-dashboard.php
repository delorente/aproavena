<?php
declare(strict_types=1);

// --- Entrega de los archivos de datos ---------------------------------------
// Se resuelve antes que nada: no necesita sesión ni base de datos, y así las
// diez descargas que el navegador lanza en paralelo no compiten por el bloqueo
// del archivo de sesión.
if (isset($_GET['avena_xlsx']) || ($_SERVER['PATH_INFO'] ?? '') === '/xlsx') {
    require __DIR__ . '/inc/datos-dashboard.php';
    exit;
}

require __DIR__ . '/inc/bootstrap.php';

/** URL ya escapada del archivo de datos, para meterla en un atributo. */
function dato(string $archivo): string
{
    return e(url('avena-dashboard.php?avena_xlsx=1&f=' . rawurlencode($archivo)));
}

$pageTitle = 'Gráficos de la industria — Aproavena';
$activeNav = 'graficos';
$pageDesc  = 'Producción, rendimiento, superficie, precios, consumo y comercio exterior de la avena, con datos internacionales comparables.';

// Los gráficos los dibuja Google Charts y las planillas las lee SheetJS.
// Van aquí y no en el header común porque ninguna otra página los necesita.
$pageHead = <<<'HTML'
<link rel="stylesheet" href="__CSS__">
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="__JS__" defer></script>
HTML;

$pageHead = str_replace(
    ['__CSS__', '__JS__'],
    [e(url('assets/produccion_css.css')), e(url('assets/dashboards.js'))],
    $pageHead
);

require __DIR__ . '/inc/header.php';
?>

<section class="av-container av-pt-64 av-pb-24">
  <div class="av-eyebrow av-eyebrow--wide">Datos de la industria</div>
  <h1 class="av-heading av-fs-34 av-mb-16">Gráficos de producción, precios y comercio de la avena</h1>
  <p class="av-text av-mw-68ch">Series internacionales de producción, rendimiento, superficie, precios a productor, consumo, costos de exportación y stocks. Elige el año y los países que quieras comparar en cada gráfico.</p>
</section>

  <div class="avena-dashboard-wrap">

    <section class="dashboard"
      data-xlsx="<?= dato('produccion.xlsx') ?>"
      data-bar-title="Ranking de países según producción (Ton)"
      data-geo-title="Producción por Países (Ton)"
      data-y-label="Producción (Ton)"
    >
      <h2 class="dash-title">Producción</h2>
    
      <div class="grid">
        <div class="card chart-block" data-chart="bar">
          <h3 class="js-title"></h3>
          <div class="controls">
            <div class="year-controls">
              <span class="label">Año:</span>
              <div class="year-buttons js-yearButtons"></div>
            </div>
          </div>
    
          <button type="button" class="toggle-btn js-toggleCountries">Elegir países</button>
          <div class="country-compare-panel hidden js-countryPanel">
            <div class="country-compare">
              <input class="js-countrySearch" type="text" placeholder="Buscar país..." />
              <div class="country-list js-countryList"></div>
              <div class="compare-actions">
                <button type="button" class="js-btnSelectAll">Seleccionar todos</button>
                <button type="button" class="js-btnClearAll">Limpiar</button>
                <span class="muted js-countrySelectedInfo"></span>
              </div>
            </div>
          </div>
          <div class="js-chart" style="width:100%;height:500px"></div>
        </div>
    
        <div class="card chart-block" data-chart="geo">
          <h3 class="js-title"></h3>
          <div class="controls">
            <div class="year-controls">
              <span class="label">Año:</span>
              <div class="year-buttons js-yearButtons"></div>
            </div>
          </div>
    
          <button type="button" class="toggle-btn js-toggleCountries">Elegir países</button>
          <div class="country-compare-panel hidden js-countryPanel">
            <div class="country-compare">
              <input class="js-countrySearch" type="text" placeholder="Buscar país..." />
              <div class="country-list js-countryList"></div>
              <div class="compare-actions">
                <button type="button" class="js-btnSelectAll">Seleccionar todos</button>
                <button type="button" class="js-btnClearAll">Limpiar</button>
                <span class="muted js-countrySelectedInfo"></span>
              </div>
            </div>
          </div>
          <div class="js-chart" style="width:100%;height:500px"></div>
        </div>
      </div>
    </section>
    
    <hr>


      <section class="dashboard"
      data-xlsx="<?= dato('rendimiento.xlsx') ?>"
      data-bar-title="Ranking de países según rendimiento (ton/ha)"
      data-geo-title="Rendimiento por Países (ton/ha)"
      data-y-label="Rendimiento"
    >
      <h2 class="dash-title">Rendimiento</h2>
    
      <div class="grid">
        <div class="card chart-block" data-chart="bar">
          <h3 class="js-title"></h3>
          <div class="controls">
            <div class="year-controls">
              <span class="label">Año:</span>
              <div class="year-buttons js-yearButtons"></div>
            </div>
          </div>
    
          <button type="button" class="toggle-btn js-toggleCountries">Elegir países</button>
          <div class="country-compare-panel hidden js-countryPanel">
            <div class="country-compare">
              <input class="js-countrySearch" type="text" placeholder="Buscar país..." />
              <div class="country-list js-countryList"></div>
              <div class="compare-actions">
                <button type="button" class="js-btnSelectAll">Seleccionar todos</button>
                <button type="button" class="js-btnClearAll">Limpiar</button>
                <span class="muted js-countrySelectedInfo"></span>
              </div>
            </div>
          </div>
          <div class="js-chart" style="width:100%;height:500px"></div>
        </div>
    
        <div class="card chart-block" data-chart="geo">
          <h3 class="js-title"></h3>
          <div class="controls">
            <div class="year-controls">
              <span class="label">Año:</span>
              <div class="year-buttons js-yearButtons"></div>
            </div>
          </div>
    
          <button type="button" class="toggle-btn js-toggleCountries">Elegir países</button>
          <div class="country-compare-panel hidden js-countryPanel">
            <div class="country-compare">
              <input class="js-countrySearch" type="text" placeholder="Buscar país..." />
              <div class="country-list js-countryList"></div>
              <div class="compare-actions">
                <button type="button" class="js-btnSelectAll">Seleccionar todos</button>
                <button type="button" class="js-btnClearAll">Limpiar</button>
                <span class="muted js-countrySelectedInfo"></span>
              </div>
            </div>
          </div>
          <div class="js-chart" style="width:100%;height:500px"></div>
        </div>
      </div>
    </section>

  <hr>

  <section class="dashboard"
      data-xlsx="<?= dato('superficie.xlsx') ?>"
      data-bar-title="Ranking de países según superficie (ha)"
      data-geo-title="Superficie por Países (ha)"
      data-y-label="Superficie"
    >
      <h2 class="dash-title">Superficie</h2>
    
      <div class="grid">
        <div class="card chart-block" data-chart="bar">
          <h3 class="js-title"></h3>
          <div class="controls">
            <div class="year-controls">
              <span class="label">Año:</span>
              <div class="year-buttons js-yearButtons"></div>
            </div>
          </div>
    
          <button type="button" class="toggle-btn js-toggleCountries">Elegir países</button>
          <div class="country-compare-panel hidden js-countryPanel">
            <div class="country-compare">
              <input class="js-countrySearch" type="text" placeholder="Buscar país..." />
              <div class="country-list js-countryList"></div>
              <div class="compare-actions">
                <button type="button" class="js-btnSelectAll">Seleccionar todos</button>
                <button type="button" class="js-btnClearAll">Limpiar</button>
                <span class="muted js-countrySelectedInfo"></span>
              </div>
            </div>
          </div>
          <div class="js-chart" style="width:100%;height:500px"></div>
        </div>
    
        <div class="card chart-block" data-chart="geo">
          <h3 class="js-title"></h3>
          <div class="controls">
            <div class="year-controls">
              <span class="label">Año:</span>
              <div class="year-buttons js-yearButtons"></div>
            </div>
          </div>
    
          <button type="button" class="toggle-btn js-toggleCountries">Elegir países</button>
          <div class="country-compare-panel hidden js-countryPanel">
            <div class="country-compare">
              <input class="js-countrySearch" type="text" placeholder="Buscar país..." />
              <div class="country-list js-countryList"></div>
              <div class="compare-actions">
                <button type="button" class="js-btnSelectAll">Seleccionar todos</button>
                <button type="button" class="js-btnClearAll">Limpiar</button>
                <span class="muted js-countrySelectedInfo"></span>
              </div>
            </div>
          </div>
          <div class="js-chart" style="width:100%;height:500px"></div>
        </div>
      </div>
    </section>
  <hr>

  <section class="single-chart"
    data-kind="line_marketing"
    data-xlsx="<?= dato('precios_a_productor_USA.xlsx') ?>"
    data-title="Prices al productor USA"
    data-year-col="Marketing"
    data-y-col="Annual"
    data-min-year="1999"
    >
    <h2 class="dash-title">Precio (USA) - Serie anual (USD/Bushel; 1 bushel = 14,51 kg)</h2>

    <div class="controls" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">

        <div class="year-controls">
        <span class="label">Desde:</span>
        <div class="year-buttons js-yearButtons"></div>
        </div>
    </div>

    <div class="card">
        <div class="js-chart" style="width:100%;height:450px"></div>
    </div>
    </section>

  <hr>

  <section class="single-chart"
    data-kind="line"
    data-xlsx="<?= dato('precios_a_productor_CA.xlsx') ?>"
    data-title="Precios al productor Canadá"
    data-x-col="Reference Date"
    data-y-col="VALUE"
    data-min-year="1999"
    data-x-parse="ym"
    data-aggregate="avg">
    <h2 class="dash-title">Precio (CA) - Serie anual (USD/tonelada métrica)</h2>
    <div class="controls">
        <span class="label">Desde:</span>
        <div class="js-yearButtons year-buttons"></div>
    </div>
    <div class="js-chart" style="height:420px;"></div>
    </section>

  <hr>

  <section class="single-chart"
    data-kind="bar_simple"
    data-xlsx="<?= dato('consumo_34.xlsx') ?>"
    data-title="Consumo doméstico (Ranking países)"
    data-country-col="Country"
    data-value-col="Value"
    data-top-n="34"
    >
    <h2 class="dash-title">Consumo doméstico (Ranking países)</h2>

    <div class="controls" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
        <button type="button" class="toggle-btn js-toggleCountries">Elegir países</button>

        <div class="country-compare-panel hidden js-countryPanel">
        <input class="js-countrySearch" type="text" placeholder="Buscar país..." />
        <div class="country-list js-countryList"></div>

        <div class="compare-actions">
            <button type="button" class="js-btnSelectAll">Seleccionar todos</button>
            <button type="button" class="js-btnClearAll">Limpiar</button>
            <span class="muted js-countrySelectedInfo"></span>
        </div>
        </div>
    </div>

    <div class="card">
        <div class="js-chart" style="width:100%;height:450px"></div>
    </div>
    </section>

  <hr>

  <section class="single-chart"
    data-kind="stack_costs"
    data-xlsx="<?= dato('polinomio_costos.xlsx') ?>"
    data-title="Polinomio de Costos (USD/ton)"
  >
    <h2 class="dash-title">Polinomio de Costos</h2>

    <div class="controls" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
      <div>
        <span class="label">Producto (código):</span>
        <select class="js-prod"></select>
      </div>

      <div>
        <span class="label">Escenario arancelario:</span>
        <select class="js-scn"></select>
      </div>

      <div>
        <span class="label">País:</span>
        <select class="js-country"></select>
      </div>

      <div>
        <span class="label">Desagregación:</span>
        <select class="js-level">
          <option value="total">Costo total</option>
          <option value="detalle_flete">Detalle flete</option>
        </select>
      </div>
    </div>

    <div class="card">
      <div class="js-chart" style="width:100%;height:520px"></div>
    </div>
  </section>

  <hr>

  <section class="single-chart"
    data-kind="trade_json_toggle"
    data-export-json="<?= dato('exportaciones_resumidas.json') ?>"
    data-import-json="<?= dato('importaciones_resumidas.json') ?>"
    data-title="Comercio exterior"
    data-min-year="2000"
    data-top-n="10"
    data-code-default="1004"
  >
    <h2 class="dash-title">Exportaciones / Importaciones</h2>

    <div class="controls" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
      <div>
        <span class="label">Tipo:</span>
        <button type="button" class="toggle-btn js-tradeToggle">Ver Importaciones</button>
      </div>

      <div>
        <span class="label">Código:</span>
        <select class="js-codeSelect"></select>
      </div>

      <div class="year-controls">
        <span class="label">Año:</span>
        <div class="js-yearButtons year-buttons"></div>
      </div>

      <button type="button" class="toggle-btn js-toggleCountries">Elegir países</button>

      <div class="country-compare-panel hidden js-countryPanel">
        <input class="js-countrySearch" type="text" placeholder="Buscar país..." />
        <div class="country-list js-countryList"></div>

        <div class="compare-actions">
          <button type="button" class="js-btnSelectAll">Seleccionar todos</button>
          <button type="button" class="js-btnClearAll">Limpiar</button>
          <span class="muted js-countrySelectedInfo"></span>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="js-chart" style="width:100%;height:450px"></div>
    </div>
  </section>

  <hr>

  <section class="single-chart"
    data-kind="us_stocks_sankey"
    data-xlsx="<?= dato('eeuu_stocks.xlsx') ?>"
    data-title="EEUU Stocks (millones de toneladas)"
    data-default-quarter="MY June-May"
  >
    <h2 class="dash-title">EEUU Stocks (Composición del stock, millones de toneladas)</h2>

    <div class="controls" style="display:flex;gap:16px;flex-wrap:wrap;">
      <div>
        <span class="label">Año comercial:</span>
        <select class="js-yearSelect"></select>
      </div>

      <div>
        <span class="label">Periodo:</span>
        <select class="js-quarterSelect"></select>
      </div>
    </div>

    <div class="card">
      <div class="js-chart" style="width:100%;height:520px"></div>
    </div>
  </section>


  </div>

<?php require __DIR__ . '/inc/footer.php'; ?>
