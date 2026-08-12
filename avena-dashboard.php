<?php

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

function esc_attr($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function avena_data_url(string $fname): string {
    return 'avena-dashboard.php?avena_xlsx=1&f=' . rawurlencode($fname);
}

$pathInfo = $_SERVER['PATH_INFO'] ?? '';
$isDataRequest = isset($_GET['avena_xlsx']) || ($pathInfo === '/xlsx');

if ($isDataRequest) {
    $f = isset($_GET['f']) ? basename((string)$_GET['f']) : '';

    if ($f === '') {
        http_response_code(400);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Falta parametro f';
        exit;
    }

    $allowed = [
        'produccion.xlsx',
        'rendimiento.xlsx',
        'superficie.xlsx',
        'precios_a_productor_USA.xlsx',
        'precios_a_productor_CA.xlsx',
        'consumo_34.xlsx',
        'polinomio_costos.xlsx',
        'eeuu_stocks.xlsx',
        'exportaciones_resumidas.json',
        'importaciones_resumidas.json',
    ];

    if (!in_array($f, $allowed, true)) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Archivo no permitido';
        exit;
    }

    $path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $f;

    if (!is_file($path)) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Archivo no encontrado: ' . esc_attr($f);
        exit;
    }

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if ($ext === 'xlsx') {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    } elseif ($ext === 'json') {
        header('Content-Type: application/json; charset=utf-8');
    } else {
        header('Content-Type: application/octet-stream');
    }

    header('Content-Length: ' . filesize($path));
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('X-Content-Type-Options: nosniff');
    header('Content-Disposition: inline; filename="' . basename($f) . '"');

    readfile($path);
    exit;
}

$u = function ($fname) {
    return avena_data_url($fname);
};
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Avena Dashboard</title>

  <link rel="stylesheet" href="assets/styles.css">
  <link rel="stylesheet" href="assets/produccion_css.css">

  <script src="https://www.gstatic.com/charts/loader.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

  <script>
    window.AVENA_DASH = {
      apiBase: "avena-dashboard.php",
      nonce: ""
    };
  </script>

  <script src="assets/dashboards.js" defer></script>
</head>
<body>

  <div class="avena-dashboard-wrap">

    <section class="dashboard"
      data-xlsx="<?php echo esc_attr($u('produccion.xlsx')); ?>"
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
      data-xlsx="<?php echo esc_attr($u('rendimiento.xlsx')); ?>"
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
      data-xlsx="<?php echo esc_attr($u('superficie.xlsx')); ?>"
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
    data-xlsx="<?php echo esc_attr($u('precios_a_productor_USA.xlsx')); ?>"
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
    data-xlsx="<?php echo esc_attr($u('precios_a_productor_CA.xlsx')); ?>"
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
    data-xlsx="<?php echo esc_attr($u('consumo_34.xlsx')); ?>"
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
    data-xlsx="<?php echo esc_attr($u('polinomio_costos.xlsx')); ?>"
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
    data-export-json="<?php echo esc_attr($u('exportaciones_resumidas.json')); ?>"
    data-import-json="<?php echo esc_attr($u('importaciones_resumidas.json')); ?>"
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
    data-xlsx="<?php echo esc_attr($u('eeuu_stocks.xlsx')); ?>"
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

</body>
</html>
