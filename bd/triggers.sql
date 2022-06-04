USE LISTACOMPRA;
DELIMITER //

CREATE OR REPLACE TRIGGER LOGIN_USER
AFTER UPDATE ON USUARIOS FOR EACH ROW BEGIN
  DECLARE VARIDUSUARIO INT;
  SET VARIDUSUARIO = NEW.IDUSUARIO;

  IF VARIDUSUARIO IN (SELECT IDUSUARIO FROM USUARIOS WHERE IDUSUARIO = VARIDUSUARIO) THEN
    INSERT INTO LOG (FECHA, DESCRIPCION) VALUES (NOW(), CONCAT("El usuario: ", (SELECT NOMBRE FROM USUARIOS WHERE IDUSUARIO = VARIDUSUARIO), " se ha autenticado correctamente"));
  END IF;
END //

/* ACCIONES CON LISTAS*/
/*Al crear una nueva lista se indica en el log y se actualiza el historico*/
CREATE OR REPLACE TRIGGER NEW_LISTA
AFTER INSERT ON LISTA FOR EACH ROW BEGIN
    INSERT INTO LOG (FECHA, DESCRIPCION) VALUES (NOW(), CONCAT("Creación de lista nueva: ", NEW.NOMBRE));
    UPDATE HISTORICO SET CANTIDAD = CANTIDAD + 1 WHERE IDHISTORICO = 1;
    UPDATE HISTORICO SET CANTIDAD = CANTIDAD + 1 WHERE IDHISTORICO = 2;
END //

/*
Al borrar una lista se indica en el log y se actualiza el historico quitando solo las listas activas

Tb se registra el cambio de la cantidad de Listas con productos si esta lo tuviera
*/
CREATE OR REPLACE TRIGGER DEL_LISTA
AFTER DELETE ON LISTA FOR EACH ROW BEGIN
    DECLARE COUNTPROD INT;
    DECLARE COUNTGRUPOS INT;

    SET COUNTPROD = (SELECT COUNT(IDLISTA) FROM LISTAPRODUCTOS WHERE IDLISTA=OLD.IDLISTA);
    SET COUNTGRUPOS = (SELECT COUNT(IDLISTA) FROM GRUPOS WHERE IDLISTA=OLD.IDLISTA);

    /* Registramos que se ha borrado una lista ACTIVA*/
    INSERT INTO LOG (FECHA, DESCRIPCION) VALUES (NOW(), CONCAT("Borrado de lista: ", OLD.NOMBRE));
    /* Borramos esta lista del historico listas activas*/
    UPDATE HISTORICO SET CANTIDAD = CANTIDAD - 1 WHERE IDHISTORICO = 2;

    IF COUNTPROD >= 1 THEN
      /* Limpiamos la tabla relacionar todas las referencias de la lista*/
      DELETE FROM LISTAPRODUCTOS WHERE IDLISTA = OLD.IDLISTA;
      /* Actualizamos el registro que indica la cantidad de listas activas que tienen productos*/
      UPDATE HISTORICO SET CANTIDAD = CANTIDAD - 1 WHERE IDHISTORICO = 3;
    END IF;

    /* Borramos los registros de los GRUPOS que pueden acceder a la lista borrada*/
    IF COUNTGRUPOS >= 1 THEN
      DELETE FROM GRUPOS WHERE IDLISTA = OLD.IDLISTA;
    END IF;
END //

/*Al insertar un producto conocido en una lista conocida, se cuenta cuantas listas tienen productos sin importar que cantidad tenga*/
CREATE OR REPLACE TRIGGER LISTA_CON_PRODUCTOS
AFTER INSERT ON LISTAPRODUCTOS FOR EACH ROW BEGIN
    DECLARE COUNTPROD INT;
    SET COUNTPROD = (SELECT COUNT(IDPRODUCTO) FROM LISTAPRODUCTOS WHERE IDLISTA=NEW.IDLISTA);

    INSERT INTO LOG (FECHA, DESCRIPCION) VALUES (NOW(), CONCAT("Inserción de producto en la lista: ", NEW.IDLISTA, " Producto: ", (SELECT NOMBRE FROM PRODUCTOS WHERE IDPRODUCTO = NEW.IDPRODUCTO)));

    /* Actualizamos indice de listas activas con al menos 1 producto pendiente */
    IF COUNTPROD = 1 THEN
      UPDATE HISTORICO SET CANTIDAD = CANTIDAD + 1 WHERE IDHISTORICO = 3;
    END IF;
END //


/* OPERACIONES CON USUARIOS */

/*Al insertar un producto conocido en una lista conocida, se cuenta cuantas listas tienen productos sin importar que cantidad tenga*/
CREATE OR REPLACE TRIGGER NEW_USER
AFTER INSERT ON USUARIOS FOR EACH ROW BEGIN
    INSERT INTO LOG (FECHA, DESCRIPCION) VALUES (NOW(), CONCAT("Nuevo usuario dado de Alta: ", NEW.NOMBRE));
END //

CREATE OR REPLACE TRIGGER UPDATE_USER
AFTER UPDATE ON USUARIOS FOR EACH ROW BEGIN
    INSERT INTO LOG (FECHA, DESCRIPCION) VALUES (NOW(), CONCAT("Información del usuario cambiada:", (SELECT NOMBRE FROM USUARIOS WHERE IDUSUARIO = NEW.IDUSUARIO)));
END //

/* Tenemos que borrar la relacion de los grupos de las listas activas*/

CREATE OR REPLACE TRIGGER DEL_USER
AFTER DELETE ON USUARIOS FOR EACH ROW BEGIN
  DECLARE COUNTLIST INT;
  SET COUNTLIST = (SELECT COUNT(IDLISTA) FROM GRUPOS WHERE IDUSUARIO = OLD.IDUSUARIO);

  INSERT INTO LOG (FECHA, DESCRIPCION) VALUES (NOW(), CONCAT("Usuario borrado ID: ", OLD.IDUSUARIO, " Nombre: ", OLD.NOMBRE));

  /* Si el usuario pertenece a 1 o mas listas compartidas o no se le borra el acceso*/
  IF COUNTLIST >= 1 THEN
    DELETE FROM GRUPOS WHERE IDUSUARIO = OLD.IDUSUARIO;
  END IF;
END //
