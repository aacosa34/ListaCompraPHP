<?php

include_once "../model/bd.php";

session_start();

if(isset($_GET['borrar']) && isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);
    
    if($user['ROL'] == "Administrador" ){
        DB_borrar();
        header('Location: /controller/usuarios.php');
    }
    else{
        header('Location: /index.php');
    }
}
?>