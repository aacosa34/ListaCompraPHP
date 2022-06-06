<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);



if(isset($_SESSION['idusuario'])){

    $user = getUserById($_SESSION['idusuario']);

    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $lista = getListaById($_GET['idlista'], $_SESSION['idusuario']);
    }
    
}

echo $twig->render('vista_lista.html', ['lista' => $lista,
                                         'user' => $user]);
?>