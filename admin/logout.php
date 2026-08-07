<?php
declare(strict_types=1);
require __DIR__ . '/../inc/bootstrap.php';

// Solo por POST y con token: así un <img src="...logout.php"> en otra web
// no puede cerrarle la sesión al administrador.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    cerrar_sesion();
}

redirect('admin/login.php');
