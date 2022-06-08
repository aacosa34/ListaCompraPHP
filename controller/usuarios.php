<?php

require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();
if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    if($user['ROL'] == "Administrador"){
        // Pagina por defecto
        $pag_actual = 1;

        if(isset($_GET['pagina']) && $_SERVER['REQUEST_METHOD'] === 'GET'){
            $pag_actual = $_GET['pagina'];
        }
        $listado_paginado = getUserPage($pag_actual);
    }
    else{
        header("Location: /index.php");
    }
}
else{
    header("Location: /index.php");
}

echo $twig->render('usuarios.html', ['listado' => $listado_paginado['pagina'],
                                     'user' => $user,
                                     'size' => $tamanio,
                                     'offset' => $offset,
                                     'pagina' => $pag_actual,
                                     'ultima' => $listado_paginado['ultima_pag']]);

?>
