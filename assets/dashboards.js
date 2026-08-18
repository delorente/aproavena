let _resizeTimer = null;

google.charts.load("current", { packages: ["corechart", "geochart", "sankey"] });

// Bloques de tipo Dashboard vivos: se guardan para poder redibujarlos cuando
// cambia el ancho. Google Charts dibuja a un ancho fijo calculado en el
// momento; al girar el teléfono el SVG se quedaba con la medida anterior.
const _chartBlocks = [];

google.charts.setOnLoadCallback(() => {
  document.querySelectorAll("section.dashboard").forEach((sec) => {
    const dash = new Dashboard(sec);
    dash.init().catch(err => console.error("[Dashboard] init error:", sec.dataset.xlsx, err));
  });

  document.querySelectorAll(".single-chart").forEach(sec => initSingleChart(sec));

  // Los .single-chart ya traen su propio listener de resize; estos no.
  window.addEventListener("resize", () => {
    clearTimeout(_resizeTimer);
    _resizeTimer = setTimeout(() => {
      _chartBlocks.forEach(b => { if (b.yearRows.length) b.redraw(); });
    }, 200);
  });
});


/* --- Ayudas para pantallas angostas -----------------------------------------
   Google Charts reparte el ancho disponible entre todas las categorías. Con
   cien países en 350px las barras quedan en un pelo y las etiquetas
   desaparecen. Bajo 700px se dibuja el gráfico más ancho que su caja y lo
   desliza .js-chart, que ya tiene overflow-x:auto para eso.
   En escritorio no cambia nada: estas funciones devuelven el valor de siempre.
   ---------------------------------------------------------------------------- */
const MOVIL_MAX = 700;
const ANCHO_MIN_BARRA = 26;

function esMovil() {
  return window.innerWidth <= MOVIL_MAX;
}

/** Ancho en píxeles para un gráfico de columnas, o null para dejarlo fluido. */
function anchoColumnas(chartEl, nBarras) {
  if (!esMovil()) return null;
  const disponible = chartEl.clientWidth || 0;
  const necesario = nBarras * ANCHO_MIN_BARRA + 90; // 90 ≈ el eje vertical
  return necesario > disponible ? necesario : null;
}

/** El margen izquierdo de las barras horizontales: 180px no caben en un móvil. */
function margenEtiquetas(px) {
  return esMovil() ? Math.min(px, 105) : px;
}

function parseNum(val) {
  if (val == null) return NaN;
  let s = String(val).trim().replace(/[^\d.,-]/g, "");
  if (!s) return NaN;

  if (s.includes(".") && s.includes(",")) {
    s = s.lastIndexOf(",") > s.lastIndexOf(".") ? s.replace(/\./g, "").replace(",", ".") : s.replace(/,/g, "");
  } else if (s.includes(",")) {
    s = /^\d{1,3}(,\d{3})+$/.test(s) ? s.replace(/,/g, "") : s.replace(",", ".");
  }
  const n = Number(s);
  return Number.isFinite(n) ? n : NaN;
}

function escapeHtml(s) {
  return String(s ?? "")
    .replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;").replaceAll("'", "&#039;");
}

async function fetchExcel(xlsxPath, sheetName = null) {
  const res = await fetch(encodeURI(xlsxPath));
  if (!res.ok) throw new Error(`HTTP ${res.status} al cargar ${xlsxPath}`);
  const ab = await res.arrayBuffer();
  const wb = XLSX.read(ab, { type: "array" });
  const name = sheetName ? (wb.SheetNames.find(n => n.toLowerCase().includes(sheetName.toLowerCase())) || wb.SheetNames[0]) : wb.SheetNames[0];
  return wb.Sheets[name];
}

