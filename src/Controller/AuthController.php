<?php

declare(strict_types=1);

namespace BovinNet\Controller;

use BovinNet\Repository\LogErrorRepository;
use BovinNet\Repository\UsuarioRepository;
use BovinNet\Security\Sesion;
use PDO;
use PDOException;

class AuthController
{
    private const MAX_INTENTOS    = 5;
    private const VENTANA_MINUTOS = 15;

    /**
     * Hash sin uso real: se verifica cuando el correo no existe para que
     * la respuesta tarde igual y no se pueda averiguar qué correos existen.
     */
    private const HASH_FALSO = '$2y$10$FPQw8O/d2FXBCnqRIcAWOud0TPeZXrPMz20uzvj1JKU2YMFZsUaeG';

    private UsuarioRepository $usuarios;
    private LogErrorRepository $bitacora;

    public function __construct(PDO $conexion)
    {
        $this->usuarios = new UsuarioRepository($conexion);
        $this->bitacora = new LogErrorRepository($conexion);
    }

    /** Pantalla de inicio de sesión. */
    public function iniciarSesion(): void
    {
        if (Sesion::usuario() !== null) {
            header('Location: dashboard.php');
            exit;
        }

        $error  = '';
        $correo = '';
        $aviso  = Sesion::tomarAviso();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = mb_strtolower(trim((string)($_POST['correo'] ?? '')));
            $clave  = (string)($_POST['clave'] ?? '');

            if (!Sesion::validarToken($_POST['csrf'] ?? null)) {
                $error = 'La página expiró. Intente de nuevo.';
            } elseif ($correo === '' || $clave === '') {
                $error = 'Ingrese su correo y su contraseña.';
            } elseif ($this->estaBloqueado($correo)) {
                $error = 'Demasiados intentos fallidos. Espere ' . self::VENTANA_MINUTOS
                       . ' minutos e intente de nuevo.';
            } else {
                $usuario = $this->usuarios->buscarPorCorreo($correo);
                $hash    = $usuario['contrasena_hash'] ?? self::HASH_FALSO;
                $claveOk = password_verify($clave, (string)$hash);

                if ($usuario !== null && $claveOk && $usuario['estado'] === 'activo') {
                    if (password_needs_rehash((string)$hash, PASSWORD_DEFAULT)) {
                        $this->usuarios->actualizarHash(
                            (int)$usuario['id_usuario'],
                            password_hash($clave, PASSWORD_DEFAULT)
                        );
                    }

                    Sesion::iniciarSesionUsuario([
                        'id_usuario' => (int)$usuario['id_usuario'],
                        'nombres'    => (string)$usuario['nombres'],
                        'apellidos'  => (string)$usuario['apellidos'],
                        'nombre_rol' => (string)$usuario['nombre_rol'],
                    ]);
                    $this->registrar('LOGIN_OK', 'Ingreso: ' . $correo, (int)$usuario['id_usuario']);

                    header('Location: dashboard.php');
                    exit;
                }

                $this->registrar('LOGIN_FAIL', $this->mensajeFallo($correo));
                $error = 'Correo o contraseña incorrectos.';
            }
        }

        require __DIR__ . '/../../public/login.view.php';
    }

    private function mensajeFallo(string $correo): string
    {
        return 'Intento fallido: ' . mb_substr($correo, 0, 200);
    }

    private function estaBloqueado(string $correo): bool
    {
        try {
            return $this->bitacora->contarRecientes(
                'LOGIN_FAIL',
                $this->mensajeFallo($correo),
                self::VENTANA_MINUTOS
            ) >= self::MAX_INTENTOS;
        } catch (PDOException $e) {
            error_log('BovinNet: no se pudo consultar la bitácora de accesos: ' . $e->getMessage());
            return false;
        }
    }

    private function registrar(string $codigo, string $mensaje, ?int $idUsuario = null): void
    {
        try {
            $this->bitacora->registrar($codigo, $mensaje, $idUsuario);
        } catch (PDOException $e) {
            error_log('BovinNet: no se pudo escribir en la bitácora: ' . $e->getMessage());
        }
    }
}
