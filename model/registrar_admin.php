<?php
require_once "../vendor/autoload.php";
include_once "../controller/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$validacion = Array();

if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);
    if($user['ROL'] == "Administrador" && $_SERVER['REQUEST_METHOD'] === 'POST'){
        
        if($validacion['boton'] != "Validar datos si son correctos"){
            $nombre = $_FILES['foto']['name'];

            $_SESSION['foto'] = $nombre;
            setcookie("name", $nombre, time()+3600);
        }

        $validacion = checkFormulario($_POST, $_FILES['foto']);
        // Se ha validado, preparamos la insercion
        if($validacion['boton'] == "Validar datos si son correctos"){
            insertUser($validacion, $user['ROL']);
        }
        //print_r($validacion);
    }
}
else{
    header("Location: /index.php");
}

echo $twig->render('formulario.html', ['valores' => $validacion,
                                        'user' => $user]);

?>