<?php

require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$titulo = "Crear lista";

$formulario = "/controller/crear_lista.php";

$valores = '';

if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);
    $estado = 'Crear';
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($_POST['boton'] == 'Crear lista'){
            $sinerrores = true;
            $valores = $_POST;
            if(empty($_FILES['foto']) || !checkValidImage($_FILES['foto'])){
                echo "Hola?";
                $valores['errorfoto'] = "Introduzca una foto para la lista";
                $sinerrores = false;
            }
            else{
                $nombre = $_FILES['foto']['name'];
                // Almacenamos el nombre de la foto de manera temporal
                setcookie("nombrefoto", $nombre, time()+3600);

                // Copia de foto a carpeta temporal
                $filename = $_FILES['foto']["name"];
                $tempname = $_FILES['foto']["tmp_name"];
                $folder = realpath("../assets/tmplistas/") . "/" . $filename;
                $folder_relative = "../assets/tmplistas/" . $filename;
                $valores['foto'] = $folder_relative;

                if (!move_uploaded_file($tempname, $folder)) {
                    echo "Failed to upload image!";
                }
            }

            if(empty($_POST['NOMBRE'])){
                $valores['errornombre'] = 'El nombre de la lista no puede estar vacío';
                $sinerrores = false;
            }

            if($sinerrores){
                $estado = 'Confirmar';
            }
            else{
                $estado = 'Invalido crear';
            }
        }

        if($_POST['boton'] == "Confirmar datos"){
            $valores = $_POST;
            
            $estado = "Lista modificada";

            insertList($valores);

            unset($_COOKIE['nombrefoto']);
        }
    }
}

echo $twig->render('crearlista_form.html', ['user' => $user,
                                            'estado' => $estado,
                                            'valores' => $valores,
                                            'titulo' => $titulo,
                                            'formulario' => $formulario]);
 
