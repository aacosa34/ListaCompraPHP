<?php

include_once "../model/bd.php";

session_start();

if(isset($_GET['restaurar']) && isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);
    if($user['ROL'] == "Administrador" && empty(DB_restore('../bd/basicdata.sql'))){
        
        header('Location: /controller/usuarios.php');
    }
    else{
        header('Location: /index.php');
    }
}