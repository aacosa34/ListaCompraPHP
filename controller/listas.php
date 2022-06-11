<?php

require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    // Cleanning on bad exit
    unset($_COOKIE['validado']);
    unset($_COOKIE['nombrefoto']);

    // Verlo todo modo RW sin ser propietario
    $privilegio = Array();

    $listasUser = Array();

    // Filtro de listas
    if($_SERVER['REQUEST_METHOD'] == "GET"){
        if(!empty($_GET["propietario"])){
            $privilegio[] = 1;
        }
        if (!empty($_GET["editable"])){
            $privilegio[] = 2;
        }
        if (!empty($_GET["visible"])){
            $privilegio[] = 3;
        }

        // OBTENER EL TAMANIO TOTAL FUNCTION y Variables de paginacion
        $tamanio = getSizeOfListas($_SESSION['idusuario']);

        $pag_actual = 1;
        $offset = 3;
        $ultima_pag = ceil($tamanio/$offset);


        // alfabeto - Ordeno por nombre de lista
        // fecha - ordeno por fecha de creacion en orden descendiente
        if (isset($_GET['pagina'])){
            $pag_actual = $_GET['pagina'];
        }

        if (isset($_GET['orden']) && $_GET["orden"] == "alfabeto" && empty($_GET["texto"])){
            $ultima_pag = ceil(getSizeOfListasAlphabeticOrder($_SESSION['idusuario'], $privilegio) / $offset);

            if($pag_actual > $ultima_pag){
                $pag_actual = 1;
            }

            if ($ultima_pag == 0){
                $ultima_pag = $pag_actual;
            }

            $listasUser = getListasAlphabeticOrder($_SESSION['idusuario'], $privilegio, $pag_actual);
        }
        else if (isset($_GET['orden']) && $_GET["orden"] == "alfabeto" && !empty($_GET["texto"])){
            $ultima_pag = ceil(getSizeOfListasAlphabeticOrderSearch($_SESSION['idusuario'], $privilegio, $_GET["texto"]) / $offset);

            if($pag_actual > $ultima_pag){
                $pag_actual = 1;
            }

            if ($ultima_pag == 0){
                $ultima_pag = $pag_actual;
            }

            $listasUser = getListasAlphabeticOrderSearch($_SESSION['idusuario'], $privilegio, $_GET["texto"], $pag_actual);
        }
        else if (isset($_GET['orden']) && $_GET["orden"] == "fecha" && empty($_GET["texto"])){
            $ultima_pag = ceil(getSizeOfListasDateOrder($_SESSION['idusuario'], $privilegio) / $offset);

            if($pag_actual > $ultima_pag){
                $pag_actual = 1;
            }

            if ($ultima_pag == 0){
                $ultima_pag = $pag_actual;
            }

            $listasUser = getListasDateOrder($_SESSION['idusuario'], $privilegio, $pag_actual);
        }
        else if (isset($_GET['orden']) && $_GET["orden"] == "fecha" && !empty($_GET["texto"])){
            $ultima_pag = ceil(getSizeOfListasDateOrderSearch($_SESSION['idusuario'], $privilegio, $_GET["texto"]) / $offset);

            if($pag_actual > $ultima_pag){
                $pag_actual = 1;
            }

            if ($ultima_pag == 0){
                $ultima_pag = $pag_actual;
            }

            $listasUser = getListasDateOrderSearch($_SESSION['idusuario'], $privilegio, $_GET["texto"], $pag_actual);
        }
        else {
            // DEFAULT MODE
            $ultima_pag = ceil(getSizeOfListasAlphabeticOrder($_SESSION['idusuario'], $privilegio) / $offset);

            if($pag_actual > $ultima_pag){
                $pag_actual = 1;
            }

            if ($ultima_pag == 0){
                $ultima_pag = $pag_actual;
            }

            $listasUser = getListasAlphabeticOrder($_SESSION['idusuario'], $privilegio, $pag_actual);
        }
    }

    $img = Array();
    
    if(!empty($listasUser)){
        foreach($listasUser as $row){
            if($row['IMGBINARY'] != NULL){
                $img[] = 'data: ' . $row['IMGTYPE'] . ';base64,' . base64_encode($row['IMGBINARY']);
            }
            else{
                $img[] = '/~adrianpedro2122/proyecto/assets/noimage.png';
            }
        }
    }



}
else{
    header("Location: /~adrianpedro2122/proyecto/index.php");
}

echo $twig->render('listas.html', ['listas'  => $listasUser,
                                   'src'     => $img,
                                   'filtros' => $_GET,
                                   'user'    => $user,
                                   'size'    => $tamanio,
                                   'offset'  => $offset,
                                   'pagina'  => $pag_actual,
                                   'ultima'  => $ultima_pag]);
?>
