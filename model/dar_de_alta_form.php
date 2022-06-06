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
        $validatephoto = checkValidImage($_FILES);

        if($validatephoto){
            $ruta= "/home/adrian/Documentos/ListaCompraPHP/assets/usuarios";
            $nombre_tmp = $_FILES['foto']['tmp_name'];
            $nombre_foto = basename($_FILES['foto']['name']);
            move_uploaded_file($nombre_tmp, "$ruta/$nombre_foto");
            // Variables para almacenar la imagen en la base de datos
            $_SESSION["imgtype"] = getTypeImg($_FILES['foto']['name']);
            $_SESSION["img"] = addslashes(file_get_contents($_FILES['foto']['tmp_name']));
            

            if(move_uploaded_file($nombre_tmp, "$ruta/$nombre_foto")){
                $foto = "$ruta/$nombre_foto";
            }
                
        }

        $validacion = checkFormulario($_POST);

        if($validacion['sinerrores']){
        }
        //print_r($validacion);
    }
}
else{
    header("Location: /index.php");
}

echo $twig->render('dar_de_alta_form.html', ['valores' => $validacion,
                                             'foto' => $foto,
                                             'user' => $user]);

?>