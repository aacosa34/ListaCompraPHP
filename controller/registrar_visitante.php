<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$visitante = $validado = false;
setcookie("validado", 0, time()+3600);

$user = $formulario = ''; // Para que exista la variable unicamente
$titulo = 'Solicitar registro';
$estado_registro = "";

if(!isset($_SESSION['idusuario'])){
    $visitante = true;
    // Registramos el formulario que ira en el action
    $formulario = '/controller/registrar_visitante.php';
    // Registramos que puede ver el formulario sin informacion
    $estado_registro = "Sin registro";

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $validacion = checkFormulario($_POST, $_FILES['foto']);

        // Existe error, se registra para la Vista
        if(!empty($validacion) && $validacion['sinerrores'] === false && $validacion['boton'] == "Enviar"){
            $estado_registro = "Invalido";
        }
        // Validacion correcta, se registra para la vista y previsualizacion
        else if (!empty($validacion) && $validacion['sinerrores'] === true && $validacion['boton'] == "Enviar"){
            echo "Llega a validado";
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
            insertUser($validacion);
            $estado_registro = "Registrada";
            unset($_COOKIE['validado']);
            unset($_COOKIE['nombrefoto']);
        }
    }
}
else if (isset($_SESSION['idusuario'])) { // Es un usuario, que hace aqui
    header("Location: /index.php");
}

echo $twig->render('formulario_registro.html', [ 'valores' => $validacion,
                                                  'visitante' => $visitante,
                                                  'formulario' => $formulario,
                                                  'titulo' => $titulo,
                                                  'user' => $user,
                                                  'estado' => $estado_registro,
                                                  'validado' => $validado]);

?>
