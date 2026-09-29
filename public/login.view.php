<?php
// Variables: $error, $correo, $aviso
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BovinNet · Iniciar sesión</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="logo-big">BN</div>
    <h1>BovinNet</h1>
    <p class="sub">Sistema de gestión ganadera</p>

    <?php if ($aviso !== ''): ?>
      <div class="alert alert-danger"><?= e($aviso) ?></div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
      <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" autocomplete="on">
      <?= campoCsrf() ?>
      <div class="field">
        <label for="correo">Correo electrónico</label>
        <input id="correo" name="correo" type="email" class="input" required autofocus
               autocomplete="username" maxlength="100" value="<?= e($correo) ?>">
      </div>
      <div class="field">
        <label for="clave">Contraseña</label>
        <input id="clave" name="clave" type="password" class="input" required
               autocomplete="current-password" maxlength="72">
      </div>
      <button type="submit" class="btn btn-primary btn-full">Ingresar</button>
    </form>
  </div>
</div>
</body>
</html>
