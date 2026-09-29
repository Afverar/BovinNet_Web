<?php

declare(strict_types=1);

namespace BovinNet\Repository;

use PDO;

/**
 * Consultas de solo lectura para el panel principal.
 * Todos los datos provienen de las tablas reales: nada aquí es simulado.
 */
class DashboardRepository
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /** Bovinos que no tienen registro de baja. */
    public function contarActivos(): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM animal a
                WHERE NOT EXISTS (SELECT 1 FROM baja b WHERE b.id_animal = a.id_animal)";
        return (int)$this->conexion->query($sql)->fetch()['total'];
    }

    /** Total de bovinos registrados (activos e inactivos). */
    public function contarTotalBovinos(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM animal";
        return (int)$this->conexion->query($sql)->fetch()['total'];
    }

    /**
     * Cuenta filas de $tabla cuya columna fecha cae en el mes actual y en el mes anterior.
     * $tabla debe ser uno de: nacimiento, baja, vacunacion (nombres fijos, no vienen del usuario).
     *
     * @return array{actual:int, anterior:int}
     */
    public function contarPorMes(string $tabla): array
    {
        $tablasPermitidas = ['nacimiento', 'baja', 'vacunacion'];
        if (!in_array($tabla, $tablasPermitidas, true)) {
            throw new \InvalidArgumentException("Tabla no permitida: $tabla");
        }

        $sql = "SELECT
                    SUM(CASE WHEN YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE())
                             THEN 1 ELSE 0 END) AS actual,
                    SUM(CASE WHEN YEAR(fecha) = YEAR(CURDATE() - INTERVAL 1 MONTH)
                              AND MONTH(fecha) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                             THEN 1 ELSE 0 END) AS anterior
                FROM {$tabla}";
        $fila = $this->conexion->query($sql)->fetch();

        return [
            'actual'   => (int)($fila['actual'] ?? 0),
            'anterior' => (int)($fila['anterior'] ?? 0),
        ];
    }

    /**
     * Causa de baja más frecuente en el mes actual (o null si no hubo bajas).
     */
    public function causaBajaMasFrecuenteDelMes(): ?string
    {
        $sql = "SELECT SUBSTRING_INDEX(causa, ' — ', 1) AS categoria, COUNT(*) AS total
                FROM baja
                WHERE YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE())
                GROUP BY categoria
                ORDER BY total DESC, categoria ASC
                LIMIT 1";
        $fila = $this->conexion->query($sql)->fetch();

        return $fila === false ? null : (string)$fila['categoria'];
    }

    /**
     * Últimos eventos registrados combinando nacimientos, bajas y vacunaciones.
     *
     * @return array<int,array<string,mixed>>
     */
    public function obtenerActividadReciente(int $limite = 8): array
    {
        $sql = "(SELECT n.fecha AS fecha, 'Nacimiento' AS tipo,
                        CONCAT('Cría del bovino #', a.arete,
                               IF(a.nombre IS NOT NULL, CONCAT(' (', a.nombre, ')'), '')) AS detalle,
                        n.id_nacimiento AS orden
                 FROM nacimiento n INNER JOIN animal a ON a.id_animal = n.id_animal)
                UNION ALL
                (SELECT b.fecha, 'Baja',
                        CONCAT('#', a.arete, ' — ', SUBSTRING_INDEX(b.causa, ' — ', 1)),
                        b.id_baja
                 FROM baja b INNER JOIN animal a ON a.id_animal = b.id_animal)
                UNION ALL
                (SELECT v.fecha, 'Vacunación',
                        CONCAT(v.tipo_vacuna, ' aplicada a #', a.arete),
                        v.id_vacunacion
                 FROM vacunacion v INNER JOIN animal a ON a.id_animal = v.id_animal)
                ORDER BY fecha DESC, orden DESC
                LIMIT :limite";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Últimos bovinos registrados (por id_animal, ya que la tabla no guarda
     * fecha de creación del registro).
     *
     * @return array<int,array<string,mixed>>
     */
    public function obtenerUltimosBovinos(int $limite = 6): array
    {
        $sql = "SELECT a.arete, a.nombre, a.sexo, l.nombre_lote,
                       NOT EXISTS (SELECT 1 FROM baja b WHERE b.id_animal = a.id_animal) AS activo
                FROM animal a
                INNER JOIN lote l ON l.id_lote = a.id_lote
                ORDER BY a.id_animal DESC
                LIMIT :limite";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
