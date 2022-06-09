<?php

require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$titulo = "Editar lista";

$formulario = "/controller/editar_lista.php";

if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);
    $estado = "Editar";

    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        $_SESSION['idlista'] = $_GET['idlista'];
        $lista = getListaById($_SESSION['idlista'], $_SESSION['idusuario']);
        $lista['foto'] = formatImageB64($lista);
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // Guardar Datos
        if (!empty($_POST['NOMBRE']) && !empty($_FILES['foto'])){

        }
        else {
            $lista['errorfoto'] = "No se ha introducido alguna foto";
            $lista['errornombre'] = "No se ha introducido algún nombre para la Lista";
        }
        $lista = getListaById($_SESSION['idlista'], $_SESSION['idusuario']);
        $estado = "Confirmar";
    }
}

// NO

echo $twig->render('crearlista_form.html', [ 'user' => $user,
                                         'valores' => $lista,
                                         'estado' => $estado])

?>