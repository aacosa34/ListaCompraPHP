<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

$formulario = "/controller/borrar_colaborador.php";
$titulo = "Borrar colaborador";

if(isset($_SESSION['idusuario']) && isset($_SESSION['idlista'])){
    $user = getUserById($_SESSION['idusuario']);
    $lista = getListaById($_SESSION['idlista'], $_SESSION['idusuario']);

    if($lista['PRIVILEGIOS'] == "Propietario"){
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $estado = "Mostrar";
            $_SESSION['iduserdel'] = $_GET['idusuario'];
            $usuario = getUserById($_GET['idusuario']);
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST' ){
            eliminarColaborador($_SESSION['iduserdel'], $_SESSION['idlista']);
            $estado = "Confirmacion";
        }
    }
}

echo $twig->render('borrar_colaborador.html', [ 'user' => $user,
                                                'usuario' => $usuario,
                                                'formulario' => $formulario,
                                                'titulo' => $titulo,
                                                'estado' => $estado]);

?>