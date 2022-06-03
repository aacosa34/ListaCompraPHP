<?php
require_once "vendor/autoload.php";
// include("bd.php");

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// $estadisticas = getEstadisticas();
// $productos = getProductosComprados();

session_start();

// Si la sesion ya ha sido iniciada, cogemos el rol del usuario
if(isset($_SESSION['iduser'])){
    $user = getUser($_SESSION['iduser']);
}

echo $twig->render('portada.html', ['estadisticas' => $estadisticas,
                                    'productos_comprados' => $productos]);

?>