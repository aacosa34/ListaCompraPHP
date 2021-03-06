<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

// Variables de control de si es visitante
$visitante = $validado = false;
// Variable de control de validacion de la informacion introducida por el administrador
setcookie("validado", 0, time()+3600);

$user = $formulario = ''; // Para que exista la variable unicamente
$titulo = 'Solicitar registro';
$estado_registro = "";
$validacion = '';

// Comprobamos que sea un usuario real, necesario tambien para el panel
if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);
}
else {
    header("Location: /~adrianpedro2122/proyecto/index.php");
}

// Verificamos que sea Administrador dicho usuario
if(isset($user['ROL']) && $user['ROL'] == 'Administrador'){
    // Registramos el formulario que ira en el action
    $formulario = '/~adrianpedro2122/proyecto/controller/registrar_admin.php';
    // Registramos que puede ver el formulario
    $estado_registro = "Sin registro";

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $validacion = checkFormulario($_POST, $_FILES['foto']);

        // Existe error, se registra para la Vista
        if(!empty($validacion) && $validacion['sinerrores'] === false && $validacion['boton'] == "Enviar"){
            $estado_registro = "Invalido";
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
            insertUser($validacion, $user['ROL']);
            $estado_registro = "Registrada Admin";
            unset($_COOKIE['validado']);
            unset($_COOKIE['nombrefoto']);
        }
    }
}
else {
    // Es un usuario estandar que hace aqui
    header("Location: /~adrianpedro2122/proyecto/index.php");
}

echo $twig->render('formulario_registro.html', [ 'valores' => $validacion,
                                                  'visitante' => $visitante,
                                                  'formulario' => $formulario,
                                                  'titulo' => $titulo,
                                                  'user' => $user,
                                                  'estado' => $estado_registro,
                                                  'validado' => $validado]);
?>
