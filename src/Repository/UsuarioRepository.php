<?php

declare(strict_types=1);

namespace BovinNet\Repository;

use PDO;

class UsuarioRepository
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Busca un usuario por correo (incluye el hash para verificar la contraseña).
     *
     * @return array<string,mixed>|null
     */
    public function buscarPorCorreo(string $correo): ?array
    {
        $stmt = $this->conexion->prepare(
            "SELECT u.id_usuario, u.nombres, u.apellidos, u.contrasena_hash, u.estado, r.nombre_rol
             FROM usuario u
             INNER JOIN rol r ON r.id_rol = u.id_rol
             WHERE u.correo = :correo
             LIMIT 1"
        );
        $stmt->execute(['correo' => $correo]);
        $fila = $stmt->fetch();

        return $fila === false ? null : $fila;
    }

    public function obtenerHash(int $idUsuario): ?string
    {
        $stmt = $this->conexion->prepare(
            "SELECT contrasena_hash FROM usuario WHERE id_usuario = :id"
        );
        $stmt->execute(['id' => $idUsuario]);
        $fila = $stmt->fetch();

        return $fila === false ? null : (string)$fila['contrasena_hash'];
    }

    public function actualizarHash(int $idUsuario, string $hash): void
    {
        $stmt = $this->conexion->prepare(
            "UPDATE usuario SET contrasena_hash = :hash WHERE id_usuario = :id"
        );
        $stmt->execute(['hash' => $hash, 'id' => $idUsuario]);
    }
}
