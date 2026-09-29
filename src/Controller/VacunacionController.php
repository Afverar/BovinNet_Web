<?php

declare(strict_types=1);

namespace BovinNet\Controller;

use BovinNet\Repository\AnimalRepository;
use BovinNet\Repository\VacunacionRepository;
use PDOException;

class VacunacionController extends BaseController
{
    protected const MODULO = 'vacunacion';

    /** Sugerencias para el campo "tipo de vacuna" (se puede escribir otra). */
    private const VACUNAS_SUGERIDAS = [
        'Fiebre aftosa',
        'Brucelosis',
        'Rabia bovina',
        'Carbón sintomático',
        'Leptospirosis',
        'IBR / DVB',
        'Clostridiales',
    ];

    /**
     * Pantalla de Vacunación: registra la aplicación de una vacuna
     * y muestra el historial.
     */
    public function gestionar(): void
    {
        $animalRepo = new AnimalRepository($this->conexion);
        $vacRepo    = new VacunacionRepository($this->conexion);

        $errores      = [];
        $errorGeneral = '';
        $valores      = [
            'id_animal'   => '',
            'tipo_vacuna' => '',
            'fecha'       => $this->hoy(),
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

            // Tipo de vacuna
            if ($valores['tipo_vacuna'] === '') {
                $errores['tipo_vacuna'] = 'Indique el tipo de vacuna.';
            } elseif (mb_strlen($valores['tipo_vacuna']) > VacunacionRepository::TIPO_LONGITUD_MAXIMA) {
                $errores['tipo_vacuna'] = 'El tipo de vacuna no debe superar '
                    . VacunacionRepository::TIPO_LONGITUD_MAXIMA . ' caracteres.';
            }

            // Fecha
            if (!fechaValida($valores['fecha'])) {
                $errores['fecha'] = 'Ingrese una fecha válida.';
            } elseif ($valores['fecha'] > $this->hoy()) {
                $errores['fecha'] = 'La fecha de aplicación no puede ser futura.';
            } elseif ($animal !== null
                && !empty($animal['fecha_nacimiento'])
                && $valores['fecha'] < $animal['fecha_nacimiento']) {
                $errores['fecha'] = 'La vacuna no puede ser anterior al nacimiento del bovino.';
            }

            if (empty($errores)) {
                try {
                    $vacRepo->registrar(
                        (int)$animal['id_animal'],
                        $valores['tipo_vacuna'],
                        $valores['fecha'],
                        (int)$this->usuario['id_usuario']
                    );
                    $this->redirigir('vacunacion.php?ok=1');
                } catch (PDOException $e) {
                    $errorGeneral = 'Error de base de datos: ' . $e->getMessage();
                }
            }
        }

        $filtroFecha = $this->leerFiltroFecha();

        $this->renderizar('vacunacion', [
            'errores'      => $errores,
            'errorGeneral' => $errorGeneral,
            'valores'      => $valores,
            'animales'     => $animales,
            'sugeridas'    => self::VACUNAS_SUGERIDAS,
            'vacunas'      => $vacRepo->listar($filtroFecha),
            'filtroFecha'  => $filtroFecha,
            'mensajeExito' => isset($_GET['ok']) ? 'Vacunación registrada correctamente.' : '',
            'hoy'          => $this->hoy(),
        ]);
    }
}
