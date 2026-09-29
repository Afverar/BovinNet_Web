<?php

declare(strict_types=1);

namespace BovinNet\Controller;

use BovinNet\Model\Animal;
use BovinNet\Repository\AnimalRepository;
use BovinNet\Repository\NacimientoRepository;
use PDOException;

class NacimientoController extends BaseController
{
    protected const MODULO = 'nacimientos';

    /**
     * Pantalla de Nacimientos: registra la cría (animal + nacimiento)
     * y muestra el historial.
     */
    public function gestionar(): void
    {
        $animalRepo = new AnimalRepository($this->conexion);
        $nacRepo    = new NacimientoRepository($this->conexion);

        $errores      = [];
        $errorGeneral = '';
        $valores      = [
            'arete'    => '',
            'nombre'   => '',
            'sexo'     => 'Hembra',
            'fecha'    => $this->hoy(),
            'peso'     => '',
            'id_madre' => '',
            'id_lote'  => '',
        ];

        $madres = $animalRepo->obtenerActivos('Hembra');
        $lotes  = $animalRepo->obtenerLotes();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$this->csrfValido()) {
            $errorGeneral = self::MSG_CSRF;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($valores as $campo => $porDefecto) {
                $valores[$campo] = trim((string)($_POST[$campo] ?? ''));
            }

            // Arete
            if ($valores['arete'] === '' || strlen($valores['arete']) > Animal::ARETE_LONGITUD_MAXIMA) {
                $errores['arete'] = 'El arete es obligatorio y no debe superar '
                    . Animal::ARETE_LONGITUD_MAXIMA . ' caracteres.';
            } elseif ($animalRepo->existeArete($valores['arete'])) {
                $errores['arete'] = 'El arete ingresado ya existe (arete duplicado).';
            }

            // Nombre
            if (mb_strlen($valores['nombre']) > 60) {
                $errores['nombre'] = 'El nombre no debe superar 60 caracteres.';
            }

            // Sexo
            if (!in_array($valores['sexo'], ['Hembra', 'Macho'], true)) {
                $errores['sexo'] = 'Seleccione el sexo de la cría.';
            }

            // Lote
            if ($this->buscarPorId($lotes, 'id_lote', $valores['id_lote']) === null) {
                $errores['id_lote'] = 'Seleccione un lote.';
            }

            // Madre (opcional)
            $madre = null;
            if ($valores['id_madre'] !== '') {
                $madre = $this->buscarPorId($madres, 'id_animal', $valores['id_madre']);
                if ($madre === null) {
                    $errores['id_madre'] = 'Seleccione una madre válida (hembra activa).';
                }
            }

            // Fecha
            if (!fechaValida($valores['fecha'])) {
                $errores['fecha'] = 'Ingrese una fecha válida.';
            } elseif ($valores['fecha'] > $this->hoy()) {
                $errores['fecha'] = 'La fecha de nacimiento no puede ser futura.';
            } elseif ($madre !== null
                && !empty($madre['fecha_nacimiento'])
                && $valores['fecha'] <= $madre['fecha_nacimiento']) {
                $errores['fecha'] = 'La cría no puede nacer antes que su madre.';
            }

            // Peso (opcional)
            $peso = null;
            if ($valores['peso'] !== '') {
                $normal = str_replace(',', '.', $valores['peso']);
                if (!is_numeric($normal) || (float)$normal <= 0 || (float)$normal > 9999.99) {
                    $errores['peso'] = 'Ingrese un peso entre 0.01 y 9999.99 kg.';
                } else {
                    $peso = round((float)$normal, 2);
                }
            }

            if (empty($errores)) {
                $cria = new Animal(
                    0,
                    $valores['arete'],
                    $valores['nombre'] !== '' ? $valores['nombre'] : null,
                    $valores['sexo'],
                    $peso,
                    $valores['fecha'],
                    (int)$valores['id_lote'],
                    $madre !== null ? (int)$madre['id_animal'] : null
                );

                try {
                    $this->conexion->beginTransaction();
                    $idCria = $animalRepo->insertar($cria);
                    $nacRepo->registrar($idCria, $valores['fecha'], (int)$this->usuario['id_usuario']);
                    $this->conexion->commit();
                } catch (PDOException $e) {
                    if ($this->conexion->inTransaction()) {
                        $this->conexion->rollBack();
                    }
                    $errorGeneral = 'Error de base de datos: ' . $e->getMessage();
                }

                if ($errorGeneral === '') {
                    $this->redirigir('nacimientos.php?ok=1');
                }
            }
        }

        $filtroFecha = $this->leerFiltroFecha();

        $this->renderizar('nacimientos', [
            'errores'      => $errores,
            'errorGeneral' => $errorGeneral,
            'valores'      => $valores,
            'madres'       => $madres,
            'lotes'        => $lotes,
            'nacimientos'  => $nacRepo->listar($filtroFecha),
            'filtroFecha'  => $filtroFecha,
            'mensajeExito' => isset($_GET['ok']) ? 'Nacimiento registrado correctamente. La cría quedó creada como bovino activo.' : '',
            'hoy'          => $this->hoy(),
        ]);
    }
}
