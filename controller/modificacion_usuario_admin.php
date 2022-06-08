<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$formulario = '/controller/modificacion_usuario_admin.php';
$estado_registro = "";
$validado = 0;

$titulo = "Modificar mis datos";

// Variable de control del usuario por defecto
if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    if($user['ROL'] == "Administrador"){
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            // GET idusuario por primera vez y lo almacenamos en una cookie
            setcookie("idusermod", $_GET['idusuario'], time()+3600);

            // Obtenemos la informacion del usuario
            $validacion = getUserById($_GET['idusuario']);
            $fecha_nac = explode("-", $validacion['FNAC']); // Dividir la fecha obtenida de la fila
            // Almacenamos la foto que tiene actualmente
            $validacion['foto'] = "data:" . $validacion['IMGTYPE'] .  ";base64," . base64_encode($validacion['IMGBINARY']);
            // Titulo
            $titulo = 'Modificación administrador usuario: ' . $validacion['NOMBRE'];
            // Ponemos que ya puede ver el estado de su formulario relleno
            $estado_registro = "Modificacion";
        }

        // Obtenemos los campos que han cambiado con el GET
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Validacion
            $validacion = checkFormulario($_POST, $_FILES['foto']);

            // Existe error, se registra para la Vista
            if(!empty($validacion) && $validacion['sinerrores'] === false && $validacion['boton'] == "Enviar"){
                $estado_registro = "Invalido";

                // No ha establecido foto... Sin embargo seguimos mostrando la vieja
                if ($validacion['errorfoto']){
                    $tmpuserfoto = getUserById($_COOKIE['idusermod']);
                    // Almacenamos la foto que tiene actualmente
                    $validacion['foto'] = "data:" . $tmpuserfoto['IMGTYPE'] .  ";base64," . base64_encode($tmpuserfoto['IMGBINARY']);
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
                $titulo = 'Confirmar modificacion admin usuario: ' . $validacion['NOMBRE'];
            }
            // Ya validado, se revisa y se inserta con la confirmacion
            else if (!empty($validacion) && $validacion['boton'] == "Confirmar" && $_COOKIE['validado'] == 1){
                $titulo = 'Registro admin finalizado usuario: ' . $validacion['NOMBRE'];
                // Le pasamos la cookie del rol para que la BD se inserte con los datos indicados por el administrador
                modificarUsuarioAdmin($validacion, $_COOKIE['idusermod']);
                $estado_registro = "Modificado Admin";
                // Cleanning
                unset($_COOKIE['validado']);
                unset($_COOKIE['nombrefoto']);
                unset($_COOKIE['idusermod']);
            }
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

  echo $twig->render('formulario_registro.html', [  'user' => $user,
                                                      'valores' => $validacion,
                                                      'fecha' => $fecha_nac,
                                                      'formulario' => $formulario,
                                                      'titulo' => $titulo,
                                                      'estado' => $estado_registro,
                                                      'validado' => $validado]);


?>
