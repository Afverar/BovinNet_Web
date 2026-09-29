<?php

declare(strict_types=1);

namespace BovinNet\Repository;

use PDO;

class NacimientoRepository
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /** Inserta el registro de nacimiento de un animal ya creado. */
    public function registrar(int $idAnimal, string $fecha, int $idUsuario): void
    {
        $stmt = $this->conexion->prepare(
            "INSERT INTO nacimiento (fecha, id_animal, id_usuario_registra)
             VALUES (:fecha, :idAnimal, :idUsuario)"
        );
        $stmt->execute([
            'fecha'     => $fecha,
            'idAnimal'  => $idAnimal,
            'idUsuario' => $idUsuario,
        ]);
    }

    /**
     * Historial de nacimientos (opcionalmente de un solo día).
     *
     * @return array<int,array<string,mixed>>
     */
    public function listar(string $fecha = ''): array
    {
        $sql = "SELECT n.fecha, c.arete, c.nombre, c.sexo, c.peso,
                       m.arete AS madre_arete, m.nombre AS madre_nombre
                FROM nacimiento n
                INNER JOIN animal c ON c.id_animal = n.id_animal
                LEFT JOIN animal m ON m.id_animal = c.id_animal_madre";
        $params = [];
        if ($fecha !== '') {
            $sql .= " WHERE n.fecha = :fecha";
            $params['fecha'] = $fecha;
        }
        $sql .= " ORDER BY n.fecha DESC, n.id_nacimiento DESC LIMIT 200";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
