-- Datos mínimos para poder ingresar a BovinNet y registrar nacimientos, bajas y vacunaciones.
-- Es seguro ejecutarlo varias veces: solo inserta o corrige lo que falta.
-- Ejecutar en phpMyAdmin: base de datos bovinnet > pestaña Importar (o pestaña SQL).
USE bovinnet;

-- Roles usados por el control de acceso (ver src/Security/Autorizacion.php)
INSERT INTO rol (nombre_rol, descripcion)
SELECT 'Administrador', 'Acceso total al sistema' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM rol WHERE nombre_rol = 'Administrador');

INSERT INTO rol (nombre_rol, descripcion)
SELECT 'Veterinario', 'Vacunación, bajas y consulta' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM rol WHERE nombre_rol = 'Veterinario');

INSERT INTO rol (nombre_rol, descripcion)
SELECT 'Operario', 'Bovinos, nacimientos y vacunación' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM rol WHERE nombre_rol = 'Operario');

INSERT INTO finca (nombre_finca, ubicacion, area_hectareas)
SELECT 'Finca principal', 'Sin especificar', NULL FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM finca);

-- Usuario administrador inicial.
--   Correo:     carlos.perez@bovinnet.local
--   Contraseña: BovinNet#2026   (temporal: cámbiela al ingresar, menú "Cambiar contraseña")
INSERT INTO usuario (nombres, apellidos, cedula, fecha_nacimiento, correo, contrasena_hash, estado, id_rol, id_finca)
SELECT 'Carlos', 'Pérez', '1000000001', '1985-04-12', 'carlos.perez@bovinnet.local',
       '$2y$10$bLV2WfHOs8Yx3h84XM9ryORQVN7gFaCh1E.s3NB9Yg43D.oW25PZW', 'activo',
       (SELECT id_rol FROM rol WHERE nombre_rol = 'Administrador'),
       (SELECT MIN(id_finca) FROM finca)
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM usuario);

-- Si el usuario ya existía con la contraseña provisional de la versión anterior, se le asigna la temporal.
UPDATE usuario
SET contrasena_hash = '$2y$10$bLV2WfHOs8Yx3h84XM9ryORQVN7gFaCh1E.s3NB9Yg43D.oW25PZW'
WHERE contrasena_hash = 'PENDIENTE_LOGIN';
