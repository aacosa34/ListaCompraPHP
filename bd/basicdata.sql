/*REGISTRO DE  LISTAS*/
INSERT INTO LISTACOMPRA.LISTA(NOMBRE,DESCRIPCION,FECHA,IMGTYPE,IMGBINARY) VALUES ("ALista1","Descripcion","2022-06-05 12:49:29","image/png",LOAD_FILE("/var/upload/listas/listas.png"));
INSERT INTO LISTACOMPRA.LISTA(NOMBRE,DESCRIPCION,FECHA,IMGTYPE,IMGBINARY) VALUES ("Lista2","Descripcion","2022-06-05 17:49:29","image/png",LOAD_FILE("/var/upload/listas/listas.png"));
INSERT INTO LISTACOMPRA.LISTA(NOMBRE,DESCRIPCION,FECHA,IMGTYPE,IMGBINARY) VALUES ("ZLista3","Descripcion","2021-06-05 17:49:29","image/png",LOAD_FILE("/home/var/upload/listas/listas.png"));


/* REGISTRO DE PRODUCTOS */
INSERT INTO LISTACOMPRA.PRODUCTOS(NOMBRE, DESCRIPCION) VALUES ("Fanta", "La fanta");
INSERT INTO LISTACOMPRA.PRODUCTOS(NOMBRE, DESCRIPCION) VALUES ("Cocacola", "La cocacola");

/* REGISTRO DE PRODUCTOS AÑADIDOS A UNA LISTA*/
INSERT INTO LISTACOMPRA.LISTAPRODUCTOS VALUES(1,1,10);
INSERT INTO LISTACOMPRA.LISTAPRODUCTOS VALUES(2,1,20);
INSERT INTO LISTACOMPRA.LISTAPRODUCTOS VALUES(1,2,100);

/* Insercion de usuarios */
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('50505050A', 'Prueba0', 'Apellidos', '+34 678345645', 'email@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Administrador', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuarios/usuarios.jpg'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51505050A', 'Prueba2', 'Apellidos', '+34 178345245', 'email2@pruebas.com', 'password', '1977/01/01', 'Femenino', 'Usuario', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51505850A', 'Prueba3', 'Apellidos', '+34 118345746', 'email3@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Usuario', 'Inactivo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('50525050A', 'Prueba4', 'Apellidos', '+34 678323045', 'email4@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Administrador', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuarios/usuarios.jpg'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51505150A', 'Prueba5', 'Apellidos', '+34 178305945', 'email5@pruebas.com', 'password', '1977/01/01', 'Femenino', 'Usuario', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51505250A', 'Prueba6', 'Apellidos', '+34 168325846', 'email6@pruebas.com', 'password', '1977/01/01', 'Femenino', 'Usuario', 'Inactivo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('50505350A', 'Prueba7', 'Apellidos', '+34 678335645', 'email7@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Administrador', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuarios/usuarios.jpg'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51505450A', 'Prueba8', 'Apellidos', '+34 158375545', 'email8@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Usuario', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51505650A', 'Prueba9', 'Apellidos', '+34 148395246', 'email9@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Usuario', 'Inactivo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('50545050A', 'Prueba10', 'Apellidos', '+34 278345645', 'email10@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Administrador', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuarios/usuarios.jpg'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51567050A', 'Prueba11', 'Apellidos', '+34 278345245', 'email11@pruebas.com', 'password', '1977/01/01', 'Femenino', 'Usuario', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51555850A', 'Prueba12', 'Apellidos', '+34 218345746', 'email12@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Usuario', 'Inactivo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('50545051A', 'Prueba13', 'Apellidos', '+34 278343045', 'email13@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Administrador', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuarios/usuarios.jpg'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51535152A', 'Prueba14', 'Apellidos', '+34 278305945', 'email14@pruebas.com', 'password', '1977/01/01', 'Femenino', 'Usuario', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51525253A', 'Prueba15', 'Apellidos', '+34 268325846', 'email15@pruebas.com', 'password', '1977/01/01', 'Femenino', 'Usuario', 'Inactivo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('50515354A', 'Prueba16', 'Apellidos', '+34 278335645', 'email16@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Administrador', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuarios/usuarios.jpg'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51512455A', 'Prueba17', 'Apellidos', '+34 258275545', 'email17@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Usuario', 'Activo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());
INSERT INTO LISTACOMPRA.USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY, LASTLOGIN) VALUES  ('51522656A', 'Prueba18', 'Apellidos', '+34 248395246', 'email18@pruebas.com', 'password', '1977/01/01', 'Masculino', 'Usuario', 'Inactivo', 'image/png', LOAD_FILE('/var/upload/usuario.png'), NOW());


/* Asociacion de USUARIOS y LISTA*/
/* Los Editores solo tienen permiso para aniadir mas productos a las listas*/
INSERT INTO LISTACOMPRA.GRUPOS VALUES (1,1,'Propietario');
INSERT INTO LISTACOMPRA.GRUPOS VALUES (1,2,'Lector');

INSERT INTO LISTACOMPRA.GRUPOS VALUES (2,1,'Editor');
INSERT INTO LISTACOMPRA.GRUPOS VALUES (2,2,'Propietario');

INSERT INTO LISTACOMPRA.GRUPOS VALUES (3,1,'Lector');
INSERT INTO LISTACOMPRA.GRUPOS VALUES (3,2,'Propietario');
