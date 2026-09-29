<?php

declare(strict_types=1);

namespace BovinNet\Controller;

use BovinNet\Repository\AnimalRepository;
use BovinNet\Repository\BajaRepository;
use PDOException;

class BajaController extends BaseController
{
    protected const MODULO = 'muertes';

    /**
     * Pantalla de Muertes / Bajas: registra la baja de un bovino
     * y muestra el historial.
     */
    public function gestionar(): void
    {
        $animalRepo = new AnimalRepository($this->conexion);
        $bajaRepo   = new BajaRepository($this->conexion);

        $errores      = [];
        $errorGeneral = '';
        $valores      = [
            'id_animal'     => '',
            'fecha'         => $this->hoy(),
            'causa'         => 'Enfermedad',
            'observaciones' => '',
        ];

        $animales = $animalRepo->obtenerActivos();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$this->csrfValido()) {
            $errorGeneral = self::MSG_CSRF;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($valores as $campo => $porDefecto) {
                $valores[$campo] = trim((string)($_POST[$campo] ?? ''));
            }

            // Bovino
            $animal = $this->buscarPorId($animales, 'id_animal', $valores['id_animal']);
            if ($animal === null) {
                $errores['id_animal'] = 'Seleccione un bovino activo.';
            }

            // Causa
            if (!in_array($valores['causa'], BajaRepository::CAUSAS, true)) {
                $errores['causa'] = 'Seleccione una causa.';
            }
            if (mb_strlen($valores['observaciones']) > BajaRepository::DETALLE_MAXIMO) {
                $errores['observaciones'] = 'Las observaciones no deben superar '
                    . BajaRepository::DETALLE_MAXIMO . ' caracteres.';
            } elseif ($valores['causa'] === 'Otra' && $valores['observaciones'] === '') {
                $errores['observaciones'] = 'Describa la causa en las observaciones.';
            }

            // Fecha
            if (!fechaValida($valores['fecha'])) {
                $errores['fecha'] = 'Ingrese una fecha válida.';
            } elseif ($valores['fecha'] > $this->hoy()) {
                $errores['fecha'] = 'La fecha no puede ser futura.';
            } elseif ($animal !== null
                && !empty($animal['fecha_nacimiento'])
                && $valores['fecha'] < $animal['fecha_nacimiento']) {
                $errores['fecha'] = 'La baja no puede ser anterior al nacimiento del bovino.';
            }

            if (empty($errores)) {
                $causa = $valores['observaciones'] !== ''
                    ? $valores['causa'] . BajaRepository::SEPARADOR . $valores['observaciones']
                    : $valores['causa'];

                try {
                    $bajaRepo->registrar(
                        (int)$animal['id_animal'],
                        $valores['fecha'],
                        $causa,
                        (int)$this->usuario['id_usuario']
                    );
                    $this->redirigir('muertes.php?ok=1');
                } catch (PDOException $e) {
                    $errorGeneral = 'Error de base de datos: ' . $e->getMessage();
                }
            }
        }

        $filtroCausa = trim((string)($_GET['causa'] ?? ''));
        if (!in_array($filtroCausa, BajaRepository::CAUSAS, true)) {
            $filtroCausa = '';
        }

        $this->renderizar('muertes', [
            'errores'      => $errores,
            'errorGeneral' => $errorGeneral,
            'valores'      => $valores,
            'animales'     => $animales,
            'causas'       => BajaRepository::CAUSAS,
            'separador'    => BajaRepository::SEPARADOR,
            'detalleMax'   => BajaRepository::DETALLE_MAXIMO,
            'bajas'        => $bajaRepo->listar($filtroCausa),
            'filtroCausa'  => $filtroCausa,
            'mensajeExito' => isset($_GET['ok']) ? 'Baja registrada correctamente.' : '',
            'hoy'          => $this->hoy(),
        ]);
    }
}
