<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

use BovinNet\Security\Autorizacion;

$usuarioActual = Autorizacion::exigir('dashboard');
$titulo        = 'Panel principal';
$breadcrumb    = 'Inicio / Panel principal';
$paginaActiva  = 'dashboard';
require __DIR__ . '/../src/View/layout_inicio.php';
?>

      <div class="grid grid-4" style="margin-bottom:22px;">
        <div class="card kpi">
          <div class="kpi-label">🐄 Bovinos activos</div>
          <div class="kpi-value">248</div>
          <div class="kpi-delta">+6 este mes</div>
        </div>
        <div class="card kpi">
          <div class="kpi-label">🐣 Nacimientos (mes)</div>
          <div class="kpi-value">9</div>
          <div class="kpi-delta">+2 vs. mes anterior</div>
        </div>
        <div class="card kpi">
          <div class="kpi-label">⚠ Muertes / bajas (mes)</div>
          <div class="kpi-value">3</div>
          <div class="kpi-delta warn">1 por enfermedad</div>
        </div>
        <div class="card kpi">
          <div class="kpi-label">💉 Vacunas pendientes</div>
          <div class="kpi-value">12</div>
          <div class="kpi-delta warn">4 próximas esta semana</div>
        </div>
      </div>

      <div class="grid grid-2">
        <div class="card">
          <div class="card-title-row">
            <h3>Próximas vacunaciones</h3>
            <a href="vacunacion.php">Ver todas →</a>
          </div>
          <table class="table">
            <thead><tr><th>Arete</th><th>Bovino</th><th>Vacuna</th><th>Estado</th></tr></thead>
            <tbody>
              <tr><td>#0231</td><td>Lucero</td><td>Fiebre aftosa</td><td><span class="badge badge-proxima">Próxima</span></td></tr>
              <tr><td>#0198</td><td>Canela</td><td>Brucelosis</td><td><span class="badge badge-programada">Programada</span></td></tr>
              <tr><td>#0304</td><td>Trueno</td><td>Fiebre aftosa</td><td><span class="badge badge-proxima">Próxima</span></td></tr>
              <tr><td>#0157</td><td>Estrella</td><td>Carbón bacteriano</td><td><span class="badge badge-aldia">Al día</span></td></tr>
            </tbody>
          </table>
        </div>

        <div class="card">
          <div class="card-title-row">
            <h3>Actividad reciente</h3>
            <span class="small text-muted">Últimos eventos</span>
          </div>
          <table class="table">
            <thead><tr><th>Fecha</th><th>Evento</th><th>Detalle</th></tr></thead>
            <tbody>
              <tr><td>12 sep</td><td>Nacimiento</td><td>Cría de "Estrella" (#0157)</td></tr>
              <tr><td>10 sep</td><td>Vacunación</td><td>Aftosa aplicada a #0231</td></tr>
              <tr><td>08 sep</td><td>Baja</td><td>#0090 — causa: vejez</td></tr>
              <tr><td>05 sep</td><td>Registro</td><td>Nuevo bovino #0305 (Canela II)</td></tr>
            </tbody>
          </table>
        </div>
      </div>

<?php require __DIR__ . '/../src/View/layout_fin.php'; ?>
