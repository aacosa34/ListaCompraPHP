<?php

require_once "../vendor/autoload.php";
include_once "../controller/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();


if(isset($_SESSION['email'])){

    $user = getUserByEmail($_SESSION['email']);

    $listasUser = getListas($user['IDUSUARIO']);

    if($_SERVER['REQUEST_METHOD'] == "GET"){
        
    }

    // foreach($listasUser as $row){
    //     $img[] = 'data: ' . $row['IMGTYPE'] . ';base64,' . base64_encode($row['IMGBINARY']);
    // }
}else{
    header("Location: /index.php");
}

echo $twig->render('listas.html', ['listas' => $listasUser,
                                   'filtros' => $_GET,
                                    'user' => $user]);


?>