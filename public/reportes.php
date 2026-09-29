<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

use BovinNet\Security\Autorizacion;

$usuarioActual = Autorizacion::exigir('reportes');
$titulo        = 'Reportes y estadísticas';
$breadcrumb    = 'Módulos / Reportes';
$paginaActiva  = 'reportes';
require __DIR__ . '/../src/View/layout_inicio.php';
?>

      <div class="grid grid-2">
        <div class="card">
          <div class="card-title-row">
            <h3>Natalidad vs. mortalidad (6 meses)</h3>
          </div>
          <div class="bar-chart">
            <div class="bar-group">
              <div class="bars"><div class="bar bar-nace" style="height:70px;"></div><div class="bar bar-muerte" style="height:20px;"></div></div>
              <div class="bar-label">Abr</div>
            </div>
            <div class="bar-group">
              <div class="bars"><div class="bar bar-nace" style="height:55px;"></div><div class="bar bar-muerte" style="height:30px;"></div></div>
              <div class="bar-label">May</div>
            </div>
            <div class="bar-group">
              <div class="bars"><div class="bar bar-nace" style="height:90px;"></div><div class="bar bar-muerte" style="height:15px;"></div></div>
              <div class="bar-label">Jun</div>
            </div>
            <div class="bar-group">
              <div class="bars"><div class="bar bar-nace" style="height:65px;"></div><div class="bar bar-muerte" style="height:25px;"></div></div>
              <div class="bar-label">Jul</div>
            </div>
            <div class="bar-group">
              <div class="bars"><div class="bar bar-nace" style="height:80px;"></div><div class="bar bar-muerte" style="height:18px;"></div></div>
              <div class="bar-label">Ago</div>
            </div>
            <div class="bar-group">
              <div class="bars"><div class="bar bar-nace" style="height:60px;"></div><div class="bar bar-muerte" style="height:22px;"></div></div>
              <div class="bar-label">Sep</div>
            </div>
          </div>
          <div class="legend">
            <span><span class="dot" style="background:var(--verde-oliva);"></span>Nacimientos</span>
            <span><span class="dot" style="background:var(--terracota);"></span>Muertes</span>
          </div>
        </div>

        <div class="card">
          <div class="card-title-row"><h3>Distribución por raza</h3></div>
          <div class="donut"><span>Brahman<br>45%</span></div>
          <div class="legend" style="justify-content:center; flex-wrap:wrap;">
            <span><span class="dot" style="background:var(--verde-oliva);"></span>Brahman 45%</span>
            <span><span class="dot" style="background:var(--cafe-medio);"></span>Cebú 30%</span>
            <span><span class="dot" style="background:var(--terracota);"></span>Holstein 25%</span>
          </div>
        </div>
      </div>

      <div class="card" style="margin-top:20px;">
        <div class="card-title-row">
          <h3>Exportar reportes</h3>
        </div>
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
          <button class="btn btn-outline">📄 Exportar en PDF</button>
          <button class="btn btn-outline">📊 Exportar en Excel</button>
          <button class="btn btn-outline">💉 Reporte de vacunación</button>
        </div>
        <p class="small text-muted" style="margin-top:14px; margin-bottom:0;">
          Los reportes exportados conservan la información al momento de su generación y no son editables posteriormente.
        </p>
      </div>

<?php require __DIR__ . '/../src/View/layout_fin.php'; ?>
