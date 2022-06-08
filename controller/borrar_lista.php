<?php

require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$titulo = "Borrar lista";

$formulario = "/controller/borrar_lista.php";

if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    if($user['ROL'] == "Administrador"){
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $_SESSION['idlista'] = $_GET['idlista'];
            $infolista = getListaById($_SESSION['idlista'], $_SESSION['idusuario']);
            $estado = "Mostrar";
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $infolista = getListaById($_SESSION['idlista'], $_SESSION['idusuario']);
            borrarLista($infolista['IDLISTA']);
            $estado = "Confirmacion";
        }
    }

}

echo $twig->render('borrar_lista.html', [ 'user' => $user,
                                          'valores' => $infolista,
                                          'formulario' => $formulario,
                                          'titulo' => $titulo,
                                          'estado' => $estado]);

?>