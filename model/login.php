<?php
require_once "../vendor/autoload.php";
include("bd.php");

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

$error = "";

if(isset($_SESSION['idusuario'])){
    header("Location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = comprobarLogin($_POST['email'], $_POST['password']);
    
    if(!$error){
        session_start();

        $_SESSION['email'] = $email;

        header("Location: /index.php");
    }
    else{
        header("Location: login.php");
    }

    exit();
}

echo $twig->render('login.html', ['error' => $error]);

?>