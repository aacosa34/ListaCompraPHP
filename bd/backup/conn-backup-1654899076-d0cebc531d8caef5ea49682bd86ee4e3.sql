DROP TABLE IF EXISTS GRUPOS;

CREATE TABLE `GRUPOS` (
  `IDLISTA` int(11) DEFAULT NULL,
  `IDUSUARIO` int(11) DEFAULT NULL,
  `PRIVILEGIOS` enum('Propietario','Editor','Lector') DEFAULT NULL,
  KEY `FK_GRUPOS_LISTAID` (`IDLISTA`),
  KEY `FK_GRUPOS_IDUSUARIO` (`IDUSUARIO`),
  CONSTRAINT `FK_GRUPOS_IDUSUARIO` FOREIGN KEY (`IDUSUARIO`) REFERENCES `USUARIOS` (`IDUSUARIO`),
  CONSTRAINT `FK_GRUPOS_LISTAID` FOREIGN KEY (`IDLISTA`) REFERENCES `LISTA` (`IDLISTA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO GRUPOS VALUES("1","2","Propietario");
INSERT INTO GRUPOS VALUES("2","3","Propietario");
INSERT INTO GRUPOS VALUES("3","3","Propietario");
INSERT INTO GRUPOS VALUES("4","3","Propietario");
INSERT INTO GRUPOS VALUES("5","3","Propietario");
INSERT INTO GRUPOS VALUES("6","3","Propietario");
INSERT INTO GRUPOS VALUES("7","5","Propietario");
INSERT INTO GRUPOS VALUES("1","4","Editor");
INSERT INTO GRUPOS VALUES("3","2","Editor");
INSERT INTO GRUPOS VALUES("1","3","Lector");
INSERT INTO GRUPOS VALUES("2","2","Lector");
INSERT INTO GRUPOS VALUES("3","4","Lector");
INSERT INTO GRUPOS VALUES("8","1","Propietario");



DROP TABLE IF EXISTS HISTORICO;

CREATE TABLE `HISTORICO` (
  `IDHISTORICO` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) DEFAULT NULL,
  `CANTIDAD` int(11) DEFAULT NULL,
  PRIMARY KEY (`IDHISTORICO`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO HISTORICO VALUES("1","N�mero de Listas Creadas:","8");
INSERT INTO HISTORICO VALUES("2","N�mero de Listas Activas:","8");
INSERT INTO HISTORICO VALUES("3","N�mero de Listas con Elementos pendientes de compra:","7");



DROP TABLE IF EXISTS HISTORICOPRODUCTOS;

CREATE TABLE `HISTORICOPRODUCTOS` (
  `IDHISTORICO` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) DEFAULT NULL,
  `CANTIDAD` int(11) DEFAULT NULL,
  PRIMARY KEY (`IDHISTORICO`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

INSERT INTO HISTORICOPRODUCTOS VALUES("1","Coliflor","14");
INSERT INTO HISTORICOPRODUCTOS VALUES("2","Naranja","12");
INSERT INTO HISTORICOPRODUCTOS VALUES("3","Lim�n","20");
INSERT INTO HISTORICOPRODUCTOS VALUES("4","Pi�a","6");
INSERT INTO HISTORICOPRODUCTOS VALUES("5","Zanahoria","15");
INSERT INTO HISTORICOPRODUCTOS VALUES("6","Pollo","7");
INSERT INTO HISTORICOPRODUCTOS VALUES("7","Queso","33");
INSERT INTO HISTORICOPRODUCTOS VALUES("8","Miel","15");
INSERT INTO HISTORICOPRODUCTOS VALUES("9","Chocolate","17");
INSERT INTO HISTORICOPRODUCTOS VALUES("10","Pasta","20");



DROP TABLE IF EXISTS LISTA;

CREATE TABLE `LISTA` (
  `IDLISTA` int(11) NOT NULL AUTO_INCREMENT,
  `IDPROPIETARIO` int(11) DEFAULT NULL,
  `NOMBRE` varchar(45) DEFAULT NULL,
  `DESCRIPCION` varchar(200) DEFAULT NULL,
  `FECHA` datetime DEFAULT NULL,
  `IMGTYPE` enum('image/jpeg','image/png','image/jpg') DEFAULT NULL,
  `IMGBINARY` longblob DEFAULT NULL,
  PRIMARY KEY (`IDLISTA`),
  KEY `FK_IDPROPIETARIO` (`IDPROPIETARIO`),
  CONSTRAINT `FK_IDPROPIETARIO` FOREIGN KEY (`IDPROPIETARIO`) REFERENCES `USUARIOS` (`IDUSUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

INSERT INTO LISTA VALUES("1","2","Lista1","Mi lista personal","2022-01-05 12:49:29","image/png",NULL);
INSERT INTO LISTA VALUES("2","3","Lista2","Lista de la compra","2022-02-05 17:49:29","image/png",NULL);
INSERT INTO LISTA VALUES("3","3","Lista3","Lista de cosas que faltan en casa","2021-03-05 17:49:29","image/png",NULL);
INSERT INTO LISTA VALUES("4","3","Lista4","Lista para los que vayan a comprar","2022-04-05 12:49:29","image/png",NULL);
INSERT INTO LISTA VALUES("5","3","Lista5","Lista del mercadona","2021-06-05 17:49:29","image/png",NULL);
INSERT INTO LISTA VALUES("6","3","Lista6","Lista para recordar cosas que comprar","2022-06-11 00:10:54","image/png",NULL);
INSERT INTO LISTA VALUES("7","5","Lista7","Lista de cosas varias","2022-06-05 12:49:29","image/png",NULL);
INSERT INTO LISTA VALUES("8","1","Lista de prueba","","2022-06-11 00:11:12","image/jpg","����\0JFIF\0\0\0\0\0\0��\0C\0		\n
%5����=���;��6�x��<qUM�i���J�⍙�~���㟵ZCs���Mo��*[Za30��IKr\0\0�g��V�Ķڅ�2LcR��%Iu��ހ}�D_c�:؏o$S��^6u��m�\\�I_�����|\0G��yςq���&��ܡ���ֽ�����v���G�h�Q�j�ғ�J�&����Æ#���1��,;E0#\n~!�G�Q�I7D޲�!v�R?�4�Չ������a�]��x>��<g=�RH��H|�a�X���u�qN���w��,V�LK$H�|��=9�D��n�m�kZ�&�v�S��$�Ɍ��pF@\'#���W���Kƥ
_״��#�{.5Y%������%H���L�{c���[i��Cef�����A����H����>،�.�E�\0�\0���Rk�)F��N����U[�j��|%���jPI�n22��{�ֱ��gH����b`#`Kc��Sm�͸����K8r��!l@�O��W��Z��wf�Ct�@��RxNO��s�A���GT�%��4�g�Ə4�R	�)0�[Ӄ��\0Ͻi$ҵUu/b��e-�F���Y����z�q��J��S��\0�u5������YO$�%�$�˿��=]1�x�t�����\"���V�$���VIE�8a�N{��D�ot���9�a��Ð9�n�\'޴2>n\'����Q$�q���4�W��uUY#������xlH�>����w���KO�/\".[G���^��M}�YT�0��8�/!����Uv�?s��[KB}��r��p�B�PF��$���A��m^	o���%��,�;O��w�*m(���)�b)Wb�Ȧ��$)*�?#��S�-�$��)�U6���\\
ʎ) Y&�s�ܕ���j�u;�m{�5��Y��8b\"IX	�@��+0�v���b}��U���[{�=1�#p,#!d\0�.rG�5G
���lpަ��i^E����H�߹��S���?Z��3�������1d�%��pq�|�M4��w���|��[b��F��g������Z01�2���r]�ڳz4v���7.�s���d�#��jKRO2[�F��2�#�=0=���<��R=%aQ/�-��RC�v���E�����]\"
�$�\0�8��Vz���[�E&�oʬ��i�Z>���^,1�l4����Rg�aq�rp͞�Ն�$���2c���!��m��R~�c�e1D�c&3 ,{�3��W�>�2O��*��9���n쬵+��ͧ��4��o���������Mb�#Ǭ���p��Z�xĀ�Lm�;�L�q��entW��[]ԯ�5�bTT\ns���5�Q�L���һ�
�=���M�\nYL��8��bW�/�������8�+��n�L�g�Œa2c�v�=+c��zsR򯂖|�q��s�@�V��~�z�<ְ]F�)gǸ�k����k&8����噯b��������J��N���5pT�,c�esZ&��nE�����)�K��g�3�o�*i�����,�t��t\nA���R�5I&���*SEu������4�G.w=˷|��5��9����O��(zu,�



DROP TABLE IF EXISTS LISTAPRODUCTOS;

CREATE TABLE `LISTAPRODUCTOS` (
  `IDLISTA` int(11) DEFAULT NULL,
  `IDPRODUCTO` int(11) DEFAULT NULL,
  `CANTIDAD` int(11) DEFAULT NULL,
  KEY `FK_LISTACOMPRA_LISTAID` (`IDLISTA`),
  KEY `FK_LISTACOMPRA_IDPRODUCTO` (`IDPRODUCTO`),
  CONSTRAINT `FK_LISTACOMPRA_IDPRODUCTO` FOREIGN KEY (`IDPRODUCTO`) REFERENCES `PRODUCTOS` (`IDPRODUCTO`),
  CONSTRAINT `FK_LISTACOMPRA_LISTAID` FOREIGN KEY (`IDLISTA`) REFERENCES `LISTA` (`IDLISTA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO LISTAPRODUCTOS VALUES("1","3","2");
INSERT INTO LISTAPRODUCTOS VALUES("1","8","1");
INSERT INTO LISTAPRODUCTOS VALUES("1","15","3");
INSERT INTO LISTAPRODUCTOS VALUES("2","1","2");
INSERT INTO LISTAPRODUCTOS VALUES("2","5","1");
INSERT INTO LISTAPRODUCTOS VALUES("2","15","5");
INSERT INTO LISTAPRODUCTOS VALUES("2","11","2");
INSERT INTO LISTAPRODUCTOS VALUES("2","8","2");
INSERT INTO LISTAPRODUCTOS VALUES("2","3","1");
INSERT INTO LISTAPRODUCTOS VALUES("2","4","3");
INSERT INTO LISTAPRODUCTOS VALUES("3","6","2");
INSERT INTO LISTAPRODUCTOS VALUES("3","9","1");
INSERT INTO LISTAPRODUCTOS VALUES("3","12","3");
INSERT INTO LISTAPRODUCTOS VALUES("3","7","2");
INSERT INTO LISTAPRODUCTOS VALUES("3","14","1");
INSERT INTO LISTAPRODUCTOS VALUES("4","16","1");
INSERT INTO LISTAPRODUCTOS VALUES("4","17","1");
INSERT INTO LISTAPRODUCTOS VALUES("5","11","10");
INSERT INTO LISTAPRODUCTOS VALUES("5","12","5");
INSERT INTO LISTAPRODUCTOS VALUES("5","13","8");
INSERT INTO LISTAPRODUCTOS VALUES("6","5","1");
INSERT INTO LISTAPRODUCTOS VALUES("6","2","2");
INSERT INTO LISTAPRODUCTOS VALUES("6","17","4");
INSERT INTO LISTAPRODUCTOS VALUES("6","13","2");
INSERT INTO LISTAPRODUCTOS VALUES("7","14","1");
INSERT INTO LISTAPRODUCTOS VALUES("7","12","5");
INSERT INTO LISTAPRODUCTOS VALUES("7","8","1");



DROP TABLE IF EXISTS LOG;

CREATE TABLE `LOG` (
  `IDLOG` int(11) NOT NULL AUTO_INCREMENT,
  `FECHA` datetime DEFAULT NULL,
  `DESCRIPCION` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`IDLOG`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;

INSERT INTO LOG VALUES("1","2022-06-11 00:10:54","Nuevo usuario dado de Alta: Admin");
INSERT INTO LOG VALUES("2","2022-06-11 00:10:54","Nuevo usuario dado de Alta: Juan");
INSERT INTO LOG VALUES("3","2022-06-11 00:10:54","Nuevo usuario dado de Alta: Maria");
INSERT INTO LOG VALUES("4","2022-06-11 00:10:54","Nuevo usuario dado de Alta: Ana");
INSERT INTO LOG VALUES("5","2022-06-11 00:10:54","Nuevo usuario dado de Alta: Pepe");
INSERT INTO LOG VALUES("6","2022-06-11 00:10:54","Creaci�n de lista nueva: Lista1");
INSERT INTO LOG VALUES("7","2022-06-11 00:10:54","Creaci�n de lista nueva: Lista2");
INSERT INTO LOG VALUES("8","2022-06-11 00:10:54","Creaci�n de lista nueva: Lista3");
INSERT INTO LOG VALUES("9","2022-06-11 00:10:54","Creaci�n de lista nueva: Lista4");
INSERT INTO LOG VALUES("10","2022-06-11 00:10:54","Creaci�n de lista nueva: Lista5");
INSERT INTO LOG VALUES("11","2022-06-11 00:10:54","Creaci�n de lista nueva: Lista6");
INSERT INTO LOG VALUES("12","2022-06-11 00:10:54","Creaci�n de lista nueva: Lista7");
INSERT INTO LOG VALUES("13","2022-06-11 00:10:54","Inserci�n de producto en la lista: 1 Producto: Pan de molde");
INSERT INTO LOG VALUES("14","2022-06-11 00:10:54","Inserci�n de producto en la lista: 1 Producto: Gel de ducha");
INSERT INTO LOG VALUES("15","2022-06-11 00:10:54","Inserci�n de producto en la lista: 1 Producto: Cl�nex");
INSERT INTO LOG VALUES("16","2022-06-11 00:10:54","Inserci�n de producto en la lista: 2 Producto: Agua");
INSERT INTO LOG VALUES("17","2022-06-11 00:10:54","Inserci�n de producto en la lista: 2 Producto: Cereales chocapic");
INSERT INTO LOG VALUES("18","2022-06-11 00:10:54","Inserci�n de producto en la lista: 2 Producto: Cl�nex");
INSERT INTO LOG VALUES("19","2022-06-11 00:10:54","Inserci�n de producto en la lista: 2 Producto: Platanos");
INSERT INTO LOG VALUES("20","2022-06-11 00:10:54","Inserci�n de producto en la lista: 2 Producto: Gel de ducha");
INSERT INTO LOG VALUES("21","2022-06-11 00:10:54","Inserci�n de producto en la lista: 2 Producto: Pan de molde");
INSERT INTO LOG VALUES("22","2022-06-11 00:10:54","Inserci�n de producto en la lista: 2 Producto: Colacao grande");
INSERT INTO LOG VALUES("23","2022-06-11 00:10:54","Inserci�n de producto en la lista: 3 Producto: Bollicao");
INSERT INTO LOG VALUES("24","2022-06-11 00:10:54","Inserci�n de producto en la lista: 3 Producto: Huevos");
INSERT INTO LOG VALUES("25","2022-06-11 00:10:54","Inserci�n de producto en la lista: 3 Producto: Manzanas");
INSERT INTO LOG VALUES("26","2022-06-11 00:10:54","Inserci�n de producto en la lista: 3 Producto: Galletas mar�a");
INSERT INTO LOG VALUES("27","2022-06-11 00:10:54","Inserci�n de producto en la lista: 3 Producto: Jab�n de manos");
INSERT INTO LOG VALUES("28","2022-06-11 00:10:54","Inserci�n de producto en la lista: 4 Producto: Suavizante");
INSERT INTO LOG VALUES("29","2022-06-11 00:10:54","Inserci�n de producto en la lista: 4 Producto: Detergente");
INSERT INTO LOG VALUES("30","2022-06-11 00:10:54","Inserci�n de producto en la lista: 5 Producto: Platanos");
INSERT INTO LOG VALUES("31","2022-06-11 00:10:54","Inserci�n de producto en la lista: 5 Producto: Manzanas");
INSERT INTO LOG VALUES("32","2022-06-11 00:10:54","Inserci�n de producto en la lista: 5 Producto: Kiwis");
INSERT INTO LOG VALUES("33","2022-06-11 00:10:54","Inserci�n de producto en la lista: 6 Producto: Cereales chocapic");
INSERT INTO LOG VALUES("34","2022-06-11 00:10:54","Inserci�n de producto en la lista: 6 Producto: Leche");
INSERT INTO LOG VALUES("35","2022-06-11 00:10:54","Inserci�n de producto en la lista: 6 Producto: Detergente");
INSERT INTO LOG VALUES("36","2022-06-11 00:10:54","Inserci�n de producto en la lista: 6 Producto: Kiwis");
INSERT INTO LOG VALUES("37","2022-06-11 00:10:54","Inserci�n de producto en la lista: 7 Producto: Jab�n de manos");
INSERT INTO LOG VALUES("38","2022-06-11 00:10:54","Inserci�n de producto en la lista: 7 Producto: Manzanas");
INSERT INTO LOG VALUES("39","2022-06-11 00:10:54","Inserci�n de producto en la lista: 7 Producto: Gel de ducha");
INSERT INTO LOG VALUES("40","2022-06-11 00:11:12","Creaci�n de lista nueva: Lista de prueba");



DROP TABLE IF EXISTS PRODUCTOS;

CREATE TABLE `PRODUCTOS` (
  `IDPRODUCTO` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(45) DEFAULT NULL,
  `DESCRIPCION` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`IDPRODUCTO`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

INSERT INTO PRODUCTOS VALUES("1","Agua",NULL);
INSERT INTO PRODUCTOS VALUES("2","Leche",NULL);
INSERT INTO PRODUCTOS VALUES("3","Pan de molde",NULL);
INSERT INTO PRODUCTOS VALUES("4","Colacao grande",NULL);
INSERT INTO PRODUCTOS VALUES("5","Cereales chocapic",NULL);
INSERT INTO PRODUCTOS VALUES("6","Bollicao",NULL);
INSERT INTO PRODUCTOS VALUES("7","Galletas mar�a",NULL);
INSERT INTO PRODUCTOS VALUES("8","Gel de ducha",NULL);
INSERT INTO PRODUCTOS VALUES("9","Huevos",NULL);
INSERT INTO PRODUCTOS VALUES("10","Caf�",NULL);
INSERT INTO PRODUCTOS VALUES("11","Platanos",NULL);
INSERT INTO PRODUCTOS VALUES("12","Manzanas",NULL);
INSERT INTO PRODUCTOS VALUES("13","Kiwis",NULL);
INSERT INTO PRODUCTOS VALUES("14","Jab�n de manos",NULL);
INSERT INTO PRODUCTOS VALUES("15","Cl�nex",NULL);
INSERT INTO PRODUCTOS VALUES("16","Suavizante",NULL);
INSERT INTO PRODUCTOS VALUES("17","Detergente",NULL);



DROP TABLE IF EXISTS USUARIOS;

CREATE TABLE `USUARIOS` (
  `IDUSUARIO` int(11) NOT NULL AUTO_INCREMENT,
  `DNI` varchar(9) DEFAULT NULL,
  `NOMBRE` varchar(50) DEFAULT NULL,
  `APELLIDOS` varchar(100) DEFAULT NULL,
  `TELEFONO` varchar(20) DEFAULT NULL,
  `EMAIL` varchar(200) DEFAULT NULL,
  `PASSWORD` varchar(45) DEFAULT NULL,
  `FNAC` date DEFAULT NULL,
  `SEXO` enum('Masculino','Femenino') DEFAULT NULL,
  `ROL` enum('Administrador','Usuario') DEFAULT NULL,
  `ESTADO` enum('Activo','Inactivo') DEFAULT NULL,
  `IMGTYPE` enum('image/jpeg','image/png','image/jpg') DEFAULT NULL,
  `IMGBINARY` longblob DEFAULT NULL,
  `LASTLOGIN` datetime DEFAULT NULL,
  PRIMARY KEY (`IDUSUARIO`),
  UNIQUE KEY `DNI` (`DNI`),
  UNIQUE KEY `TELEFONO` (`TELEFONO`),
  UNIQUE KEY `EMAIL` (`EMAIL`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO USUARIOS VALUES("1","50505050A","Admin","Admin","+34 678345645","admin@void.ugr.es","1234","1977-01-01","Masculino","Administrador","Activo","image/png",NULL,"2022-06-11 00:10:54");
INSERT INTO USUARIOS VALUES("2","51505050A","Juan","Martin","+34 678345245","juan@void.ugr.es","1234","1977-01-01","Masculino","Administrador","Activo","image/png","�PNG
INSERT INTO USUARIOS VALUES("3","51505850A","Maria","Jimenez","+34 618345746","maria@void.ugr.es","1234","1977-01-01","Femenino","Usuario","Inactivo","image/png","�PNG
!�$�&;&��9�)=�ˣ6L����!=pd�E���U�ڬ�$yI�pp��jm5��~��1�kU}u�~���\\��`�+�Y:��OHd��L�Y:Z��(
INSERT INTO USUARIOS VALUES("4","50525050A","Ana","Fernandez","+34 678323045","ana@void.ugr.es","1234","1977-01-01","Femenino","Usuario","Activo","image/png","�PNG
INSERT INTO USUARIOS VALUES("5","51505150A","Pepe","Perez","+34 678305945","pepe@void.ugr.es","1234","1977-01-01","Masculino","Usuario","Activo","image/png","�PNG