async function fetchExcelObjects(xlsxPath, requiredCols = []) {
  const ws = await fetchExcel(xlsxPath);
  const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: "" });
  const norm = (x) => String(x ?? "").trim().toLowerCase();
  
  const headerIdx = requiredCols.length 
    ? rows.findIndex(r => requiredCols.map(c => c.toLowerCase()).every(c => r.map(norm).includes(c)))
    : rows.findIndex(r => r.some(c => String(c).trim() !== ""));

  if (headerIdx === -1) return [];

  const header = rows[headerIdx].map(h => String(h ?? "").trim());
  return rows.slice(headerIdx + 1).filter(r => r && r.some(c => String(c).trim() !== "")).map(r => {
    const o = {};
    header.forEach((h, j) => { if (h) o[h] = r[j]; });
    return o;
  });
}

async function fetchJSON(jsonPath) {
  const res = await fetch(encodeURI(jsonPath));
  if (!res.ok) throw new Error(`HTTP ${res.status} al cargar ${jsonPath}`);
  return await res.json();
}

function makeYearButtons(container, years, onPick, defaultYear) {
  if (!container) return;
  container.innerHTML = "";
  const setActive = (y) => {
    [...container.querySelectorAll(".year-btn")].forEach(b => b.classList.toggle("active", b.dataset.year === String(y)));
  };
  years.forEach(y => {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "year-btn";
    btn.textContent = y;
    btn.dataset.year = y;
    btn.onclick = () => { setActive(y); onPick(y); };
    container.appendChild(btn);
  });
  if (defaultYear != null) { setActive(defaultYear); onPick(defaultYear); }
}

function populateSelectOptions(sel, values, placeholder) {
  if (!sel) return;
  sel.innerHTML = placeholder ? `<option value="">${placeholder}</option>` : "";
  values.forEach(v => {
    const o = document.createElement("option");
    o.value = v;
    o.textContent = v;
    sel.appendChild(o);
  });
}

class CountryFilter {
  constructor(sec, onUpdate) {
    this.sec = sec;
    this.onUpdate = onUpdate;
    this.selected = new Set();
    this.all = [];
    this.showNone = false;

    this.toggleBtn = sec.querySelector(".js-toggleCountries");
    this.panel = sec.querySelector(".js-countryPanel");
    this.search = sec.querySelector(".js-countrySearch");
    this.list = sec.querySelector(".js-countryList");
    this.info = sec.querySelector(".js-countrySelectedInfo");

    this.initUI();
  }

  initUI() {
    if (this.toggleBtn && this.panel) {
      this.toggleBtn.onclick = () => this.panel.classList.toggle("hidden");
    }
    if (this.search) {
      this.search.oninput = () => this.render();
    }
    const btnAll = this.sec.querySelector(".js-btnSelectAll");
    const btnClear = this.sec.querySelector(".js-btnClearAll");

    if (btnAll) btnAll.onclick = () => { this.showNone = false; this.selected = new Set(this.all); this.render(); this.onUpdate(); };
    if (btnClear) btnClear.onclick = () => { this.selected.clear(); this.showNone = true; this.render(); this.onUpdate(); };
  }

  setCountries(list) {
    this.all = list;
    if (this.selected.size > 0) {
      this.selected = new Set([...this.selected].filter(c => this.all.includes(c)));
    }
    this.render();
  }

  filterRows(rows, getCountryFn = r => r[0]) {
    if (this.showNone) return [];
    if (this.selected.size === 0) return rows;
    return rows.filter(r => this.selected.has(getCountryFn(r)));
  }

  render() {
    if (!this.list) return;
    const term = (this.search?.value || "").toLowerCase();
    this.list.innerHTML = "";

    this.all.filter(c => c.toLowerCase().includes(term)).forEach(country => {
      const label = document.createElement("label");
      label.className = "country-item";
      label.innerHTML = `<input type="checkbox" ${this.selected.has(country) ? "checked" : ""}> <span>${escapeHtml(country)}</span>`;
      
      label.querySelector("input").onchange = (e) => {
        this.showNone = false;
        if (e.target.checked) this.selected.add(country);
        else this.selected.delete(country);
        this.updateInfo();
        this.onUpdate();
      };
      this.list.appendChild(label);
    });
    this.updateInfo();
  }

