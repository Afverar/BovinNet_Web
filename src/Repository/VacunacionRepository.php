<?php

declare(strict_types=1);

namespace BovinNet\Repository;

use PDO;

class VacunacionRepository
{
    /** Longitud máxima de tipo_vacuna en la base de datos. */
    public const TIPO_LONGITUD_MAXIMA = 80;

    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function registrar(int $idAnimal, string $tipoVacuna, string $fecha, int $idUsuario): void
    {
        $stmt = $this->conexion->prepare(
            "INSERT INTO vacunacion (tipo_vacuna, fecha, id_animal, id_usuario_registra)
             VALUES (:tipo, :fecha, :idAnimal, :idUsuario)"
        );
        $stmt->execute([
            'tipo'      => $tipoVacuna,
            'fecha'     => $fecha,
            'idAnimal'  => $idAnimal,
            'idUsuario' => $idUsuario,
        ]);
    }

    /**
     * Historial de vacunación (opcionalmente de un solo día).
     *
     * @return array<int,array<string,mixed>>
     */
    public function listar(string $fecha = ''): array
    {
        $sql = "SELECT v.fecha, v.tipo_vacuna, a.arete, a.nombre
                FROM vacunacion v
                INNER JOIN animal a ON a.id_animal = v.id_animal";
        $params = [];
        if ($fecha !== '') {
            $sql .= " WHERE v.fecha = :fecha";
            $params['fecha'] = $fecha;
        }
        $sql .= " ORDER BY v.fecha DESC, v.id_vacunacion DESC LIMIT 200";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
