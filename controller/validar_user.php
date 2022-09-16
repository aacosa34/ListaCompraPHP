<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$formulario = '/controller/validar_user.php';
$titulo = 'Validar usuario';
$estado_registro = "Visionado";
$valores = $fecha_nac = '';

if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    // Acciones realizadas con el administrador solo
    if($user['ROL'] == "Administrador" ){
        // Obtenemos la informacion del usuario desde el Listado con metodo GET
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            if(isset($_GET['idusuario'])){
                // Obtenemos la informacion del usuario obtenido por el GET
                $valores = getUserById($_GET['idusuario']);

                // Guardamos la cookie del usuario para poder validarlo mas tarde
                setcookie("idusermod", $_GET['idusuario'], time()+3600);

                // Obtencion de la fecha de nacimiento de formato SQL a HTML para render
                $fecha_nac = explode("-", $valores['FNAC']); // Dividir la fecha obtenida de la fila

                // Finalmente obtenemos la foto del usuario
                $valores['foto'] = formatImageB64($valores);
            }
        }

        // Activacion del usuario, no hace falta rellenar el Formulario
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_COOKIE['idusermod'])){
            if(isset($_POST['boton']) && $_POST['boton'] == "Activar e informar"){
                activateUser($_COOKIE['idusermod']);
                $estado_registro = "Activar";
            }
            else if(isset($_POST['boton']) && $_POST['boton'] == "Borrar usuario"){
                borrarUsuarioAdmin($_COOKIE['idusermod']);
                $estado_registro = "Borrar";
            }
            else if(isset($_POST['boton']) && $_POST['boton'] == "Informar de error"){
                $estado_registro = "Informar";
            }

            // La cookie se desactiva si hemos realizado cualquiera de las acciones anteriores
            unset($_COOKIE['idusermod']);
        }
    }
    else{
        header("Location: /index.php");
    }
}
/**
  * Variables de control
  *     user obtiene la informacion del usuario administrador
  *     valores obtiene la informacion del usuario en la primera fase
  *     valores en la segunda fase esta vacio, no es necesario
  *     estado en la segunda fase indica que accion hemos pedido realizar con el formulario:
  *         Activar - Hemos llamado a la activacion del usuario
  *         Borrar - Hemos llamado al borrado del usuario
  *         Informar - Hemos llamado al textbox de insertar el informe del usuario
  **/

echo $twig->render('formulario_validacion.html', [  'user' => $user,
                                                    'valores' => $valores,
                                                    'fecha' => $fecha_nac,
                                                    'formulario' => $formulario,
                                                    'titulo' => $titulo,
                                                    'estado' => $estado_registro]);


?>
