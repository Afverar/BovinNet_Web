<?php
$titulo       = 'Panel principal';
$breadcrumb   = 'Inicio / Panel principal';
$paginaActiva = 'dashboard';
require __DIR__ . '/../src/View/layout_inicio.php';
?>
      <div class="grid grid-4" style="margin-bottom:22px;">
        <div class="card kpi">
          <div class="kpi-label">🐄 Bovinos activos</div>
          <div class="kpi-value"><?= (int)$bovinosActivos ?></div>
          <div class="kpi-delta"><?= (int)$bovinosTotal ?> registrados en total</div>
        </div>
        <div class="card kpi">
          <div class="kpi-label">🐣 Nacimientos (mes)</div>
          <div class="kpi-value"><?= (int)$nacimientos['actual'] ?></div>
          <div class="kpi-delta"><?= e(deltaMensual($nacimientos['actual'], $nacimientos['anterior'])) ?></div>
        </div>
        <div class="card kpi">
          <div class="kpi-label">⚠ Muertes / bajas (mes)</div>
          <div class="kpi-value"><?= (int)$bajas['actual'] ?></div>
          <div class="kpi-delta <?= $bajas['actual'] > 0 ? 'warn' : '' ?>">
            <?= $causaMasFrecuente !== null ? 'Más frecuente: ' . e($causaMasFrecuente) : e(deltaMensual($bajas['actual'], $bajas['anterior'])) ?>
          </div>
        </div>
        <div class="card kpi">
          <div class="kpi-label">💉 Vacunaciones (mes)</div>
          <div class="kpi-value"><?= (int)$vacunas['actual'] ?></div>
          <div class="kpi-delta"><?= e(deltaMensual($vacunas['actual'], $vacunas['anterior'])) ?></div>
        </div>
      </div>

      <div class="grid grid-2">
        <div class="card">
          <div class="card-title-row">
            <h3>Actividad reciente</h3>
            <span class="small text-muted">Últimos eventos registrados</span>
          </div>
          <table class="table">
            <thead><tr><th>Fecha</th><th>Evento</th><th>Detalle</th></tr></thead>
            <tbody>
              <?php if (empty($actividadReciente)): ?>
                <tr><td colspan="3" style="text-align:center;">Todavía no hay eventos registrados.</td></tr>
              <?php else: ?>
                <?php foreach ($actividadReciente as $ev):
                    $clase = ['Nacimiento' => 'badge-activo', 'Baja' => 'badge-baja', 'Vacunación' => 'badge-programada'][$ev['tipo']] ?? 'badge-activo';
                ?>
                  <tr>
                    <td><?= e(fechaCorta($ev['fecha'])) ?></td>
                    <td><span class="badge <?= $clase ?>"><?= e($ev['tipo']) ?></span></td>
                    <td><?= e($ev['detalle']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="card">
          <div class="card-title-row">
            <h3>Últimos bovinos registrados</h3>
            <a href="bovinos.php">Ver todos →</a>
          </div>
          <table class="table">
            <thead><tr><th>Arete</th><th>Nombre</th><th>Sexo</th><th>Lote</th><th>Estado</th></tr></thead>
            <tbody>
              <?php if (empty($ultimosBovinos)): ?>
                <tr><td colspan="5" style="text-align:center;">Todavía no hay bovinos registrados.</td></tr>
              <?php else: ?>
                <?php foreach ($ultimosBovinos as $b): ?>
                  <tr>
                    <td>#<?= e($b['arete']) ?></td>
                    <td><?= e($b['nombre'] ?? '—') ?></td>
                    <td><?= e($b['sexo']) ?></td>
                    <td><?= e($b['nombre_lote']) ?></td>
                    <td>
                      <?php if ($b['activo']): ?>
                        <span class="badge badge-activo">Activo</span>
                      <?php else: ?>
                        <span class="badge badge-baja">De baja</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
<?php require __DIR__ . '/../src/View/layout_fin.php'; ?>
