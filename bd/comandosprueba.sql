/*REGISTRO DE  LISTAS*/

INSERT INTO LISTACOMPRA.LISTA(NOMBRE,DESCRIPCION,FECHA,IMGTYPE,IMGBINARY) VALUES ("Lista1","Descripcion",NOW(),"image/png",LOAD_FILE("/var/www/html/segmentar-listas.png"));
INSERT INTO LISTACOMPRA.LISTA(NOMBRE,DESCRIPCION,FECHA,IMGTYPE,IMGBINARY) VALUES ("Lista2","Descripcion",NOW(),"image/jpg",LOAD_FILE("/var/www/html/lista-seo.jpg"));

/* REGISTRO DE PRODUCTOS */
INSERT INTO LISTACOMPRA.PRODUCTOS(NOMBRE, DESCRIPCION, IMGTYPE, IMGBINARY) VALUES ("Fanta", "La fanta");
INSERT INTO LISTACOMPRA.PRODUCTOS(NOMBRE, DESCRIPCION, IMGTYPE, IMGBINARY) VALUES ("Cocacola", "La cocacola");

/* REGISTRO DE PRODUCTOS AÑADIDOS A UNA LISTA*/
INSERT INTO LISTACOMPRA.LISTAPRODUCTOS VALUES(1,1,10);
INSERT INTO LISTACOMPRA.LISTAPRODUCTOS VALUES(2,1,20);
INSERT INTO LISTACOMPRA.LISTAPRODUCTOS VALUES(1,2,100);

/* A PHP metodo de insercion de un usuario nuevo*/
INSERT INTO USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('50505050A', 'Prueba', 'Apellidos', '+34 678345645', 'email@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Administrador', 'Activo', 'image/png', LOAD_FILE('/var/www/html/img/usuario.png'), NOW());
INSERT INTO USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51505050A', 'Prueba', 'Apellidos', '+34 178345645', 'email2@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Usuario', 'Activo', 'image/png', LOAD_FILE('/var/www/html/img/usuario.png'), NOW());

/* A PHP AUTENTICACION, y LOGOUT*/
SELECT COUNT(IDUSUARIO) FROM USUARIOS WHERE EMAIL = VARIABLEMAILPHP AND PASSWORD = VARIABLEPASSWORDPHP;

  /*A PHP, cuando un usuario se identifica correctamente con el comando anterior se lanza un UPDATE de la fecha de autenticación*/
  UPDATE USUARIOS SET LASTLOGIN=NOW() WHERE IDUSUARIO=1;

  /* Si falla el login por no existir el usuario Registramos la ip del intento Fallido*/
  INSERT INTO LOG (FECHA, DESCRIPCION) VALUES (NOW(), CONCAT("Intento Fallido de inicio de sesion", $_SERVER['REMOTE_ADDR']);

  /* Cierre de Sesion */
  INSERT INTO LOG (FECHA, DESCRIPCION) VALUES (NOW(), CONCAT("Cierre de sesion del usuario:", (SELECT NOMBRE FROM USUARIOS WHERE IDUSUARIO = PHPIDUSUARIO));



En el 50-server.cnf debemos poner esto para que la BD pueda cargar las fotos y no aparezca como NULL
secure-file-priv=/var/www/html
