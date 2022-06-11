<?php

require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);
    $mensaje = '';
    
    if($user['ROL'] == "Administrador" ){
        if(isset($_POST['boton'])){
            $find_sql = strpos($_FILES['fichero']['name'], '.sql');
            if($find_sql !== false){
                $mensaje = DB_restore($_FILES['fichero']['tmp_name']);
                if(empty($mensaje)){
                    $mensaje = "Éxito al cargar la BBDD";
                }
            }else{
                $mensaje = "No se ha podido subir el fichero (formato invalido)";
            }
        }        
    }
    else{
        header('Location: /~adrianpedro2122/proyecto/index.php');
    }
}

echo $twig->render('restore_from_file.html', ['user' => $user,
                                              'mensaje' => $mensaje]);

?>