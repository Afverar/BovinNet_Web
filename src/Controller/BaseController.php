<?php

declare(strict_types=1);

namespace BovinNet\Controller;

use BovinNet\Security\Autorizacion;
use BovinNet\Security\Sesion;
use PDO;

/**
 * Funciones comunes de los controladores de los módulos.
 * Cada controlador hijo declara su MODULO; el constructor exige sesión
 * iniciada y permiso del rol sobre ese módulo.
 */
abstract class BaseController
{
    protected const MODULO = '';

    protected const MSG_CSRF = 'El formulario expiró o no es válido. Recargue la página e intente de nuevo.';

    protected PDO $conexion;

    /** @var array<string,mixed> usuario autenticado (de la sesión) */
    protected array $usuario;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
        $this->usuario  = Autorizacion::exigir(static::MODULO);
    }

    protected function hoy(): string
    {
        return date('Y-m-d');
    }

    /** True si el formulario enviado trae un token anti-CSRF válido. */
    protected function csrfValido(): bool
    {
        return Sesion::validarToken($_POST['csrf'] ?? null);
    }

    /** Lee ?fecha=AAAA-MM-DD de la URL; devuelve '' si no es válida. */
    protected function leerFiltroFecha(): string
    {
        $fecha = trim((string)($_GET['fecha'] ?? ''));
        return ($fecha !== '' && fechaValida($fecha)) ? $fecha : '';
    }

    /** Busca una fila por id dentro de una lista de arreglos. */
    protected function buscarPorId(array $lista, string $campo, string $id): ?array
    {
        foreach ($lista as $fila) {
            if ((string)$fila[$campo] === $id) {
                return $fila;
            }
        }
        return null;
    }

    /** Muestra public/{vista}.view.php con los datos indicados. */
    protected function renderizar(string $vista, array $datos): void
    {
        $datos['usuarioActual'] = $this->usuario;
        extract($datos, EXTR_SKIP);
        require __DIR__ . '/../../public/' . $vista . '.view.php';
    }

    protected function redirigir(string $destino): void
    {
        header('Location: ' . $destino);
        exit;
    }
}
