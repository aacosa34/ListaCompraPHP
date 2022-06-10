-- DROP DATABASE IF EXISTS adrianpedro2122;
-- CREATE DATABASE adrianpedro2122;

-- DROP USER IF EXISTS 'adrianpedro2122'@'localhost';
-- CREATE USER 'adrianpedro2122'@'localhost' IDENTIFIED BY 'W1F7SMzT';
-- GRANT ALL PRIVILEGES ON adrianpedro2122.* TO 'adrianpedro2122'@'localhost';
-- FLUSH PRIVILEGES;

-- DROP TABLE IF EXISTS adrianpedro2122.USUARIOS;

DROP TABLE IF EXISTS adrianpedro2122.GRUPOS;
DROP TABLE IF EXISTS adrianpedro2122.LISTAPRODUCTOS;
DROP TABLE IF EXISTS adrianpedro2122.LISTA;


CREATE OR REPLACE TABLE adrianpedro2122.USUARIOS (
  IDUSUARIO INT NOT NULL AUTO_INCREMENT,
  DNI VARCHAR(9) UNIQUE,
  NOMBRE VARCHAR(50),
  APELLIDOS VARCHAR(100),
  TELEFONO VARCHAR(20) UNIQUE,
  EMAIL VARCHAR(200) UNIQUE,
  PASSWORD VARCHAR(45),
  FNAC DATE,
  SEXO ENUM('Masculino','Femenino'),
  ROL ENUM('Administrador','Usuario'),
  ESTADO ENUM('Activo','Inactivo'),
  IMGTYPE ENUM('image/jpeg', 'image/png', 'image/jpg'),
  IMGBINARY LONGBLOB,
  LASTLOGIN DATETIME,

  CONSTRAINT PK_IDUSUARIO PRIMARY KEY(IDUSUARIO)
);

-- DROP TABLE IF EXISTS adrianpedro2122.LISTA;
CREATE OR REPLACE TABLE adrianpedro2122.LISTA (
  IDLISTA INT NOT NULL AUTO_INCREMENT,
  IDPROPIETARIO INT,
  NOMBRE VARCHAR (45),
  DESCRIPCION VARCHAR(200),
  FECHA DATETIME,
  IMGTYPE ENUM('image/jpeg', 'image/png', 'image/jpg'),
  IMGBINARY LONGBLOB,

  CONSTRAINT PK_IDLISTA PRIMARY KEY(IDLISTA),
  CONSTRAINT FK_IDPROPIETARIO FOREIGN KEY(IDPROPIETARIO) REFERENCES USUARIOS(IDUSUARIO)
);

-- DROP TABLE IF EXISTS adrianpedro2122.PRODUCTOS;
CREATE OR REPLACE TABLE adrianpedro2122.PRODUCTOS (
  IDPRODUCTO INT NOT NULL AUTO_INCREMENT,
  NOMBRE VARCHAR(45),
  DESCRIPCION VARCHAR(45),

  CONSTRAINT PK_IDPRODUCTO PRIMARY KEY(IDPRODUCTO)
);


-- DROP TABLE IF EXISTS adrianpedro2122.LISTAPRODUCTOS;
CREATE OR REPLACE TABLE adrianpedro2122.LISTAPRODUCTOS (
  IDLISTA INT,
  IDPRODUCTO INT,
  CANTIDAD INT,

  CONSTRAINT FK_LISTACOMPRA_LISTAID FOREIGN KEY(IDLISTA) REFERENCES LISTA(IDLISTA),
  CONSTRAINT FK_LISTACOMPRA_IDPRODUCTO FOREIGN KEY(IDPRODUCTO) REFERENCES PRODUCTOS(IDPRODUCTO)
);

-- DROP TABLE IF EXISTS adrianpedro2122.GRUPOS;
CREATE OR REPLACE TABLE adrianpedro2122.GRUPOS (
  IDLISTA INT,
  IDUSUARIO INT,
  PRIVILEGIOS ENUM('Propietario','Editor','Lector'),

  CONSTRAINT FK_GRUPOS_LISTAID FOREIGN KEY(IDLISTA) REFERENCES LISTA(IDLISTA),
  CONSTRAINT FK_GRUPOS_IDUSUARIO FOREIGN KEY(IDUSUARIO) REFERENCES USUARIOS(IDUSUARIO)
);


/*Tablas de log*/
-- DROP TABLE IF EXISTS adrianpedro2122.LOG;
CREATE OR REPLACE TABLE adrianpedro2122.LOG(
  IDLOG INT NOT NULL AUTO_INCREMENT,
  FECHA DATETIME,
  DESCRIPCION VARCHAR(200),

  CONSTRAINT PK_IDLOG PRIMARY KEY(IDLOG)
);


-- DROP TABLE IF EXISTS adrianpedro2122.HISTORICO;
CREATE OR REPLACE TABLE adrianpedro2122.HISTORICO (
  IDHISTORICO INT NOT NULL AUTO_INCREMENT,
  NOMBRE VARCHAR(200),
  CANTIDAD INT,

  CONSTRAINT PK_IDHISTORICO PRIMARY KEY(IDHISTORICO)
);

-- INSERT INTO adrianpedro2122.HISTORICO (NOMBRE, CANTIDAD) VALUES ('Número de Listas Creadas:', 0);
-- INSERT INTO adrianpedro2122.HISTORICO (NOMBRE, CANTIDAD) VALUES ('Número de Listas Activas:', 0);
-- INSERT INTO adrianpedro2122.HISTORICO (NOMBRE, CANTIDAD) VALUES ('Número de Listas con Elementos pendientes de compra:', 0);

-- Tabla de productos comprados en historico

-- DROP TABLE IF EXISTS adrianpedro2122.HISTORICOPRODUCTOS;
CREATE OR REPLACE TABLE adrianpedro2122.HISTORICOPRODUCTOS (
  IDHISTORICO INT NOT NULL AUTO_INCREMENT,
  NOMBRE VARCHAR(200),
  CANTIDAD INT,

  CONSTRAINT PK_IDHISTORICO PRIMARY KEY(IDHISTORICO)
);

