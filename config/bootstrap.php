<?php

declare(strict_types=1);

// Configuración común de las páginas de BovinNet.
// (En producción conviene poner display_errors en '0'.)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

date_default_timezone_set('America/Bogota');

// Cabeceras de seguridad básicas
header('X-Frame-Options: DENY');                 // evita que la página se cargue dentro de un <iframe>
header('X-Content-Type-Options: nosniff');       // el navegador no adivina tipos de archivo
header('Referrer-Policy: same-origin');

// Conexión PDO: deja disponible la variable $conexion
require_once __DIR__ . '/db.php';

// Autocarga de clases: BovinNet\Controller\X => src/Controller/X.php
spl_autoload_register(static function (string $clase): void {
    $prefijo = 'BovinNet\\';
    if (strncmp($clase, $prefijo, strlen($prefijo)) !== 0) {
        return;
    }
    $relativa = str_replace('\\', '/', substr($clase, strlen($prefijo)));
    $archivo  = __DIR__ . '/../src/' . $relativa . '.php';
    if (is_file($archivo)) {
        require_once $archivo;
    }
});

// Sesión segura (cookie httponly, expiración por inactividad)
\BovinNet\Security\Sesion::iniciar();

/** Escapa texto para imprimirlo de forma segura en HTML. */
function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

/** True si $fecha tiene formato AAAA-MM-DD y es una fecha real. */
function fechaValida(string $fecha): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    return $d !== false && $d->format('Y-m-d') === $fecha;
}

/** Convierte 2026-09-12 en "12 sep 2026". */
function fechaCorta(?string $fecha): string
{
    if ($fecha === null || $fecha === '' || !fechaValida($fecha)) {
        return '—';
    }
    $meses = [1 => 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    [$anio, $mes, $dia] = explode('-', $fecha);
    return $dia . ' ' . $meses[(int)$mes] . ' ' . $anio;
}

/** Texto para mostrar un animal en listas: "#0157 — Estrella". */
function etiquetaAnimal(array $animal): string
{
    $texto = '#' . $animal['arete'];
    if (!empty($animal['nombre'])) {
        $texto .= ' — ' . $animal['nombre'];
    }
    return $texto;
}

/** Campo oculto con el token anti-CSRF; va dentro de todo <form method="POST">. */
function campoCsrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e(\BovinNet\Security\Sesion::token()) . '">';
}

/** Texto de variación mensual: "+3 este mes" / "-1 este mes" / "Sin cambios". */
function deltaMensual(int $actual, int $anterior): string
{
    $diferencia = $actual - $anterior;
    if ($diferencia === 0) {
        return 'Igual que el mes anterior';
    }
    $signo = $diferencia > 0 ? '+' : '';
    return $signo . $diferencia . ' vs. mes anterior';
}
