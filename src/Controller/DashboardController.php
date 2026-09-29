<?php

declare(strict_types=1);

namespace BovinNet\Controller;

use BovinNet\Repository\DashboardRepository;

class DashboardController extends BaseController
{
    protected const MODULO = 'dashboard';

    /** Muestra el panel principal con datos reales de la base de datos. */
    public function mostrar(): void
    {
        $repo = new DashboardRepository($this->conexion);

        $nacimientos = $repo->contarPorMes('nacimiento');
        $bajas       = $repo->contarPorMes('baja');
        $vacunas     = $repo->contarPorMes('vacunacion');

        $this->renderizar('dashboard', [
            'bovinosActivos'      => $repo->contarActivos(),
            'bovinosTotal'        => $repo->contarTotalBovinos(),
            'nacimientos'         => $nacimientos,
            'bajas'               => $bajas,
            'vacunas'             => $vacunas,
            'causaMasFrecuente'   => $repo->causaBajaMasFrecuenteDelMes(),
            'actividadReciente'   => $repo->obtenerActividadReciente(),
            'ultimosBovinos'      => $repo->obtenerUltimosBovinos(),
        ]);
    }
}
