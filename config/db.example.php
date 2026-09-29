<?php

declare(strict_types=1);

// Copie este archivo como config/db.php y complete sus credenciales.
// config/db.php NO se sube al repositorio (ver .gitignore).

$host = 'localhost';
$db   = 'bovinnet';
$user = 'root';
$pass = '';              // en XAMPP por defecto es vacía
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conexion = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log('BovinNet - error de conexión: ' . $e->getMessage());
    http_response_code(500);
    die('No fue posible conectar con la base de datos. Revise config/db.php y que MySQL esté iniciado.');
}
