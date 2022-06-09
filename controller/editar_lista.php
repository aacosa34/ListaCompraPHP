<?php

require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$titulo = "Editar lista";

$formulario = "/controller/editar_lista.php";

if(isset($_SESSION['idusuario']) && isset($_SESSION['idlista'])){
    $user = getUserById($_SESSION['idusuario']);
    $estado_registro = "Editar";


    if($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['idlista'])){
        $_SESSION['idlista'] = $_GET['idlista'];
        $validacion = getListaById($_SESSION['idlista'], $_SESSION['idusuario']);
        $validacion['foto'] = formatImageB64($validacion);
    }


    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // Validacion
        $validacion = checkLista($_POST, $_FILES['foto']);

        // Existe error, se registra para la Vista
        if(!empty($validacion) && $validacion['sinerrores'] === false && $validacion['boton'] == "Editar lista"){
            $estado_registro = "Invalido editar";

            // No ha establecido foto... Sin embargo seguimos mostrando la vieja
            if (isset($validacion['errorfoto'])){
                 // Volvemos a obtener la foto que tiene actualmente, debido al fallo
                $lista = getListaById($_SESSION['idlista'], $_SESSION['idusuario']);
                $lista['foto'] = formatImageB64($lista);
            }
        }
        // Validacion correcta, se registra para la vista y previsualizacion
        else if (!empty($validacion) && $validacion['sinerrores'] === true && $validacion['boton'] == "Editar lista"){
            $estado_registro = "Validado";
            $validado = true;
            setcookie("validado", 1, time()+3600);
            // Almacenamos la foto
            $nombre = $_FILES['foto']['name'];
            // Almacenamos el nombre de la foto de manera temporal
            setcookie("nombrefoto", $nombre, time()+3600);
            // Cambiamos el titulo para indicar confirmacion
            $titulo = 'Confirmar cambios';
        }
        // Ya validado, se revisa y se inserta con la confirmacion
        else if (!empty($validacion) && $validacion['boton'] == "Confirmar datos" && $_COOKIE['validado'] == 1){
            $titulo = 'Registro finalizado';
            // Le pasamos la cookie del rol para que la BD se inserte con los datos indicados por el administrador
            modificarInfoLista($validacion, $_SESSION['idusuario'], $_SESSION['idlista']);
            $estado_registro = "Lista modificada";
            unset($_COOKIE['validado']);
            unset($_COOKIE['nombrefoto']);
        }
    }
}

// NO

echo $twig->render('crearlista_form.html', [ 'user' => $user,
                                         'valores' => $validacion,
                                         'estado' => $estado_registro])

?>