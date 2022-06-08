<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();
/*
ESTADOS
    Mostrar
    Confirmacion
*/
$estado="";
$titulo="";

$formulario = '/controller/borrar_user.php';
// Variable de control del usuario por defecto
if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    if($user['ROL'] == "Administrador"){
        if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['idusuario'])){
            $estado = "Mostrar";
            $validacion = getUserById($_GET['idusuario']);
            setcookie('idusuariodel', $_GET['idusuario'], time()+3600);
            $titulo = "Borrado del usuario: " . $validacion['NOMBRE'];
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['boton']) && isset($_COOKIE['idusuariodel'])){
            borrarUsuarioAdmin($_COOKIE['idusuariodel']);
            $estado = "Confirmacion";
            $titulo = "Usuario borrado";
            unset($_COOKIE['idusuariodel']);
        }
    }
    else {
        header("Location: /index.php");
    }
}
else{
    header("Location: /index.php");
}
/**
  * Variables de control
  *     user obtiene la informacion del usuario administrador
  *     valores obtiene la informacion del usuario en la primera fase
  *     valores en la segunda fase obtiene los campos del usuario cambiados y debe ponerse en readonly
  *     estado en la segunda fase indica que accion hemos pedido realizar con el formulario:
  *         Activar - Hemos llamado a la activacion del usuario
  *         Borrar - Hemos llamado al borrado del usuario
  *         Informar - Hemos llamado al textbox de insertar el informe del usuario
  **/

  echo $twig->render('borrar_user.html', [ 'user' => $user,
                                          'valores' => $validacion,
                                          'formulario' => $formulario,
                                          'titulo' => $titulo,
                                          'estado' => $estado]);


?>
