<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$visitante = true; // Viene de un visitante, asi que lo establecemos a true
$formulario = '/controller/validar_user.php';
$titulo = 'Validar usuario';
$estado_registro = "";
$sinerrores = false;
if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    if($user['ROL'] == "Administrador" ){
        // Obtenemos la informacion del usuario desde el Listado con metodo GET
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if(isset($_GET['idusuario'])){
                // Obtenemos la informacion del usuario obtenido por el GET
                $mod_usuario = getUserById($_GET['idusuario']);

                // Guardamos la cookie del usuario para poder validarlo mas tarde
                setcookie("idusermod", $_GET['idusuario'], time()+3600);
                // Establecemos la variable sin errores a true para renderizarlo de manera correcta (campos en readonly)
                // $sinerrores = true;
                // $mod_usuario['sinerrores'] = true;
                
                // Obtencion de la fecha de nacimiento de formato SQL a HTML para render
                $fecha_nac = explode("-",$mod_usuario['FNAC']); // Dividir la fecha obtenida de la fila
                
                // Finalmente obtenemos la foto del usuario
                $mod_usuario['foto'] = formatImageB64($mod_usuario);
            }
        }
        
        // Activacion del usuario, no hace falta rellenar el Formulario
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_COOKIE['idusermod'])){
            if(isset($_POST['boton']) && $_POST['boton'] == "Activar e informar"){
                activateUser($_COOKIE['idusermod']);
                $estado_registro = "Activado";
                unset($_COOKIE['idusermod']);
            }
            
            if(isset($_POST['boton']) && $_POST['boton'] == "Borrar usuario"){
                borrarUsuarioAdmin($_COOKIE['idusermod']);
                $estado_registro = "Borrar";
                unset($_COOKIE['idusermod']);
            } 
        }
    }else{
        header("Location: /index.php");
    }
}

echo $twig->render('formulario_validacion.html', [ 'valores' => $mod_usuario,
                                                    'fecha' => $fecha_nac,
                                                    'formulario' => $formulario,
                                                    'titulo' => $titulo,
                                                    'estado' => $estado_registro]);


?>