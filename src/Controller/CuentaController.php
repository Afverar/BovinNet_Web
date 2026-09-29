<?php

declare(strict_types=1);

namespace BovinNet\Controller;

use BovinNet\Repository\UsuarioRepository;
use BovinNet\Security\Sesion;
use PDOException;

class CuentaController extends BaseController
{
    protected const MODULO = 'cuenta';

    private const CLAVE_MINIMA = 8;
    private const CLAVE_MAXIMA = 72; // límite del algoritmo bcrypt

    /** Pantalla para que el usuario cambie su propia contraseña. */
    public function cambiarClave(): void
    {
        $usuarios     = new UsuarioRepository($this->conexion);
        $errores      = [];
        $errorGeneral = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$this->csrfValido()) {
            $errorGeneral = self::MSG_CSRF;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actual    = (string)($_POST['clave_actual'] ?? '');
            $nueva     = (string)($_POST['clave_nueva'] ?? '');
            $confirmar = (string)($_POST['clave_confirmar'] ?? '');
            $idUsuario = (int)$this->usuario['id_usuario'];

            $hash = $usuarios->obtenerHash($idUsuario);
            $actualOk = $hash !== null && password_verify($actual, $hash);
            if (!$actualOk) {
                $errores['clave_actual'] = 'La contraseña actual no es correcta.';
            }

            if (strlen($nueva) < self::CLAVE_MINIMA) {
                $errores['clave_nueva'] = 'Debe tener al menos ' . self::CLAVE_MINIMA . ' caracteres.';
            } elseif (strlen($nueva) > self::CLAVE_MAXIMA) {
                $errores['clave_nueva'] = 'No debe superar ' . self::CLAVE_MAXIMA . ' caracteres.';
            } elseif (!preg_match('/[A-Za-z]/', $nueva) || !preg_match('/\d/', $nueva)) {
                $errores['clave_nueva'] = 'Debe combinar letras y números.';
            } elseif ($actualOk && $nueva === $actual) {
                $errores['clave_nueva'] = 'Debe ser distinta de la contraseña actual.';
            }

            if ($nueva !== $confirmar) {
                $errores['clave_confirmar'] = 'La confirmación no coincide.';
            }

            if (empty($errores)) {
                try {
                    $usuarios->actualizarHash($idUsuario, password_hash($nueva, PASSWORD_DEFAULT));
                    Sesion::regenerarId();
                    $this->redirigir('cuenta.php?ok=1');
                } catch (PDOException $e) {
                    $errorGeneral = 'Error de base de datos: ' . $e->getMessage();
                }
            }
        }

        $this->renderizar('cuenta', [
            'errores'      => $errores,
            'errorGeneral' => $errorGeneral,
            'mensajeExito' => isset($_GET['ok']) ? 'Contraseña actualizada correctamente.' : '',
            'claveMinima'  => self::CLAVE_MINIMA,
        ]);
    }
}
