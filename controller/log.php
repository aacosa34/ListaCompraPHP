<?php

require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    if($user['ROL'] === "Administrador"){
        $log = getLog();
    }
    else{
        header("Location: /~adrianpedro2122/proyecto/index.php");
    }
}

echo $twig->render('log.html', ['user' => $user,
                                'log' => $log]);

?>