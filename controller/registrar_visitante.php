<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$visitante = false;
$user = $formulario = ''; // Para que exista la variable unicamente
$titulo = 'Solicitar registro';
$estado_registro = "";
if(!isset($_SESSION['idusuario'])){
    $visitante = true;
    // Registramos el formulario que ira en el action
    $formulario = '/controller/registrar_visitante.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST'){ 
        $validacion = checkFormulario($_POST, $_FILES['foto']);

        // Si es en el primer envio de los datos
        if($validacion['boton'] == "Enviar"){
            echo "Entra enviar";
            $nombre = $_FILES['foto']['name'];

            // Almacenamos en una variable 
            $_SESSION['foto'] = $nombre;
            setcookie("name", $nombre, time()+3600);
        }

      
        // print_r($validacion);
        // Se ha validado, preparamos la insercion
        if($validacion['boton'] == "Confirmar"){
            echo "Entra confirmacion";

            insertUser($validacion);
            // unset($_COOKIE['name']);
            $estado_registro = "Solicitado";
        }
        //print_r($validacion);
    }
}

echo $twig->render('formulario.html', ['valores' => $validacion,
                                        'visitante' => $visitante,
                                        'formulario' => $formulario,
                                        'titulo' => $titulo,
                                        'user' => $user,
                                        'estado' => $estado_registro]);

?>