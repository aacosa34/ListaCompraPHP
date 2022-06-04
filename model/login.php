<?php
require_once "../vendor/autoload.php";
include_once "../controller/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

$error = $clase = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $error = comprobarLogin($_POST['email'], $_POST['password']);
    $email = $_POST['email'];
    $password = $_POST['password'];
    $resultado = comprobarLogin($email, $password);

    if(!$resultado){
        $clase = 'is-invalid';
        $error = 'Email o contraseña incorrectos';
    }else{
        session_start();

        $_SESSION['email'] = $email;

        header("Location: /index.php");
    }
}

echo $twig->render('login.html', ['clase' => $clase, 'error' => $error]);

?>