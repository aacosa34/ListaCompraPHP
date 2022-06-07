<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$formulario = '/controller/modificacion_usuario.php';
$estado_registro = "Visionado";
$sinerrores = false;
if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    // Titulo
    $titulo = 'Modificación usuario: ' . $user['NOMBRE'];

    // Acciones realizadas con ambos usuarios tanto Administrador como Usuario
    // Modifican solo 3 campos
    // Campo 1: email
    // Campo 2: Telefono
    // Campo 3: Passwords
    // Campo 4: foto
    if($user['ROL'] == "Administrador" || $user['ROL'] == "Usuario" ){
        if($_SERVER['REQUEST_METHOD'] === 'POST' && ){
            // Validacion
            $validacion = checkFormulario($_POST, $_FILES['foto']);

            if (!empty($_COOKIE['validado']) && $_COOKIE['validado'] == 1){
                
            }
            setcookie("nombrefoto", $nombre, time()+3600);

            // Obtencion de la fecha de nacimiento de formato SQL a HTML para render
            $fecha_nac = explode("-", $valores['FNAC']); // Dividir la fecha obtenida de la fila

            // Finalmente obtenemos la foto del usuario
            $valores['foto'] = formatImageB64($valores);

            // Pasamos a la fase borrador donde el formulario ya validado se pone en modo Borrador de solo lectura
            $estado_registro = "Borrador";

            }
            else {
                // No hay POST con la comprobacion de valores, usamos los por defecto
                $validacion = $user;
            }
        }

        // Activacion del usuario, no hace falta rellenar el Formulario
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_COOKIE['idusermod'])){
            if(isset($_POST['boton']) && $_POST['boton'] == "Activar e informar"){
                activateUser($_COOKIE['idusermod']);
            }

            if(isset($_POST['boton']) && $_POST['boton'] == "Borrar usuario"){
                borrarUsuarioAdmin($_COOKIE['idusermod']);
                $estado_registro = "Borrar";
            }

            if(isset($_POST['boton']) && $_POST['boton'] == "Informar de error"){
                $estado_registro = "Informar";
            }

            // La cookie se desactiva si hemos realizado cualquiera de las acciones anteriores
            unset($_COOKIE['idusermod']);

            $estado_registro = "Enviado";
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
  *     valores en la segunda fase obtiene los campos del usuario cambiados y debe ponerse en readonly
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
