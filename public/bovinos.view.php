<?php
$titulo       = 'Gestión de bovinos';
$breadcrumb   = 'Módulos / Gestión de bovinos';
$paginaActiva = 'bovinos';
require __DIR__ . '/../src/View/layout_inicio.php';
?>

      <div class="grid" style="grid-template-columns:1.3fr 1fr; gap:20px;">

        <!-- LISTADO DE BOVINOS -->
        <div class="card">
          <div class="card-title-row">
            <h3>Listado de bovinos</h3>
            <span class="small text-muted">
              <?php echo count($animales); ?> registros
            </span>
          </div>
          <div class="field" style="margin-bottom:14px;">
            <input class="input" placeholder="Buscar por código de arete o nombre...">
          </div>
          <table class="table">
            <thead>
              <tr>
                <th>Arete ↕</th>
                <th>Nombre</th>
                <th>Sexo</th>
                <th>Peso (kg)</th>
                <th>Fecha nac.</th>
                <th>ID Lote</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($animales)): ?>
                <tr>
                  <td colspan="6" style="text-align:center;">No hay bovinos registrados todavía.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($animales as $animal): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($animal->getArete()); ?></td>
                    <td><?php echo htmlspecialchars($animal->getNombre() ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($animal->getSexo()); ?></td>
                    <td>
                      <?php
                        $p = $animal->getPeso();
                        echo $p !== null ? htmlspecialchars(number_format($p, 2)) : '—';
                      ?>
                    </td>
                    <td><?php echo htmlspecialchars($animal->getFechaNacimiento() ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars((string)$animal->getIdLote()); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- FORMULARIO DE REGISTRO -->
        <div class="card">
          <h3 class="mt-0">Registrar bovino</h3>

          <?php if (!empty($errorGeneral)): ?>
            <div class="alert alert-danger">
              <?php echo htmlspecialchars($errorGeneral); ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($mensajeExito)): ?>
            <div class="alert alert-success">
              <?php echo htmlspecialchars($mensajeExito); ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="bovinos.php" class="form-animal">
            <?= campoCsrf() ?>
            <div class="field">
              <label for="arete">Código de arete</label>
              <input
                id="arete"
                name="arete"
                class="input <?php echo $errorArete ? 'error' : ''; ?>"
                maxlength="<?php echo \BovinNet\Model\Animal::ARETE_LONGITUD_MAXIMA; ?>"
                value="<?php echo htmlspecialchars($valorArete ?? ''); ?>"
                required
              >
              <?php if ($errorArete): ?>
                <div class="error-text">⚠ <?php echo htmlspecialchars($errorArete); ?></div>
              <?php endif; ?>
            </div>

            <div class="field">
              <label for="nombre">Nombre</label>
              <input
                id="nombre"
                name="nombre"
                class="input"
                placeholder="Ej. Lucero"
                value="<?php echo htmlspecialchars($valorNombre ?? ''); ?>"
              >
            </div>

            <div class="grid grid-2">
              <div class="field">
                <label for="sexo">Sexo</label>
                <select id="sexo" name="sexo" class="input">
                  <option value="Hembra" <?php echo ($valorSexo === 'Hembra') ? 'selected' : ''; ?>>Hembra</option>
                  <option value="Macho"  <?php echo ($valorSexo === 'Macho')  ? 'selected' : ''; ?>>Macho</option>
                </select>
              </div>

              <div class="field">
                <label for="idLote">Lote</label>
                <select id="idLote" name="idLote" class="input" required>
                  <option value="">Seleccione un lote</option>
                  <?php foreach ($lotes as $lote): ?>
                    <option
                      value="<?php echo (int)$lote['id_lote']; ?>"
                      <?php echo ($valorIdLote === (string)$lote['id_lote']) ? 'selected' : ''; ?>
                    >
                      <?php echo htmlspecialchars($lote['nombre_lote']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="grid grid-2">
              <div class="field">
                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                <input
                  class="input"
                  type="date"
                  id="fecha_nacimiento"
                  name="fecha_nacimiento"
                  value="<?php echo htmlspecialchars($valorFechaNac ?? ''); ?>"
                >
              </div>
              <div class="field">
                <label for="peso">Peso (kg)</label>
                <input
                  class="input"
                  type="number"
                  step="0.01"
                  id="peso"
                  name="peso"
                  placeholder="0"
                  value="<?php echo htmlspecialchars($valorPeso ?? ''); ?>"
                >
              </div>
            </div>

            <button
              type="submit"
              class="btn btn-primary btn-full"
              <?php echo $errorArete ? 'disabled' : ''; ?>
            >
              Guardar bovino
            </button>
            <p class="hint" style="text-align:center; margin-top:10px;">
              El botón se deshabilita cuando hay errores en el código de arete.
            </p>
          </form>
        </div>
      </div>

<?php require __DIR__ . '/../src/View/layout_fin.php'; ?>
