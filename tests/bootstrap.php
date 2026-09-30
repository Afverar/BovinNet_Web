<?php

declare(strict_types=1);

// Bootstrap exclusivo para pruebas unitarias: NO se conecta a MySQL ni
// arranca una sesion real, a diferencia de config/bootstrap.php (usado por
// la aplicacion web). Esto permite ejecutar "composer test" sin depender de
// que XAMPP/MySQL esten corriendo.

spl_autoload_register(static function (string $clase): void {
    $prefijo = 'BovinNet\\';
    if (strncmp($clase, $prefijo, strlen($prefijo)) !== 0) {
        return;
    }
    $relativa = str_replace('\\', '/', substr($clase, strlen($prefijo)));

    // BovinNet\Tests\X vive en tests/X.php; el resto vive en src/X.php.
    if (strncmp($relativa, 'Tests/', 6) === 0) {
        $archivo = __DIR__ . '/' . substr($relativa, 6) . '.php';
    } else {
        $archivo = __DIR__ . '/../src/' . $relativa . '.php';
    }

    if (is_file($archivo)) {
        require_once $archivo;
    }
});
