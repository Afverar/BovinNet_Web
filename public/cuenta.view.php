<?php
$titulo       = 'Cambiar contraseña';
$breadcrumb   = 'Cuenta / Cambiar contraseña';
$paginaActiva = '';
require __DIR__ . '/../src/View/layout_inicio.php';
?>
      <div class="grid" style="grid-template-columns:minmax(0,460px); gap:20px;">
        <div class="card">
          <h3 class="mt-0">Cambiar contraseña</h3>

          <?php if ($errorGeneral !== ''): ?>
            <div class="alert alert-danger"><?= e($errorGeneral) ?></div>
          <?php endif; ?>
          <?php if ($mensajeExito !== ''): ?>
            <div class="alert alert-success"><?= e($mensajeExito) ?></div>
          <?php endif; ?>

          <form method="POST" action="cuenta.php" autocomplete="off">
            <?= campoCsrf() ?>
            <div class="field">
              <label for="clave_actual">Contraseña actual</label>
              <input id="clave_actual" name="clave_actual" type="password" required
                     autocomplete="current-password" maxlength="72"
                     class="input <?= isset($errores['clave_actual']) ? 'error' : '' ?>">
              <?php if (isset($errores['clave_actual'])): ?>
                <div class="error-text">⚠ <?= e($errores['clave_actual']) ?></div>
              <?php endif; ?>
            </div>
            <div class="field">
              <label for="clave_nueva">Contraseña nueva</label>
              <input id="clave_nueva" name="clave_nueva" type="password" required
                     autocomplete="new-password" maxlength="72"
                     class="input <?= isset($errores['clave_nueva']) ? 'error' : '' ?>">
              <?php if (isset($errores['clave_nueva'])): ?>
                <div class="error-text">⚠ <?= e($errores['clave_nueva']) ?></div>
              <?php else: ?>
                <div class="hint">Mínimo <?= (int)$claveMinima ?> caracteres, con letras y números.</div>
              <?php endif; ?>
            </div>
            <div class="field">
              <label for="clave_confirmar">Confirmar contraseña nueva</label>
              <input id="clave_confirmar" name="clave_confirmar" type="password" required
                     autocomplete="new-password" maxlength="72"
                     class="input <?= isset($errores['clave_confirmar']) ? 'error' : '' ?>">
              <?php if (isset($errores['clave_confirmar'])): ?>
                <div class="error-text">⚠ <?= e($errores['clave_confirmar']) ?></div>
              <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Guardar contraseña</button>
          </form>
        </div>
      </div>
<?php require __DIR__ . '/../src/View/layout_fin.php'; ?>
