<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

$error = $clase = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $error = comprobarLogin($_POST['email'], $_POST['password']);
    $email = $_POST['email'];
    $password = $_POST['password'];
    $usuario = comprobarLogin($email, $password);

    if(empty($usuario)){
        $clase = 'is-invalid';
        $error = 'Email o contraseña incorrectos';
    }else{
        session_start();

        actualizarSesion($usuario['IDUSUARIO']);
        
        $_SESSION['idusuario'] = $usuario['IDUSUARIO'];

        header("Location: /index.php");
    }
}

echo $twig->render('login.html', ['clase' => $clase, 'error' => $error]);

?>