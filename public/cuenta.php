<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

use BovinNet\Controller\CuentaController;

(new CuentaController($conexion))->cambiarClave();
