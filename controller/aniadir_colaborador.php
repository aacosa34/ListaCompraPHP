<?php

require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();


if(isset($_SESSION['idusuario']) && isset($_SESSION['idlista'])){
    $user = getUserById($_SESSION['idusuario']);
    $lista = getListaById($_SESSION['idlista'], $_SESSION['idusuario']);

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(isset($_POST['email']) && isset($_POST['privilegios'])){
            $usuario = getUserByEmail($_POST['email']);
            
            if(isset($usuario) && !isUserInList($usuario['IDUSUARIO'], $_SESSION['idlista'])){
                $privilegios = $_POST['privilegios'];
                addUserToList($usuario['IDUSUARIO'], $privilegios, $_SESSION['idlista']);
            }
        }

        $productos = getProductosLista($_SESSION['idlista']);
        $usuarios_compartidos = getUsersOnList($lista['IDLISTA']);
    }

    header('Location: /~adrianpedro2122/proyecto/controller/vista_lista.php?idlista='.$_SESSION['idlista']);
}

echo $twig->render('vista_lista.html',[ 'user' => $user,
                                        'lista' => $lista,
                                        'productos' => $productos,
                                        'usuarios' => $usuarios_compartidos]);

?>