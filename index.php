<?php
require_once "vendor/autoload.php";
include("model/bd.php");

$loader = new \Twig\Loader\FilesystemLoader('view');
$twig = new \Twig\Environment($loader);

$estadisticas = getEstadisticas();
$productos = getProductosComprados();

session_start();
 
// Si la sesion ya ha sido iniciada, cogemos el rol del usuario
if(isset($_SESSION['iduser'])){
    $user = getUser($_SESSION['email']);
}

echo $twig->render('portada.html', ['estadisticas' => $estadisticas,
                                    'productos_comprados' => $productos,
                                    'user' => $user]);

?>