<?php

declare(strict_types=1);

namespace BovinNet\Security;

/**
 * Manejo seguro de la sesión: cookie protegida, expiración por inactividad,
 * usuario autenticado y token anti-CSRF.
 */
final class Sesion
{
    /** Segundos de inactividad tras los cuales se cierra la sesión (30 min). */
    private const TIEMPO_INACTIVIDAD = 1800;

    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_name('BOVINNET_SID');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,       // JavaScript no puede leer la cookie
            'samesite' => 'Lax',      // no se envía en peticiones cruzadas de otros sitios
            'secure'   => !empty($_SERVER['HTTPS']),
        ]);
        session_start();

        $ahora = time();
        if (isset($_SESSION['ultima_actividad'])
            && ($ahora - (int)$_SESSION['ultima_actividad']) > self::TIEMPO_INACTIVIDAD) {
            self::destruir();
            session_start();
            $_SESSION['aviso'] = 'Su sesión se cerró por inactividad. Ingrese de nuevo.';
        }
        $_SESSION['ultima_actividad'] = $ahora;
    }

    /** @return array<string,mixed>|null */
    public static function usuario(): ?array
    {
        $u = $_SESSION['usuario'] ?? null;
        return is_array($u) ? $u : null;
    }

    /**
     * Guarda al usuario autenticado. Se cambia el id de sesión para evitar
     * la fijación de sesión y se genera un token CSRF nuevo.
     *
     * @param array<string,mixed> $usuario
     */
    public static function iniciarSesionUsuario(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario']          = $usuario;
        $_SESSION['csrf']             = bin2hex(random_bytes(32));
        $_SESSION['ultima_actividad'] = time();
    }

    public static function regenerarId(): void
    {
        session_regenerate_id(true);
    }

    public static function cerrar(): void
    {
        self::destruir();
    }

    /** Mensaje de un solo uso (por ejemplo, "sesión expirada"). */
    public static function tomarAviso(): string
    {
        $aviso = (string)($_SESSION['aviso'] ?? '');
        unset($_SESSION['aviso']);
        return $aviso;
    }

    /** Token anti-CSRF de la sesión actual. */
    public static function token(): string
    {
        if (empty($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function validarToken($recibido): bool
    {
        return is_string($recibido)
            && isset($_SESSION['csrf'])
            && is_string($_SESSION['csrf'])
            && hash_equals($_SESSION['csrf'], $recibido);
    }

    private static function destruir(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $p['path'],
                'domain'   => $p['domain'],
                'secure'   => $p['secure'],
                'httponly' => $p['httponly'],
                'samesite' => 'Lax',
            ]);
        }
        session_destroy();
    }
}