  updateInfo() {
    const sz = this.selected.size;
    if (this.toggleBtn) this.toggleBtn.textContent = sz === 0 ? "Elegir países (todos)" : `Elegir países (${sz})`;
    if (this.info) this.info.textContent = sz === 0 ? `Mostrando: todos (${this.all.length})` : `Seleccionados: ${sz} / ${this.all.length}`;
  }
}

class Dashboard {
  constructor(root) {
    this.root = root;
    this.xlsxPath = root.dataset.xlsx;
    this.barTitle = root.dataset.barTitle || "";
    this.geoTitle = root.dataset.geoTitle || "";
    this.yLabel = root.dataset.yLabel || "Valor";
    this.raw = [];
    this.years = [];
    this.blocks = [];
  }

  async init() {
    try {
      const data = await fetchExcelObjects(this.xlsxPath, ["Country", "Year", "Value"]);
      this.raw = data.map(r => ({
        Country: this.normalizeCountry(r.Country),
        Year: String(r.Year).trim(),
        Value: parseNum(r.Value)
      })).filter(r => r.Country && r.Year && Number.isFinite(r.Value));

      this.years = [...new Set(this.raw.map(r => r.Year))].sort();

      this.root.querySelectorAll(".chart-block").forEach(el => {
        const block = new ChartBlock(el, this);
        this.blocks.push(block);
        block.init();
      });
    } catch (e) {
      console.error("[Dashboard] error al cargar:", this.xlsxPath, e);
    }
  }

  normalizeCountry(name) {
    const map = {
      "United States of America": "United States", "Russian Federation": "Russia",
      "Iran (Islamic Republic of)": "Iran", "Bolivia (Plurinational State of)": "Bolivia",
      "Viet Nam": "Vietnam", "Turkiye": "Turkey", "Czechia": "Czech Republic",
      "Republic of Korea": "South Korea", "United Kingdom of Great Britain and Northern Ireland": "United Kingdom"
    };
    const clean = String(name || "").normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
    return map[clean] || clean;
  }

  yearRows(year) {
    return this.raw
      .filter(r => r.Year === String(year).trim())
      .map(r => [r.Country, r.Value])
      .sort((a, b) => b[1] - a[1]);
  }
}

class ChartBlock {
  constructor(blockEl, dash) {
    this.el = blockEl;
    this.dash = dash;
    this.type = blockEl.dataset.chart;
    this.currentYear = null;
    this.yearRows = [];
    this.chartEl = blockEl.querySelector(".js-chart");
    this.filter = new CountryFilter(blockEl, () => this.redraw());
    _chartBlocks.push(this);
  }

  init() {
    const titleEl = this.el.querySelector(".js-title");
    if (titleEl) titleEl.textContent = (this.type === "bar") ? this.dash.barTitle : this.dash.geoTitle;

    const yearButtonsEl = this.el.querySelector(".js-yearButtons");
    if (this.dash.years.length > 0) {
      makeYearButtons(yearButtonsEl, this.dash.years, (y) => this.setYear(y), this.dash.years[0]);
    }
  }

  setYear(year) {
    this.currentYear = String(year).trim();
    this.yearRows = this.dash.yearRows(this.currentYear);
    const countries = [...new Set(this.yearRows.map(r => r[0]))].sort((a, b) => a.localeCompare(b));
    this.filter.setCountries(countries);
    this.redraw();
  }

  redraw() {
    const rows = this.filter.filterRows(this.yearRows);
    if (!rows.length) {
      this.chartEl.innerHTML = "<div style='padding:10px;color:#666'>No hay países seleccionados.</div>";
      return;
    }

    const dt = new google.visualization.DataTable();
    dt.addColumn("string", "País");
    dt.addColumn("number", this.dash.yLabel);
    dt.addRows(rows);

    if (this.type === "bar") {
      const opciones = {
        legend: { position: "none" },
        vAxis: { title: this.dash.yLabel },
        hAxis: { title: "País", slantedText: true, slantedTextAngle: 60, textStyle: { fontSize: 9 } },
        chartArea: { left: 70, right: 20, top: 20, bottom: 140, width: "100%", height: "75%" }
      };
      const ancho = anchoColumnas(this.chartEl, rows.length);
      if (ancho) opciones.width = ancho;
      new google.visualization.ColumnChart(this.chartEl).draw(dt, opciones);
    } else {
      new google.visualization.GeoChart(this.chartEl).draw(dt, {
        colorAxis: { colors: ["#ffffff", "#1f4aa8"] },
        datalessRegionColor: "#f2f2f2"
      });
    }
  }
}

