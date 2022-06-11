<?php
require_once "vendor/autoload.php";
include_once "model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('view');
$twig = new \Twig\Environment($loader);

// Comprobamos si la base de datos necesita ser set
DB_init();

$estadisticas = getEstadisticas();
$productos = getProductosComprados();

$user='';

session_start();

// Si la sesion ya ha sido iniciada, cogemos el rol del usuario
if(isset($_SESSION['idusuario'])){
    $id = $_SESSION['idusuario'];
    $user = getUserById($id);
}

echo $twig->render('portada.html', ['estadisticas' => $estadisticas,
                                    'productos_comprados' => $productos,
                                    'user' => $user]);

?>