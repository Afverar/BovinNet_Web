<?php
// Se muestra con código 403 cuando el rol del usuario no tiene permiso sobre el módulo.
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BovinNet · Acceso denegado</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="error-wrap">
  <div class="error-card">
    <div class="error-icon">!</div>
    <h1>Acceso denegado</h1>
    <p>Su rol no tiene permiso para entrar a este módulo. Si cree que es un error, comuníquese con el administrador.</p>
    <div class="error-actions">
      <a class="btn btn-outline" href="dashboard.php">Ir al panel principal</a>
      <form method="POST" action="logout.php" style="margin:0;">
        <?= campoCsrf() ?>
        <button type="submit" class="btn btn-primary">Cerrar sesión</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
