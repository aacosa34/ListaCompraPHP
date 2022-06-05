<?php
require_once "vendor/autoload.php";
include_once "controller/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('view');
$twig = new \Twig\Environment($loader);

$estadisticas = getEstadisticas();
$productos = getProductosComprados();

$user='';

session_start();
 
// Si la sesion ya ha sido iniciada, cogemos el rol del usuario
if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];
    $user = getUserByEmail($email);

    // [0] => Array ( [IDUSUARIO] => 1 [DNI] => 50505050A [NOMBRE] => Prueba [APELLIDOS] => Apellidos [TELEFONO] => +34 678345645 [EMAIL] => email@pruebas.com [PASSWORD] => password [FNAC] => 1977-01-01 [SEXO] => Masculino [ROL] => Administrador [ESTADO] => Activo [IMGTYPE] => image/jpg [IMGBINARY] => [LASTLOGIN] => 2022-06-04 10:36:11 ) [1] => Array ( [IDUSUARIO] => 2 [DNI] => 51505050A [NOMBRE] => Prueba2 [APELLIDOS] => Apellidos [TELEFONO] => +34 178345645 [EMAIL] => email2@pruebas.com [PASSWORD] => password [FNAC] => 1977-01-01 [SEXO] => Masculino [ROL] => Usuario [ESTADO] => Activo [IMGTYPE] => image/jpg [IMGBINARY] => [LASTLOGIN] => 2022-06-04 10:36:11
}

echo $twig->render('portada.html', ['estadisticas' => $estadisticas,
                                    'productos_comprados' => $productos,
                                    'user' => $user]);

?>