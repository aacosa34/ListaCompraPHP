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
if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    if($user['ROL'] == "Administrador" ){
        // Obtenemos la informacion del usuario desde el Listado con metodo GET
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if(isset($_GET['idusuario'])){
                setcookie("idusermod", $_GET['idusuario'], time()+3600);
                $mod_usuario = getUserById($_GET['idusuario']);
                $mod_usuario['sinerrores'] = true; // para que se muestren en readonly todos los campos
                $fecha_nac = explode("-",$mod_usuario['FNAC']); // Dividir la fecha obtenida de la fila
                $mod_usuario['foto'] = formatImageB64($mod_usuario);
            }
        }
        
        // Activacion del usuario 
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_COOKIE['idusermod'])){
            if(isset($_POST['boton']) && $_POST['boton'] == "Activar e informar"){
                activateUser($_COOKIE['idusermod']);
                $estado_registro = "Activado";
                unset($_COOKIE['remember_user']); 
                // Retorno a las listas de usuarios
                // header("usuarios.php");
            }
            
            // if(isset($_POST['boton']) )
        }
    }
}

echo $twig->render('formulario.html', [ 'valores' => $mod_usuario,
                                        'fecha' => $fecha_nac,
                                        'formulario' => $formulario,
                                        'titulo' => $titulo,
                                        'visitante' => $visitante,
                                        'user' => $user,
                                        'estado' => $estado_registro]);


?>