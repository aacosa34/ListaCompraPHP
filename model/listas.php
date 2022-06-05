<?php

require_once "../vendor/autoload.php";
include_once "../controller/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();




if(isset($_SESSION['idusuario'])){

    $user = getUserById($_SESSION['idusuario']);

    // Verlo todo modo RW sin ser propietario

    $privilegio = Array();

    // Filtro de listas
    if($_SERVER['REQUEST_METHOD'] == "GET"){
        if(isset($_GET["propietario"])){
            $privilegio[] = 1;
        }
        if (isset($_GET["editable"])){
            $privilegio[] = 2;
        }
        if (isset($_GET["visible"])){
            $privilegio[] = 3;
        }

        // OBTENER EL TAMANIO TOTAL 
        
        // alfabeto - Ordeno por nombre de lista
        // fecha - ordeno por fecha de creacion en orden descendiente
        if ($_GET["orden"] == "alfabeto" && !isset($_GET["texto"])){
            $listasUser = getListasAlphabeticOrder($_SESSION['idusuario'], $privilegio);
        }
        else if ($_GET["orden"] == "alfabeto" && isset($_GET["texto"])){
            $listasUser = getListasAlphabeticOrderSearch($_SESSION['idusuario'], $privilegio, $_GET["texto"]);
        }
        else if ($_GET["orden"] == "fecha" && !isset($_GET["texto"])){
            $listasUser = getListasDateOrder($_SESSION['idusuario'], $privilegio);
        }
        else if ($_GET["orden"] == "fecha" && isset($_GET["texto"])){
            $listasUser = getListasDateOrderSearch($_SESSION['idusuario'], $privilegio, $_GET["texto"]);
        }
        else {
            // DEFAULT MODE
            $listasUser = getListasAlphabeticOrder($_SESSION['idusuario'], $privilegio);
        }
    }



    foreach($listasUser as $row){
        if($listaUser['IMGBINARY'] != NULL){
            $img[] = 'data: ' . $row['IMGTYPE'] . ';base64,' . base64_encode($row['IMGBINARY']);
        }
        else{
            $img[] = '/assets/noimage.png';
        }
    }

    
}else{
    header("Location: /index.php");
}

echo $twig->render('listas.html', ['listas'  => $listasUser,
                                   'src'     => $img,
                                   'filtros' => $_GET,
                                   'user'    => $user]);


?>