const chartHandlers = {
  line: async (sec, chartDiv) => {
    const data = await fetchExcelObjects(sec.dataset.xlsx, [sec.dataset.xCol, sec.dataset.yCol]);
    const minYear = Number(sec.dataset.minYear || -Infinity);
    const xParse = (sec.dataset.xParse || "year").toLowerCase();
    const aggregate = (sec.dataset.aggregate || "").toLowerCase();

    let rawRows = data.map(o => {
      let x = xParse === "ym" ? Number(String(o[sec.dataset.xCol]).match(/(\d{4})/)?.[1]) : Number(o[sec.dataset.xCol]);
      return [x, parseNum(o[sec.dataset.yCol])];
    }).filter(([x, y]) => Number.isFinite(x) && x >= minYear && Number.isFinite(y));

    if (aggregate === "avg" || aggregate === "sum") {
      const map = new Map();
      rawRows.forEach(([x, y]) => {
        const cur = map.get(x) || { sum: 0, count: 0 };
        map.set(x, { sum: cur.sum + y, count: cur.count + 1 });
      });
      rawRows = [...map.entries()].map(([year, b]) => [year, aggregate === "sum" ? b.sum : (b.sum / b.count)]);
    }
    rawRows.sort((a, b) => a[0] - b[0]);
    const years = [...new Set(rawRows.map(r => r[0]))];

    const draw = (minY) => {
      const filtered = rawRows.filter(r => r[0] >= Number(minY));
      const dt = new google.visualization.DataTable();
      dt.addColumn("string", "Año");
      dt.addColumn("number", sec.dataset.title || "Valor");
      dt.addRows(filtered.map(([x, y]) => [String(x), y]));

      new google.visualization.LineChart(chartDiv).draw(dt, {
        legend: { position: "none" }, colors: ["#1f4aa8"],
        chartArea: { left: 70, right: 20, top: 20, bottom: 60, width: "100%", height: "75%" },
        hAxis: { title: "Año" }
      });
    };

    makeYearButtons(sec.querySelector(".js-yearButtons"), years, draw, years[0]);
  },

  bar_simple: async (sec, chartDiv) => {
    const data = await fetchExcelObjects(sec.dataset.xlsx, [sec.dataset.countryCol || "Country", sec.dataset.valueCol || "Value"]);
    const topN = Number(sec.dataset.topN || 0);

    let baseRows = data.map(o => [String(o[sec.dataset.countryCol || "Country"]).trim(), parseNum(o[sec.dataset.valueCol || "Value"])])
      .filter(([c, v]) => c && Number.isFinite(v))
      .sort((a, b) => b[1] - a[1]);

    if (topN > 0) baseRows = baseRows.slice(0, topN);

    const filter = new CountryFilter(sec, () => draw());
    filter.setCountries(baseRows.map(r => r[0]));

    const draw = () => {
      const rows = filter.filterRows(baseRows);
      if (!rows.length) {
        chartDiv.innerHTML = `<div style="padding:10px;color:#666">No hay países para mostrar.</div>`;
        return;
      }
      const dt = new google.visualization.DataTable();
      dt.addColumn("string", "País");
      dt.addColumn("number", sec.dataset.title || "Valor");
      dt.addColumn({ type: "string", role: "annotation" });

      const fmt = (n) => new Intl.NumberFormat("es-CL", { maximumFractionDigits: 0 }).format(n);
      dt.addRows(rows.map(([c, v]) => [c, v, fmt(v)]));

      new google.visualization.BarChart(chartDiv).draw(dt, {
        title: sec.dataset.title || "", legend: { position: "none" }, bars: "horizontal",
        chartArea: { left: margenEtiquetas(180), right: 30, top: 60, bottom: 40, width: "100%", height: "70%" },
        hAxis: { title: "Ton" }
      });
    };
    draw();
    window.addEventListener("resize", draw);
  },

  line_marketing: async (sec, chartDiv) => {
    const ws = await fetchExcel(sec.dataset.xlsx);
    const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: "" });
    const minYear = Number(sec.dataset.minYear || 1999);

    const parsed = [];
    rows.slice(1).forEach(r => {
      const startYear = Number(String(r[1]).match(/(\d{4})/)?.[1]);
      const annual = parseNum(r[2]);
      if (Number.isFinite(startYear) && Number.isFinite(annual) && startYear >= minYear) {
        parsed.push([startYear, annual]);
      }
    });

    const years = [...new Set(parsed.map(r => r[0]))].sort((a,b) => a-b);

    const render = (startY) => {
      const filtered = parsed.filter(r => r[0] >= startY).sort((a,b) => a[0]-b[0]);
      const dt = new google.visualization.DataTable();
      dt.addColumn("number", "Año");
      dt.addColumn("number", sec.dataset.title || "Serie");
      dt.addRows(filtered);

      new google.visualization.LineChart(chartDiv).draw(dt, {
        legend: { position: "none" }, colors: ["#1f4aa8"],
        chartArea: { left: 70, right: 20, top: 20, bottom: 60, width: "100%", height: "75%" },
        hAxis: { title: "Marketing year", format: "####" }
      });
    };

    makeYearButtons(sec.querySelector(".js-yearButtons"), years, render, years[0]);
  },

  trade_json_toggle: async (sec, chartDiv) => {
    const [rawExp, rawImp] = await Promise.all([fetchJSON(sec.dataset.exportJson), fetchJSON(sec.dataset.importJson)]);
    const minYear = Number(sec.dataset.minYear || 2000);
    const topN = Number(sec.dataset.topN || 10);

    // Los JSON de comercio vienen en formato compacto {cols, rows}: una fila
    // por registro, sin repetir los nombres de las claves ni el texto largo de
    // la partida arancelaria, que aqui no se usa. Ver tools/comprimir-comercio.php.
    // Se acepta tambien el formato antiguo (lista de objetos) por si queda algun
    // volcado sin convertir.
    const norm = (data) => {
      const filas = Array.isArray(data)
        ? data.map(o => [o.Year, o["Reported Country"], o["Number Code"], o["Net Weight Sum"]])
        : (data && Array.isArray(data.rows) ? data.rows : []);

      return filas.map(([year, country, code, value]) => ({
        year: Number(year),
        country: String(country ?? "").trim(),
        code: String(code ?? "").trim(),
        value: Number(value ?? 0)
      })).filter(o => Number.isFinite(o.year) && o.year >= minYear && o.country && o.code);
    };

    const expData = norm(rawExp);
    const impData = norm(rawImp);
    const allData = expData.concat(impData);

    const years = [...new Set(allData.map(o => o.year))].sort((a,b) => a-b);
    const codes = [...new Set(allData.map(o => o.code))].sort();

    let mode = "export";
    let curYear = years.includes(2024) ? 2024 : years[0];
    let curCode = codes.includes(sec.dataset.codeDefault) ? sec.dataset.codeDefault : codes[0];

    const selCode = sec.querySelector(".js-codeSelect");
    populateSelectOptions(selCode, codes);
    if (selCode) { selCode.value = curCode; selCode.onchange = () => { curCode = selCode.value; draw(); }; }

    const toggleTradeBtn = sec.querySelector(".js-tradeToggle");
    if (toggleTradeBtn) {
      toggleTradeBtn.onclick = () => {
        mode = mode === "export" ? "import" : "export";
        toggleTradeBtn.textContent = mode === "export" ? "Ver Importaciones" : "Ver Exportaciones";
        draw();
      };
    }

    const filter = new CountryFilter(sec, () => draw());

    const draw = () => {
      const activeData = mode === "export" ? expData : impData;
      let rows = activeData.filter(o => o.year === curYear && o.code === curCode).sort((a, b) => b.value - a.value);

      filter.setCountries([...new Set(rows.map(o => o.country))].sort());
      rows = filter.filterRows(rows, r => r.country).slice(0, topN);

      if (!rows.length) {
        chartDiv.innerHTML = `<div style="padding:10px;color:#666">No hay datos para mostrar.</div>`;
        return;
      }

      const dt = new google.visualization.DataTable();
      dt.addColumn("string", "País");
      dt.addColumn("number", mode === "export" ? "Exportaciones" : "Importaciones");
      dt.addColumn({ type: "string", role: "annotation" });

      const fmt = (n) => new Intl.NumberFormat("es-CL", { maximumFractionDigits: 0 }).format(n);
      dt.addRows(rows.map(o => [o.country, o.value, fmt(o.value)]));

      new google.visualization.BarChart(chartDiv).draw(dt, {
        title: `${mode === "export" ? "Exportaciones" : "Importaciones"} - Año ${curYear} - Código ${curCode}`,
        legend: { position: "none" }, bars: "horizontal",
        chartArea: { left: margenEtiquetas(180), right: 30, top: 60, bottom: 40, width: "100%", height: "70%" },
        hAxis: { title: "Toneladas" }
      });
    };

    makeYearButtons(sec.querySelector(".js-yearButtons"), years, (y) => { curYear = Number(y); draw(); }, curYear);
    window.addEventListener("resize", draw);
  },

  stack_costs: async (sec, chartDiv) => {
    const data = await fetchExcelObjects(sec.dataset.xlsx, ["Producto", "Tipo Arancel", "Pais", "FOB USD/ton"]);
    
    const selProd = sec.querySelector(".js-prod");
    const selScn = sec.querySelector(".js-scn");
    const selCountry = sec.querySelector(".js-country");
    const selLevel = sec.querySelector(".js-level");

    const uniq = (arr) => [...new Set(arr)].filter(Boolean).sort();
    populateSelectOptions(selProd, uniq(data.map(o => String(o.Producto).trim())));
    populateSelectOptions(selScn, uniq(data.map(o => String(o["Tipo Arancel"]).trim())));
    populateSelectOptions(selCountry, uniq(data.map(o => String(o.Pais).trim())), "Todos");

    if (selProd.options.length) selProd.selectedIndex = 0;
    if (selScn.options.length) selScn.selectedIndex = 0;

    const draw = () => {
      const prod = selProd.value, scn = selScn.value, country = selCountry.value, level = selLevel.value || "total";
      const filtered = data.filter(o => 
        (!prod || String(o.Producto).trim() === prod) &&
        (!scn || String(o["Tipo Arancel"]).trim() === scn) &&
        (!country || String(o.Pais).trim() === country)
      );

      if (!filtered.length) {
        chartDiv.innerHTML = `<div style="padding:10px;color:#666">No hay datos para los filtros.</div>`;
        return;
      }

      const dt = new google.visualization.DataTable();
      dt.addColumn("string", "País");

      const categories = level === "total" 
        ? ["FOB USD/ton", "Flete Internacional USD/ton", "Costo Seguro Internacional", "Costo Arancel USD/ton", "Promedio Costos M.A y otros"]
        : ["FOB USD/ton", "Costos portuarios origen", "Flete marítimo internacional", "Costos portuarios destino (EE. UU.)", "Transporte ferroviario", "Costo Seguro Internacional", "Costo Arancel USD/ton", "Promedio Costos M.A y otros"];

      categories.forEach(c => dt.addColumn("number", c));

      filtered.forEach(o => {
        const row = [String(o.Pais).trim()];
        categories.forEach(c => row.push(parseNum(o[c]) || 0));
        dt.addRow(row);
      });

      new google.visualization.ColumnChart(chartDiv).draw(dt, {
        title: sec.dataset.title || "", isStacked: true, legend: { position: "top", maxLines: 3 },
        chartArea: { left: 80, right: 20, top: 60, bottom: 120, width: "100%", height: "70%" },
        hAxis: { title: "País", slantedText: true, slantedTextAngle: 45 },
        vAxis: { title: "USD/ton" }
      });
    };

    [selProd, selScn, selCountry, selLevel].forEach(el => { if (el) el.onchange = draw; });
    draw();
    window.addEventListener("resize", draw);
  },

  us_stocks_sankey: async (sec, chartDiv) => {
    const data = await fetchExcelObjects(sec.dataset.xlsx, ["Marketing year", "Quarter", "Beginning stocks"]);
    
    const years = [...new Set(data.map(o => Number(String(o["Marketing year"]).match(/(\d{4})/)?.[1])))].filter(Boolean).sort((a,b)=>a-b);
    const quarters = [...new Set(data.map(o => String(o.Quarter).trim()))].filter(Boolean);

    const yearSelect = sec.querySelector(".js-yearSelect");
    const quarterSelect = sec.querySelector(".js-quarterSelect");

    populateSelectOptions(yearSelect, years);
    populateSelectOptions(quarterSelect, quarters);

    const update = () => {
      const row = data.find(o => String(o["Marketing year"]).includes(yearSelect.value) && String(o.Quarter).trim() === quarterSelect.value);
      if (!row) {
        chartDiv.innerHTML = `<div style="padding:10px;color:#b00">No hay datos.</div>`;
        return;
      }

      const fmt = new Intl.NumberFormat("es-CL", { minimumFractionDigits: 3, maximumFractionDigits: 3 });
      const dt = new google.visualization.DataTable();
      dt.addColumn("string", "From"); dt.addColumn("string", "To");
      dt.addColumn("number", "Weight"); dt.addColumn({ type: "string", role: "tooltip" });

      const flows = [
        ["Beginning stocks", "Total supply", parseNum(row["Beginning stocks"])],
        ["Production", "Total supply", parseNum(row.Production)],
        ["Imports", "Total supply", parseNum(row.Imports)],
        ["Total supply", "Food, alcohol & industrial use", parseNum(row["Food, alcohol, and industrial use"])],
        ["Total supply", "Seed use", parseNum(row["Seed use"])],
        ["Total supply", "Feed & residual use", parseNum(row["Feed and residual use"])],
        ["Total supply", "Exports", parseNum(row.Exports)],
        ["Total supply", "Ending stocks", parseNum(row["Ending stocks"])]
      ];

      dt.addRows(flows.filter(r => r[2] > 0).map(([f, t, v]) => [f, t, v, `${f} → ${t}: ${fmt.format(v)} MM Ton`]));

      chartDiv.innerHTML = `<div style="font-weight:600;margin-bottom:10px">${escapeHtml(sec.dataset.title || "")} - ${escapeHtml(yearSelect.value)} - ${escapeHtml(quarterSelect.value)}</div><div class="sankey-inner" style="width:100%;height:480px"></div>`;
      new google.visualization.Sankey(chartDiv.querySelector(".sankey-inner")).draw(dt, {
        sankey: { node: { label: { fontSize: 13 } }, link: { colorMode: "source" } }
      });
    };

    if (yearSelect) yearSelect.onchange = update;
    if (quarterSelect) quarterSelect.onchange = update;
    update();
  }
};

async function initSingleChart(sec) {
  const kind = sec.dataset.kind;
  const chartDiv = sec.querySelector(".js-chart");
  const handler = chartHandlers[kind];

  if (handler) {
    try {
      await handler(sec, chartDiv);
    } catch (e) {
      console.error(`[SingleChart Error - ${kind}]`, e);
      chartDiv.innerHTML = `<div style="padding:10px;color:#b00">Error al cargar gráfico: ${escapeHtml(e.message)}</div>`;
    }
  } else {
    chartDiv.innerHTML = `<div style="padding:10px;color:#b00">Tipo de gráfico no soportado: ${escapeHtml(kind)}</div>`;
  }
}