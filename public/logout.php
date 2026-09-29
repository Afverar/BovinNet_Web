<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

use BovinNet\Security\Sesion;

// Solo se cierra la sesión con un POST que traiga el token CSRF válido.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && Sesion::validarToken($_POST['csrf'] ?? null)) {
    Sesion::cerrar();
}

header('Location: login.php');
exit;
