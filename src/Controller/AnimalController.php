<?php

declare(strict_types=1);

namespace BovinNet\Controller;

use BovinNet\Model\Animal;
use BovinNet\Repository\AnimalRepository;
use PDO;
use PDOException;

class AnimalController extends BaseController
{
    protected const MODULO = 'bovinos';

    private AnimalRepository $animalRepository;

    public function __construct(PDO $conexion)
    {
        parent::__construct($conexion);
        $this->animalRepository = new AnimalRepository($conexion);
    }

    /**
     * Controla la pantalla de Gestión de bovinos:
     * - procesa el formulario
     * - obtiene animales y lotes
     * - carga la vista maquetada (bovinos.view.php).
     */
    public function gestionarBovinos(): void
    {
        $errorArete   = '';
        $mensajeExito = '';
        $errorGeneral = '';

        // Valores para repoblar el formulario tras envío
        $valorArete    = '';
        $valorNombre   = '';
        $valorSexo     = 'Hembra';
        $valorPeso     = '';
        $valorFechaNac = '';
        $valorIdLote   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$this->csrfValido()) {
            $errorGeneral = self::MSG_CSRF;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Leer datos del formulario
            $arete       = trim($_POST['arete'] ?? '');
            $nombre      = trim($_POST['nombre'] ?? '');
            $sexo        = $_POST['sexo'] ?? 'Hembra';
            $pesoStr     = trim($_POST['peso'] ?? '');
            $fechaNacStr = trim($_POST['fecha_nacimiento'] ?? '');
            $idLote      = isset($_POST['idLote']) ? (int)$_POST['idLote'] : 0;

            // 2. Guardar valores para reusar en la vista
            $valorArete    = $arete;
            $valorNombre   = $nombre;
            $valorSexo     = $sexo;
            $valorPeso     = $pesoStr;
            $valorFechaNac = $fechaNacStr;
            $valorIdLote   = $idLote > 0 ? (string)$idLote : '';

            // 3. Normalizar peso y fecha
            $peso            = $pesoStr !== '' ? (float)str_replace(',', '.', $pesoStr) : null;
            $fechaNacimiento = $fechaNacStr !== '' ? $fechaNacStr : null;

            // 4. Validar
            if (!in_array($sexo, ['Hembra', 'Macho'], true)) {
                $errorGeneral = 'Seleccione un sexo válido.';
            } elseif ($idLote <= 0) {
                $errorGeneral = 'Seleccione un lote.';
            } elseif ($peso !== null && ($peso <= 0 || $peso > 9999.99)) {
                $errorGeneral = 'El peso debe estar entre 0.01 y 9999.99 kg.';
            } elseif ($fechaNacimiento !== null
                && (!fechaValida($fechaNacimiento) || $fechaNacimiento > $this->hoy())) {
                $errorGeneral = 'La fecha de nacimiento no es válida o es futura.';
            } elseif (mb_strlen($nombre) > 60) {
                $errorGeneral = 'El nombre no debe superar 60 caracteres.';
            }

            if ($errorGeneral === '') {
                // 5. Crear objeto de dominio
                $animal = new Animal(
                    0,                              // idAnimal (autoincrement)
                    $arete,
                    $nombre !== '' ? $nombre : null,
                    $sexo,
                    $peso,
                    $fechaNacimiento,
                    $idLote
                );

                // 6. Validar arete y registrar
                if (!$animal->tieneAreteValido()) {
                    $errorArete = 'El arete es obligatorio y no debe superar '
                        . Animal::ARETE_LONGITUD_MAXIMA . ' caracteres.';
                } elseif ($this->animalRepository->existeArete($arete)) {
                    $errorArete = 'El arete ingresado ya existe (arete duplicado).';
                } else {
                    try {
                        $this->animalRepository->registrarNacimiento($animal);
                        $mensajeExito = 'Bovino registrado correctamente.';

                        // Limpiar formulario tras éxito
                        $valorArete    = '';
                        $valorNombre   = '';
                        $valorSexo     = 'Hembra';
                        $valorPeso     = '';
                        $valorFechaNac = '';
                        $valorIdLote   = '';
                    } catch (PDOException $e) {
                        $errorGeneral = 'Error de base de datos: ' . $e->getMessage();
                    }
                }
            }
        }

        // 7. Obtener datos para la vista
        $animales = $this->animalRepository->obtenerTodos();
        $lotes    = $this->animalRepository->obtenerLotes();

        // 8. Cargar la vista maquetada
        $this->renderizar('bovinos', compact(
            'animales', 'lotes', 'errorArete', 'errorGeneral', 'mensajeExito',
            'valorArete', 'valorNombre', 'valorSexo', 'valorPeso', 'valorFechaNac', 'valorIdLote'
        ));
    }
}
