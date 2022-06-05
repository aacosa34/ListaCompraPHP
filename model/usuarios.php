<?php

require_once "../vendor/autoload.php";
include_once "../controller/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();
if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    if($user['ROL'] == "Administrador"){

        $pag_actual = 1;
        $offset = 5;
        $tamanio = getSizeOfUserPage();

        $ultima_pag = ceil($tamanio/$offset);

        if(isset($_GET['pagina']) && $_SERVER['REQUEST_METHOD'] === 'GET'){
            $pag_actual = $_GET['pagina'];
            echo "Hola";
        }
        $pagina = getUserPage($pag_actual);
    }
    else{
        header("Location: /index.php");
    }

    
}

echo $twig->render('usuarios.html', ['listado' => $pagina,
                                     'user' => $user,
                                     'size' => $tamanio,
                                     'offset' => $offset,
                                     'pagina' => $pag_actual,
                                     'ultima' => $ultima_pag]);

?>