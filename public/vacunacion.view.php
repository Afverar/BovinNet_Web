<?php
$titulo       = 'Vacunación';
$breadcrumb   = 'Módulos / Vacunación';
$paginaActiva = 'vacunacion';
require __DIR__ . '/../src/View/layout_inicio.php';
?>
      <div class="grid" style="grid-template-columns:1fr 1.3fr; gap:20px;">
        <div class="card">
          <h3 class="mt-0">Registrar vacunación</h3>

          <?php if ($errorGeneral !== ''): ?>
            <div class="alert alert-danger"><?= e($errorGeneral) ?></div>
          <?php endif; ?>
          <?php if ($mensajeExito !== ''): ?>
            <div class="alert alert-success"><?= e($mensajeExito) ?></div>
          <?php endif; ?>

          <form method="POST" action="vacunacion.php">
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
              <label for="tipo_vacuna">Tipo de vacuna</label>
              <input id="tipo_vacuna" name="tipo_vacuna" list="lista_vacunas"
                     class="input <?= isset($errores['tipo_vacuna']) ? 'error' : '' ?>"
                     maxlength="80" required placeholder="Elija o escriba una vacuna"
                     value="<?= e($valores['tipo_vacuna']) ?>">
              <datalist id="lista_vacunas">
                <?php foreach ($sugeridas as $v): ?>
                  <option value="<?= e($v) ?>"></option>
                <?php endforeach; ?>
              </datalist>
              <?php if (isset($errores['tipo_vacuna'])): ?>
                <div class="error-text">⚠ <?= e($errores['tipo_vacuna']) ?></div>
              <?php endif; ?>
            </div>

            <div class="field">
              <label for="fecha">Fecha de aplicación</label>
              <input id="fecha" name="fecha" class="input <?= isset($errores['fecha']) ? 'error' : '' ?>"
                     type="date" max="<?= e($hoy) ?>" required value="<?= e($valores['fecha']) ?>">
              <?php if (isset($errores['fecha'])): ?>
                <div class="error-text">⚠ <?= e($errores['fecha']) ?></div>
              <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-full">
              Registrar vacunación
            </button>
          </form>
        </div>

        <div class="card">
          <div class="card-title-row">
            <h3>Historial de vacunación</h3>
            <form method="GET" action="vacunacion.php" style="display:flex; gap:8px; align-items:center;">
              <input class="input" style="width:150px; padding:7px 10px; font-size:12.5px;" type="date"
                     name="fecha" value="<?= e($filtroFecha) ?>" onchange="this.form.submit()">
              <?php if ($filtroFecha !== ''): ?>
                <a href="vacunacion.php" class="small text-muted">Limpiar</a>
              <?php endif; ?>
            </form>
          </div>
          <table class="table">
            <thead><tr><th>Fecha</th><th>Arete</th><th>Nombre</th><th>Vacuna</th></tr></thead>
            <tbody>
              <?php if (empty($vacunas)): ?>
                <tr><td colspan="4" style="text-align:center;">No hay vacunaciones registradas<?= $filtroFecha !== '' ? ' en esa fecha' : ' todavía' ?>.</td></tr>
              <?php else: ?>
                <?php foreach ($vacunas as $v): ?>
                  <tr>
                    <td><?= e(fechaCorta($v['fecha'])) ?></td>
                    <td>#<?= e($v['arete']) ?></td>
                    <td><?= e($v['nombre'] ?? '—') ?></td>
                    <td><?= e($v['tipo_vacuna']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
<?php require __DIR__ . '/../src/View/layout_fin.php'; ?>
