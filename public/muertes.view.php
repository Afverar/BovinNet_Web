<?php
$titulo       = 'Registro de muertes / bajas';
$breadcrumb   = 'Módulos / Muertes y bajas';
$paginaActiva = 'muertes';
require __DIR__ . '/../src/View/layout_inicio.php';
?>
      <div class="grid" style="grid-template-columns:1fr 1.3fr; gap:20px;">
        <div class="card">
          <h3 class="mt-0">Registrar baja</h3>

          <?php if ($errorGeneral !== ''): ?>
            <div class="alert alert-danger"><?= e($errorGeneral) ?></div>
          <?php endif; ?>
          <?php if ($mensajeExito !== ''): ?>
            <div class="alert alert-success"><?= e($mensajeExito) ?></div>
          <?php endif; ?>

          <form method="POST" action="muertes.php"
                onsubmit="return confirm('¿Confirma dar de baja este bovino? Esta acción no se puede deshacer.');">
            <?= campoCsrf() ?>
            <div class="field">
              <label for="id_animal">Bovino</label>
              <select id="id_animal" name="id_animal" class="input <?= isset($errores['id_animal']) ? 'error' : '' ?>" required>
                <option value="">Seleccione un bovino</option>
                <?php foreach ($animales as $a): ?>
                  <option value="<?= (int)$a['id_animal'] ?>"
                          <?= $valores['id_animal'] === (string)$a['id_animal'] ? 'selected' : '' ?>>
                    <?= e(etiquetaAnimal($a)) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errores['id_animal'])): ?>
                <div class="error-text">⚠ <?= e($errores['id_animal']) ?></div>
              <?php endif; ?>
            </div>

            <div class="field">
              <label for="fecha">Fecha</label>
              <input id="fecha" name="fecha" class="input <?= isset($errores['fecha']) ? 'error' : '' ?>"
                     type="date" max="<?= e($hoy) ?>" required value="<?= e($valores['fecha']) ?>">
              <?php if (isset($errores['fecha'])): ?>
                <div class="error-text">⚠ <?= e($errores['fecha']) ?></div>
              <?php endif; ?>
            </div>

            <div class="field">
              <label for="causa">Causa</label>
              <select id="causa" name="causa" class="input <?= isset($errores['causa']) ? 'error' : '' ?>">
                <?php foreach ($causas as $c): ?>
                  <option value="<?= e($c) ?>" <?= $valores['causa'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errores['causa'])): ?>
                <div class="error-text">⚠ <?= e($errores['causa']) ?></div>
              <?php endif; ?>
            </div>

            <div class="field">
              <label for="observaciones">Observaciones del veterinario</label>
              <textarea id="observaciones" name="observaciones" rows="3"
                        class="input <?= isset($errores['observaciones']) ? 'error' : '' ?>"
                        maxlength="<?= (int)$detalleMax ?>"
                        placeholder="Describe brevemente lo ocurrido..."><?= e($valores['observaciones']) ?></textarea>
              <?php if (isset($errores['observaciones'])): ?>
                <div class="error-text">⚠ <?= e($errores['observaciones']) ?></div>
              <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-danger btn-full">
              Confirmar baja
            </button>
            <p class="hint" style="text-align:center; margin-top:10px;">
              Esta acción es irreversible y se solicitará confirmación adicional.
            </p>
          </form>
        </div>

        <div class="card">
          <div class="card-title-row">
            <h3>Historial de bajas</h3>
            <form method="GET" action="muertes.php" style="display:flex; gap:8px; align-items:center;">
              <select class="input" style="width:150px; padding:7px 10px; font-size:12.5px;"
                      name="causa" onchange="this.form.submit()">
                <option value="">Todas las causas</option>
                <?php foreach ($causas as $c): ?>
                  <option value="<?= e($c) ?>" <?= $filtroCausa === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
              </select>
              <?php if ($filtroCausa !== ''): ?>
                <a href="muertes.php" class="small text-muted">Limpiar</a>
              <?php endif; ?>
            </form>
          </div>
          <table class="table">
            <thead><tr><th>Fecha</th><th>Arete</th><th>Bovino</th><th>Causa</th></tr></thead>
            <tbody>
              <?php if (empty($bajas)): ?>
                <tr><td colspan="4" style="text-align:center;">No hay bajas registradas<?= $filtroCausa !== '' ? ' con esa causa' : ' todavía' ?>.</td></tr>
              <?php else: ?>
                <?php foreach ($bajas as $b): ?>
                  <?php $partes = explode($separador, (string)$b['causa'], 2); ?>
                  <tr>
                    <td><?= e(fechaCorta($b['fecha'])) ?></td>
                    <td>#<?= e($b['arete']) ?></td>
                    <td><?= e($b['nombre'] ?? '—') ?></td>
                    <td>
                      <span class="badge badge-baja"><?= e($partes[0]) ?></span>
                      <?php if (isset($partes[1])): ?>
                        <div class="small text-muted"><?= e($partes[1]) ?></div>
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
