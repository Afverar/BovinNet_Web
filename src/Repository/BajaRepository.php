<?php

declare(strict_types=1);

namespace BovinNet\Repository;

use PDO;

class BajaRepository
{
    /** Categorías de causa disponibles en el formulario. */
    public const CAUSAS = ['Enfermedad', 'Accidente', 'Vejez', 'Sacrificio', 'Otra'];

    /** Separador entre la categoría y el detalle guardados en la columna causa. */
    public const SEPARADOR = ' — ';

    /** Máximo de caracteres del detalle (la columna causa admite 150). */
    public const DETALLE_MAXIMO = 120;

    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function registrar(int $idAnimal, string $fecha, string $causa, int $idUsuario): void
    {
        $stmt = $this->conexion->prepare(
            "INSERT INTO baja (fecha, causa, id_animal, id_usuario_registra)
             VALUES (:fecha, :causa, :idAnimal, :idUsuario)"
        );
        $stmt->execute([
            'fecha'     => $fecha,
            'causa'     => $causa,
            'idAnimal'  => $idAnimal,
            'idUsuario' => $idUsuario,
        ]);
    }

    /**
     * Historial de bajas (opcionalmente filtrado por categoría de causa).
     *
     * @return array<int,array<string,mixed>>
     */
    public function listar(string $categoria = ''): array
    {
        $sql = "SELECT b.fecha, b.causa, a.arete, a.nombre
                FROM baja b
                INNER JOIN animal a ON a.id_animal = b.id_animal";
        $params = [];
        if ($categoria !== '') {
            $sql .= " WHERE b.causa LIKE :causa";
            $params['causa'] = $categoria . '%';
        }
        $sql .= " ORDER BY b.fecha DESC, b.id_baja DESC LIMIT 200";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
