<?php
/**
 * Encabezado común: <head>, barra lateral y barra superior.
 * Variables esperadas: $titulo, $breadcrumb, $paginaActiva, $usuarioActual (array|null)
 */
$nombreUsuario = trim($usuarioActual['nombres'] . ' ' . $usuarioActual['apellidos']);
$rolUsuario    = (string)$usuarioActual['nombre_rol'];
$iniciales     = mb_strtoupper(mb_substr($usuarioActual['nombres'], 0, 1) . mb_substr($usuarioActual['apellidos'], 0, 1));

$menu = [
    'dashboard'   => ['dashboard.php',   '🏠', 'Panel principal'],
    'bovinos'     => ['bovinos.php',     '🐄', 'Gestión de bovinos'],
    'nacimientos' => ['nacimientos.php', '🐣', 'Nacimientos'],
    'muertes'     => ['muertes.php',     '⚠', 'Muertes / Bajas'],
    'vacunacion'  => ['vacunacion.php',  '💉', 'Vacunación'],
    'reportes'    => ['reportes.php',    '📊', 'Reportes'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BovinNet · <?= e($titulo) ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">
      <div class="logo">BN</div>
      <span>BovinNet</span>
    </div>
    <nav>
      <?php foreach ($menu as $clave => [$url, $icono, $texto]): ?>
        <?php if (!\BovinNet\Security\Autorizacion::permite($rolUsuario, $clave)) { continue; } ?>
        <a href="<?= e($url) ?>" class="<?= $clave === $paginaActiva ? 'active' : '' ?>"><span class="ico"><?= $icono ?></span> <?= e($texto) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="user-box">
      Sesión activa<br><strong style="color:#fff;"><?= e($nombreUsuario) ?></strong> · <?= e($rolUsuario) ?>
      <div style="margin-top:8px; display:flex; gap:12px; font-size:12px;">
        <a href="cuenta.php" style="text-decoration:underline;">Cambiar contraseña</a>
        <form method="POST" action="logout.php" style="margin:0;">
          <?= campoCsrf() ?>
          <button type="submit" style="background:none;border:0;padding:0;color:inherit;font:inherit;text-decoration:underline;cursor:pointer;">Cerrar sesión</button>
        </form>
      </div>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <div>
        <h1><?= e($titulo) ?></h1>
        <div class="breadcrumb"><?= e($breadcrumb) ?></div>
      </div>
      <div class="actions">
        <span class="small text-muted">🔔</span>
        <div class="avatar"><?= e($iniciales) ?></div>
      </div>
    </div>
    <div class="content">
