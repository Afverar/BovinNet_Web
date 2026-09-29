<?php

declare(strict_types=1);

namespace BovinNet\Repository;

use PDO;

/**
 * Bitácora de eventos de seguridad (tabla log_error).
 */
class LogErrorRepository
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function registrar(string $codigo, string $mensaje, ?int $idUsuario = null): void
    {
        $stmt = $this->conexion->prepare(
            "INSERT INTO log_error (codigo_error, mensaje, id_usuario)
             VALUES (:codigo, :mensaje, :idUsuario)"
        );
        $stmt->execute([
            'codigo'    => mb_substr($codigo, 0, 10),
            'mensaje'   => mb_substr($mensaje, 0, 255),
            'idUsuario' => $idUsuario,
        ]);
    }

    /** Cuenta eventos con ese código y mensaje en los últimos $minutos minutos. */
    public function contarRecientes(string $codigo, string $mensaje, int $minutos): int
    {
        $minutos = max(1, $minutos);
        $stmt = $this->conexion->prepare(
            "SELECT COUNT(*) AS total
             FROM log_error
             WHERE codigo_error = :codigo
               AND mensaje = :mensaje
               AND fecha_hora >= (NOW() - INTERVAL {$minutos} MINUTE)"
        );
        $stmt->execute([
            'codigo'  => mb_substr($codigo, 0, 10),
            'mensaje' => mb_substr($mensaje, 0, 255),
        ]);
        $fila = $stmt->fetch();

        return (int)($fila['total'] ?? 0);
    }
}
