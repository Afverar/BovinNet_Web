<?php

declare(strict_types=1);

namespace BovinNet\Repository;

use BovinNet\Model\Animal;
use PDO;
use InvalidArgumentException;

class AnimalRepository
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Inserta un animal y devuelve su id.
     * Solo incluye id_animal_madre en el INSERT cuando hay madre.
     *
     * @throws InvalidArgumentException si el arete no es válido.
     */
    public function insertar(Animal $animal): int
    {
        if (!$animal->tieneAreteValido()) {
            throw new InvalidArgumentException(
                "Arete inválido: {$animal->getArete()}"
            );
        }

        $columnas = 'arete, nombre, sexo, peso, fecha_nacimiento, id_lote';
        $valores  = ':arete, :nombre, :sexo, :peso, :fecha_nacimiento, :idLote';
        $params   = [
            'arete'            => $animal->getArete(),
            'nombre'           => $animal->getNombre(),
            'sexo'             => $animal->getSexo(),
            'peso'             => $animal->getPeso(),
            'fecha_nacimiento' => $animal->getFechaNacimiento(),
            'idLote'           => $animal->getIdLote(),
        ];

        if ($animal->getIdAnimalMadre() !== null) {
            $columnas .= ', id_animal_madre';
            $valores  .= ', :idMadre';
            $params['idMadre'] = $animal->getIdAnimalMadre();
        }

        $sentencia = $this->conexion->prepare(
            "INSERT INTO animal ($columnas) VALUES ($valores)"
        );
        $sentencia->execute($params);

        return (int)$this->conexion->lastInsertId();
    }

    /**
     * Inserta un animal en la base de datos (usado por Gestión de bovinos).
     *
     * @throws InvalidArgumentException si el arete no es válido.
     */
    public function registrarNacimiento(Animal $animal): bool
    {
        $this->insertar($animal);
        return true;
    }

    /**
     * Devuelve true si ya existe un animal con ese arete.
     */
    public function existeArete(string $arete): bool
    {
        $sql  = "SELECT COUNT(*) AS total FROM animal WHERE arete = :arete";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute(['arete' => $arete]);
        $fila = $stmt->fetch();

        return (int)($fila['total'] ?? 0) > 0;
    }

    /**
     * Obtiene todos los animales registrados.
     *
     * @return Animal[]
     */
    public function obtenerTodos(): array
    {
        $sql = "SELECT id_animal, arete, nombre, sexo, peso, fecha_nacimiento, id_lote 
                FROM animal
                ORDER BY id_animal DESC";

        $stmt      = $this->conexion->query($sql);
        $registros = $stmt->fetchAll();

        $animales = [];
        foreach ($registros as $row) {
            $animales[] = new Animal(
                (int)$row['id_animal'],
                $row['arete'],
                $row['nombre'],
                $row['sexo'],
                $row['peso'] !== null ? (float)$row['peso'] : null,
                $row['fecha_nacimiento'],
                (int)$row['id_lote']
            );
        }

        return $animales;
    }

    /**
     * Animales que NO han sido dados de baja (opcionalmente solo un sexo).
     *
     * @return array<int,array<string,mixed>>
     */
    public function obtenerActivos(?string $sexo = null): array
    {
        $sql = "SELECT a.id_animal, a.arete, a.nombre, a.sexo, a.fecha_nacimiento, a.id_lote
                FROM animal a
                WHERE NOT EXISTS (SELECT 1 FROM baja b WHERE b.id_animal = a.id_animal)";
        $params = [];
        if ($sexo !== null) {
            $sql .= " AND a.sexo = :sexo";
            $params['sexo'] = $sexo;
        }
        $sql .= " ORDER BY a.arete ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene todos los lotes para el combo del formulario.
     *
     * @return array<int,array<string,mixed>>
     */
    public function obtenerLotes(): array
    {
        $sql = "SELECT id_lote, nombre_lote
                FROM lote
                ORDER BY nombre_lote ASC";

        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll();
    }
}
