<?php
// Aproavena · configuración
// -----------------------------------------------------------------------------
// Copia este archivo como  inc/config.php  y completa los valores reales.
// inc/config.php está en .gitignore: nunca se sube al repositorio.

return [

    // Base de datos ----------------------------------------------------------
    'db' => [
        'host'   => 'localhost',
        'name'   => 'aproavena',
        'user'   => 'root',
        'pass'   => '',
        'charset'=> 'utf8mb4',
    ],

    // Ruta base del sitio ----------------------------------------------------
    // null  → se deduce sola comparando la carpeta del proyecto con el
    //         DocumentRoot. Sirve tanto en la raíz de un dominio como en
    //         http://localhost/proyectos/aproavena/. Es lo recomendado.
    // ''    → forzar la raíz del dominio.
    // '/sub'→ forzar un subdirectorio (útil detrás de un proxy inverso, donde
    //         la ruta pública no coincide con la del disco).
    'base_path' => null,

    // Correo -----------------------------------------------------------------
    'mail' => [
        // A dónde llegan los mensajes del formulario de contacto.
        'to'   => 'contacto@aproavena.cl',
        // Remitente. Debe ser una casilla del propio dominio: si pones el correo
        // del visitante aquí, SPF/DKIM lo marcan como falsificado y va a spam.
        'from' => 'no-reply@aproavena.cl',
    ],

    // true solo mientras desarrollas en WAMP: muestra los errores en pantalla.
    // En el VPS déjalo en false, siempre.
    'debug' => false,
];
