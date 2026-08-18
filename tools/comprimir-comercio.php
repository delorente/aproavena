<?php
declare(strict_types=1);
/**
 * Convierte los volcados de comercio exterior al formato compacto que consume
 * el dashboard.
 *
 * El volcado original trae un registro por fila con las claves completas y un
 * campo "Description" (el texto largo de la partida arancelaria) que el
 * navegador nunca usa: solo se leen año, país, código y peso. Repetido en
 * 38.617 filas, ese campo era el 81% del archivo.
 *
 * Formato de salida:
 *   {"cols":["year","country","code","value"],"rows":[[2000,"Albania","1004",56.4], ...]}
 *
 * Uso:
 *   php tools/comprimir-comercio.php uploads/exportaciones_crudo.json data/exportaciones_resumidas.json
 *
 * Los volcados crudos van en uploads/, que está en .gitignore: al repositorio
 * solo sube la versión compacta.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Solo por consola.\n");
}

$origen  = $argv[1] ?? '';
$destino = $argv[2] ?? '';

if ($origen === '' || $destino === '') {
    fwrite(STDERR, "Uso: php tools/comprimir-comercio.php <origen.json> <destino.json>\n");
    exit(1);
}

if (!is_file($origen)) {
    fwrite(STDERR, "No existe el archivo de origen: $origen\n");
    exit(1);
}

$crudo = json_decode((string)file_get_contents($origen), true);

if (!is_array($crudo) || $crudo === []) {
    fwrite(STDERR, "El origen no es un JSON con registros: $origen\n");
    exit(1);
}

$filas = [];

foreach ($crudo as $r) {
    // Si ya viene en formato compacto no hay nada que hacer: evita que una
    // segunda pasada destruya el archivo.
    if (!isset($r['Year'], $r['Reported Country'], $r['Number Code'])) {
        fwrite(STDERR, "Registro sin las claves esperadas. ¿Ya estaba comprimido?\n");
        exit(1);
    }

    $anio = (int)$r['Year'];
    $pais = trim((string)$r['Reported Country']);

    if ($anio <= 0 || $pais === '') {
        continue;
    }

    $filas[] = [$anio, $pais, trim((string)$r['Number Code']), (float)($r['Net Weight Sum'] ?? 0)];
}

$salida = json_encode(
    ['cols' => ['year', 'country', 'code', 'value'], 'rows' => $filas],
    JSON_UNESCAPED_UNICODE
);

if ($salida === false) {
    fwrite(STDERR, "No se pudo codificar la salida: " . json_last_error_msg() . "\n");
    exit(1);
}

if (file_put_contents($destino, $salida) === false) {
    fwrite(STDERR, "No se pudo escribir: $destino\n");
    exit(1);
}

printf(
    "%s -> %s : %d registros, %.2f MB -> %.2f MB (%.0f%% menos)\n",
    basename($origen),
    basename($destino),
    count($filas),
    filesize($origen) / 1048576,
    strlen($salida) / 1048576,
    100 * (1 - strlen($salida) / filesize($origen))
);
