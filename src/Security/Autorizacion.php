<?php

declare(strict_types=1);

namespace BovinNet\Security;

/**
 * Control de acceso por rol.
 *
 * Para cambiar quién puede entrar a cada módulo basta con editar MATRIZ.
 * Un rol que no aparezca aquí no tiene acceso a ningún módulo.
 */
final class Autorizacion
{
    /** Módulos permitidos por nombre de rol (tabla rol.nombre_rol). */
    private const MATRIZ = [
        'Administrador' => ['dashboard', 'bovinos', 'nacimientos', 'muertes', 'vacunacion', 'reportes', 'cuenta'],
        'Veterinario'   => ['dashboard', 'bovinos', 'muertes', 'vacunacion', 'reportes', 'cuenta'],
        'Operario'      => ['dashboard', 'bovinos', 'nacimientos', 'vacunacion', 'cuenta'],
    ];

    public static function permite(string $rol, string $modulo): bool
    {
        return in_array($modulo, self::MATRIZ[$rol] ?? [], true);
    }

    /**
     * Exige sesión iniciada y permiso sobre el módulo.
     * Sin sesión redirige al login; sin permiso responde 403.
     *
     * @return array<string,mixed> usuario autenticado
     */
    public static function exigir(string $modulo): array
    {
        $usuario = Sesion::usuario();

        if ($usuario === null) {
            header('Location: login.php');
            exit;
        }

        if (!self::permite((string)$usuario['nombre_rol'], $modulo)) {
            http_response_code(403);
            require __DIR__ . '/../View/acceso_denegado.php';
            exit;
        }

        return $usuario;
    }
}
