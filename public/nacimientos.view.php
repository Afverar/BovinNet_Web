<?php
$titulo       = 'Registro de nacimientos';
$breadcrumb   = 'Módulos / Nacimientos';
$paginaActiva = 'nacimientos';
require __DIR__ . '/../src/View/layout_inicio.php';
?>
      <div class="grid" style="grid-template-columns:1fr 1.3fr; gap:20px;">
        <div class="card">
          <h3 class="mt-0">Registrar nacimiento</h3>

          <?php if ($errorGeneral !== ''): ?>
            <div class="alert alert-danger"><?= e($errorGeneral) ?></div>
          <?php endif; ?>
          <?php if ($mensajeExito !== ''): ?>
            <div class="alert alert-success"><?= e($mensajeExito) ?></div>
          <?php endif; ?>

          <form method="POST" action="nacimientos.php">
            <?= campoCsrf() ?>
            <div class="field">
              <label for="arete">Código de arete de la cría</label>
              <input id="arete" name="arete" class="input <?= isset($errores['arete']) ? 'error' : '' ?>"
                     maxlength="10" required value="<?= e($valores['arete']) ?>">
              <?php if (isset($errores['arete'])): ?>
                <div class="error-text">⚠ <?= e($errores['arete']) ?></div>
              <?php endif; ?>
            </div>

            <div class="field">
              <label for="nombre">Nombre (opcional)</label>
              <input id="nombre" name="nombre" class="input <?= isset($errores['nombre']) ? 'error' : '' ?>"
                     maxlength="60" placeholder="Ej. Lucero" value="<?= e($valores['nombre']) ?>">
              <?php if (isset($errores['nombre'])): ?>
                <div class="error-text">⚠ <?= e($errores['nombre']) ?></div>
              <?php endif; ?>
            </div>

            <div class="field">
              <label for="id_madre">Madre (opcional)</label>
              <select id="id_madre" name="id_madre" class="input <?= isset($errores['id_madre']) ? 'error' : '' ?>">
                <option value="">Sin registrar</option>
                <?php foreach ($madres as $m): ?>
                  <option value="<?= (int)$m['id_animal'] ?>"
                          data-lote="<?= (int)$m['id_lote'] ?>"
                          <?= $valores['id_madre'] === (string)$m['id_animal'] ? 'selected' : '' ?>>
                    <?= e(etiquetaAnimal($m)) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errores['id_madre'])): ?>
                <div class="error-text">⚠ <?= e($errores['id_madre']) ?></div>
              <?php endif; ?>
              <?php if (empty($madres)): ?>
                <div class="hint">No hay hembras activas registradas todavía.</div>
              <?php endif; ?>
            </div>

            <div class="grid grid-2">
              <div class="field">
                <label for="fecha">Fecha de nacimiento</label>
                <input id="fecha" name="fecha" class="input <?= isset($errores['fecha']) ? 'error' : '' ?>"
                       type="date" max="<?= e($hoy) ?>" required value="<?= e($valores['fecha']) ?>">
                <?php if (isset($errores['fecha'])): ?>
                  <div class="error-text">⚠ <?= e($errores['fecha']) ?></div>
                <?php endif; ?>
              </div>
              <div class="field">
                <label for="sexo">Sexo de la cría</label>
                <select id="sexo" name="sexo" class="input">
                  <option value="Hembra" <?= $valores['sexo'] === 'Hembra' ? 'selected' : '' ?>>Hembra</option>
                  <option value="Macho"  <?= $valores['sexo'] === 'Macho'  ? 'selected' : '' ?>>Macho</option>
                </select>
              </div>
            </div>

            <div class="grid grid-2">
              <div class="field">
                <label for="peso">Peso al nacer (kg)</label>
                <input id="peso" name="peso" class="input <?= isset($errores['peso']) ? 'error' : '' ?>"
                       type="number" step="0.01" min="0" placeholder="0" value="<?= e($valores['peso']) ?>">
                <?php if (isset($errores['peso'])): ?>
                  <div class="error-text">⚠ <?= e($errores['peso']) ?></div>
                <?php endif; ?>
              </div>
              <div class="field">
                <label for="id_lote">Lote</label>
                <select id="id_lote" name="id_lote" class="input <?= isset($errores['id_lote']) ? 'error' : '' ?>" required>
                  <option value="">Seleccione un lote</option>
                  <?php foreach ($lotes as $lote): ?>
                    <option value="<?= (int)$lote['id_lote'] ?>"
                            <?= $valores['id_lote'] === (string)$lote['id_lote'] ? 'selected' : '' ?>>
                      <?= e($lote['nombre_lote']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if (isset($errores['id_lote'])): ?>
                  <div class="error-text">⚠ <?= e($errores['id_lote']) ?></div>
                <?php endif; ?>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full">
              Registrar nacimiento
            </button>
            <p class="hint" style="text-align:center; margin-top:10px;">
              La cría se crea como un nuevo bovino activo. Al elegir la madre se sugiere su lote.
            </p>
          </form>
        </div>

        <div class="card">
          <div class="card-title-row">
            <h3>Historial de nacimientos</h3>
            <form method="GET" action="nacimientos.php" style="display:flex; gap:8px; align-items:center;">
              <input class="input" style="width:150px; padding:7px 10px; font-size:12.5px;" type="date"
                     name="fecha" value="<?= e($filtroFecha) ?>" onchange="this.form.submit()">
              <?php if ($filtroFecha !== ''): ?>
                <a href="nacimientos.php" class="small text-muted">Limpiar</a>
              <?php endif; ?>
            </form>
          </div>
          <table class="table">
            <thead>
              <tr><th>Fecha</th><th>Cría (arete)</th><th>Madre</th><th>Sexo</th><th>Peso (kg)</th></tr>
            </thead>
            <tbody>
              <?php if (empty($nacimientos)): ?>
                <tr><td colspan="5" style="text-align:center;">No hay nacimientos registrados<?= $filtroFecha !== '' ? ' en esa fecha' : ' todavía' ?>.</td></tr>
              <?php else: ?>
                <?php foreach ($nacimientos as $n): ?>
                  <tr>
                    <td><?= e(fechaCorta($n['fecha'])) ?></td>
                    <td><?= e(etiquetaAnimal($n)) ?></td>
                    <td><?= $n['madre_arete'] !== null
                              ? e(etiquetaAnimal(['arete' => $n['madre_arete'], 'nombre' => $n['madre_nombre']]))
                              : '—' ?></td>
                    <td><?= e($n['sexo']) ?></td>
                    <td><?= $n['peso'] !== null ? e(number_format((float)$n['peso'], 2)) : '—' ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

<script>
  // Al elegir la madre, sugerir su lote para la cría.
  document.getElementById('id_madre').addEventListener('change', function () {
    var lote = this.options[this.selectedIndex].getAttribute('data-lote');
    if (lote) { document.getElementById('id_lote').value = lote; }
  });
</script>
<?php require __DIR__ . '/../src/View/layout_fin.php'; ?>
