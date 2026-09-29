-- =========================================================
-- BovinNet - Sistema de gestion ganadera
-- Script de creacion de base de datos (MySQL 8.0)
-- Evidencia GA6-220501096-AA2-EV02
-- =========================================================

DROP DATABASE IF EXISTS bovinnet;
CREATE DATABASE bovinnet
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_spanish_ci;
USE bovinnet;

-- ---------------------------------------------------------
-- Tabla: rol
-- ---------------------------------------------------------
CREATE TABLE rol (
  id_rol      INT UNSIGNED AUTO_INCREMENT,
  nombre_rol  VARCHAR(50)  NOT NULL,
  descripcion VARCHAR(150) NULL,
  CONSTRAINT pk_rol PRIMARY KEY (id_rol),
  CONSTRAINT uq_rol_nombre UNIQUE (nombre_rol)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: finca
-- ---------------------------------------------------------
CREATE TABLE finca (
  id_finca       INT UNSIGNED AUTO_INCREMENT,
  nombre_finca   VARCHAR(100) NOT NULL,
  ubicacion      VARCHAR(150) NOT NULL,
  area_hectareas DECIMAL(8,2) NULL,
  CONSTRAINT pk_finca PRIMARY KEY (id_finca),
  CONSTRAINT ck_finca_area CHECK (area_hectareas IS NULL OR area_hectareas > 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: usuario
-- ---------------------------------------------------------
CREATE TABLE usuario (
  id_usuario      INT UNSIGNED AUTO_INCREMENT,
  nombres         VARCHAR(60)  NOT NULL,
  apellidos       VARCHAR(60)  NOT NULL,
  cedula          VARCHAR(15)  NOT NULL,
  fecha_nacimiento DATE        NOT NULL,
  correo          VARCHAR(100) NOT NULL,
  contrasena_hash VARCHAR(255) NOT NULL,
  estado          ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  id_rol          INT UNSIGNED NOT NULL,
  id_finca        INT UNSIGNED NULL,
  CONSTRAINT pk_usuario PRIMARY KEY (id_usuario),
  CONSTRAINT uq_usuario_cedula UNIQUE (cedula),
  CONSTRAINT uq_usuario_correo UNIQUE (correo),
  CONSTRAINT fk_usuario_rol FOREIGN KEY (id_rol)
      REFERENCES rol (id_rol)
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_usuario_finca FOREIGN KEY (id_finca)
      REFERENCES finca (id_finca)
      ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT ck_usuario_mayor_edad
      CHECK (fecha_nacimiento <= DATE_SUB(CURDATE(), INTERVAL 18 YEAR))
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: lote
-- ---------------------------------------------------------
CREATE TABLE lote (
  id_lote     INT UNSIGNED AUTO_INCREMENT,
  nombre_lote VARCHAR(50) NOT NULL,
  id_finca    INT UNSIGNED NOT NULL,
  CONSTRAINT pk_lote PRIMARY KEY (id_lote),
  CONSTRAINT uq_lote_finca UNIQUE (nombre_lote, id_finca),
  CONSTRAINT fk_lote_finca FOREIGN KEY (id_finca)
      REFERENCES finca (id_finca)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: animal
-- ---------------------------------------------------------
CREATE TABLE animal (
  id_animal        INT UNSIGNED AUTO_INCREMENT,
  arete            VARCHAR(10) NOT NULL,
  nombre           VARCHAR(60) NULL,
  sexo             ENUM('Hembra','Macho') NOT NULL,
  peso             DECIMAL(6,2) NULL,
  fecha_nacimiento DATE NULL,
  id_lote          INT UNSIGNED NOT NULL,
  id_animal_madre  INT UNSIGNED NULL,
  CONSTRAINT pk_animal PRIMARY KEY (id_animal),
  CONSTRAINT uq_animal_arete UNIQUE (arete),
  CONSTRAINT fk_animal_lote FOREIGN KEY (id_lote)
      REFERENCES lote (id_lote)
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_animal_madre FOREIGN KEY (id_animal_madre)
      REFERENCES animal (id_animal)
      ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: nacimiento
-- ---------------------------------------------------------
CREATE TABLE nacimiento (
  id_nacimiento      INT UNSIGNED AUTO_INCREMENT,
  fecha              DATE NOT NULL,
  id_animal          INT UNSIGNED NOT NULL,
  id_usuario_registra INT UNSIGNED NOT NULL,
  CONSTRAINT pk_nacimiento PRIMARY KEY (id_nacimiento),
  CONSTRAINT uq_nacimiento_animal UNIQUE (id_animal),
  CONSTRAINT fk_nacimiento_animal FOREIGN KEY (id_animal)
      REFERENCES animal (id_animal)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_nacimiento_usuario FOREIGN KEY (id_usuario_registra)
      REFERENCES usuario (id_usuario)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: vacunacion
-- ---------------------------------------------------------
CREATE TABLE vacunacion (
  id_vacunacion       INT UNSIGNED AUTO_INCREMENT,
  tipo_vacuna         VARCHAR(80) NOT NULL,
  fecha               DATE NOT NULL,
  id_animal           INT UNSIGNED NOT NULL,
  id_usuario_registra INT UNSIGNED NOT NULL,
  CONSTRAINT pk_vacunacion PRIMARY KEY (id_vacunacion),
  CONSTRAINT fk_vacunacion_animal FOREIGN KEY (id_animal)
      REFERENCES animal (id_animal)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_vacunacion_usuario FOREIGN KEY (id_usuario_registra)
      REFERENCES usuario (id_usuario)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: baja
-- ---------------------------------------------------------
CREATE TABLE baja (
  id_baja             INT UNSIGNED AUTO_INCREMENT,
  fecha               DATE NOT NULL,
  causa               VARCHAR(150) NOT NULL,
  id_animal           INT UNSIGNED NOT NULL,
  id_usuario_registra INT UNSIGNED NOT NULL,
  CONSTRAINT pk_baja PRIMARY KEY (id_baja),
  CONSTRAINT uq_baja_animal UNIQUE (id_animal),
  CONSTRAINT fk_baja_animal FOREIGN KEY (id_animal)
      REFERENCES animal (id_animal)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_baja_usuario FOREIGN KEY (id_usuario_registra)
      REFERENCES usuario (id_usuario)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: log_error
-- ---------------------------------------------------------
CREATE TABLE log_error (
  id_error     INT UNSIGNED AUTO_INCREMENT,
  codigo_error VARCHAR(10) NOT NULL,
  mensaje      VARCHAR(255) NOT NULL,
  fecha_hora   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_usuario   INT UNSIGNED NULL,
  CONSTRAINT pk_log_error PRIMARY KEY (id_error),
  CONSTRAINT fk_log_error_usuario FOREIGN KEY (id_usuario)
      REFERENCES usuario (id_usuario)
      ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Indices adicionales de apoyo a consultas frecuentes
-- ---------------------------------------------------------
CREATE INDEX ix_animal_lote        ON animal (id_lote);
CREATE INDEX ix_vacunacion_animal  ON vacunacion (id_animal);
CREATE INDEX ix_usuario_finca      ON usuario (id_finca);
