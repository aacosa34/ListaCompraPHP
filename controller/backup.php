<?php

include_once "../model/bd.php";

session_start();

if(isset($_GET['backup']) && isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);
    
    if($user['ROL'] == "Administrador" ){
        $backup = DB_backup();
        header('Location: /~adrianpedro2122/proyecto/controller/usuarios.php');
    }
    else{
        header('Location: /~adrianpedro2122/proyecto/index.php');
    }
}

?>