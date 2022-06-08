<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$formulario = '/controller/modificacion_usuario.php';
$estado_registro = "";
$validado = 0;

// No hay que controlar si es usuario o administrador, da igual, se modifican los valores de ellos mismos de manera limitada
if(isset($_SESSION['idusuario'])){
    $validacion = $user = getUserById($_SESSION['idusuario']);

    if ($user['ROL'] == "Administrador"){
        // Redirigir a modificacion_usuario con su propio id
        header("Location: /controller/modificacion_usuario_admin.php?idusuario=".$_SESSION['idusuario']);
    }

    $fecha_nac = explode("-", $validacion['FNAC']); // Dividir la fecha obtenida de la fila

    // Almacenamos la foto que tiene actualmente
    $validacion['foto'] = "data:" . $user['IMGTYPE'] .  ";base64," . base64_encode($user['IMGBINARY']);
    // Titulo
    $titulo = 'Modificación usuario: ' . $user['NOMBRE'];
    // Ponemos que ya puede ver el estado de su formulario relleno
    $estado_registro = "Visionado";

    // Acciones realizadas con ambos usuarios tanto Administrador como Usuario
    // Modifican solo 3 campos
    // Campo 1: email
    // Campo 2: Telefono
    // Campo 3: Passwords
    // Campo 4: foto
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // Validacion
        $validacion = checkFormulario($_POST, $_FILES['foto']);

        // Existe error, se registra para la Vista
        if(!empty($validacion) && $validacion['sinerrores'] === false && $validacion['boton'] == "Enviar"){
            $estado_registro = "Invalido";

            // No ha establecido foto... Sin embargo seguimos mostrando la vieja
            if ($validacion['errorfoto']){
                // Almacenamos la foto que tiene actualmente
                $validacion['foto'] = "data:" . $user['IMGTYPE'] .  ";base64," . base64_encode($user['IMGBINARY']);
            }
        }
        // Validacion correcta, se registra para la vista y previsualizacion
        else if (!empty($validacion) && $validacion['sinerrores'] === true && $validacion['boton'] == "Enviar"){
            $estado_registro = "Validado";
            $validado = true;
            setcookie("validado", 1, time()+3600);
            // Almacenamos la foto
            $nombre = $_FILES['foto']['name'];
            // Almacenamos el nombre de la foto de manera temporal
            setcookie("nombrefoto", $nombre, time()+3600);
            // Cambiamos el titulo para indicar confirmacion
            $titulo = 'Confirmar registro';
        }
        // Ya validado, se revisa y se inserta con la confirmacion
        else if (!empty($validacion) && $validacion['boton'] == "Confirmar" && $_COOKIE['validado'] == 1){
            $titulo = 'Registro finalizado';
            // Le pasamos la cookie del rol para que la BD se inserte con los datos indicados por el administrador
            modificarUsuarioAdmin($validacion, $_SESSION['idusuario']);
            $estado_registro = "Modificado Usuario";
            unset($_COOKIE['validado']);
            unset($_COOKIE['nombrefoto']);
        }
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

echo $twig->render('formulario_modificacion.html', [  'user' => $user,
                                                    'valores' => $validacion,
                                                    'fecha' => $fecha_nac,
                                                    'formulario' => $formulario,
                                                    'titulo' => $titulo,
                                                    'estado' => $estado_registro,
                                                    'validado' => $validado]);




?>
