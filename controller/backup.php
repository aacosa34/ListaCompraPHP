<?php

include_once "../model/bd.php";

session_start();

if(isset($_GET['backup']) && isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);
    
    if($user['ROL'] == "Administrador" ){
        $backup = DB_backup();
        header('Location: /controller/usuarios.php');
    }
    else{
        header('Location: /index.php');
    }
}

?>