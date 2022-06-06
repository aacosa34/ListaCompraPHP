<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$validacion = Array();
$formulario = '';
$titulo = 'Registrar usuario';
$estado_registro = "";
if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);
    if($user['ROL'] == "Administrador" && $_SERVER['REQUEST_METHOD'] === 'POST'){
        $validacion = checkFormulario($_POST, $_FILES['foto']);

        //almacenamos el formulario que ira en el action
        $formulario = '/controller/registrar_admin.php';
        // Si es en el primer envio de los datos
        if($validacion['boton'] != "Validar datos si son correctos"){
            $nombre = $_FILES['foto']['name'];

            // Almacenamos en una variable 
            $_SESSION['foto'] = $nombre;
            setcookie("name", $nombre, time()+3600);
        }

        // Se ha validado, preparamos la insercion
        if($validacion['boton'] == "Validar datos si son correctos"){
            insertUser($validacion, $user['ROL']);
            unset($_COOKIE['name']);
            $estado_registro = "Insertado";
        }
        //print_r($validacion);
    }
}
else{
    header("Location: /index.php");
}

echo $twig->render('formulario.html', ['valores' => $validacion,
                                        'formulario' => $formulario,
                                        'titulo' => $titulo,
                                        'user' => $user,
                                        'estado' => $estado_registro]);

?>