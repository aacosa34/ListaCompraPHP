<?php

$conn = NULL;

function getConnection(){
    global $conn;

    if ($conn == NULL){
        $conn = new mysqli("127.0.0.1", "adminlistacompra", "password", "LISTACOMPRA");
        if ($conn->connect_errno) {
          echo ("Fallo al conectar: " . $conn->connect_error);
        }
      }
}

function getUserByEmail($email){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT * FROM USUARIOS WHERE EMAIL=?");
    $query->bind_param("i", $email);
    $query->execute();

    $resultQuery = $query->get_result();

    if($resultQuery->num_rows>0){
        $usuario = $resultQuery->fetch_assoc();
    }

    return $usuario;
}

function getEstadisticas(){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT NOMBRE, CANTIDAD FROM HISTORICO");
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $estadisticas = $resultQuery->fetch_all(MYSQLI_ASSOC);
    }

    return $estadisticas;

}

function getProductosComprados(){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT * FROM PRODUCTOSENLISTAS");
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $historico_productos = $resultQuery->fetch_all(MYSQLI_ASSOC);
    }

    return $historico_productos;
}

function comprobarLogin($email, $password){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT EMAIL, PASSWORD FROM USUARIOS WHERE EMAIL=?");
    $query->bind_param('s', $email);
    $query->execute();
    $resultQuery = $query->get_result();

    if ($resultQuery->num_rows > 0) {
      $usuario = $resultQuery->fetch_assoc();
    }

    // Si hemos encontrado un usuario, comprobamos la contrasenia
    if(!empty($usuario)){
        if($password == $usuario['PASSWORD']){
            return true;
        }
    }
    
    return false;
}

function getListas($idusuario){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G WHERE G.IDLISTA=L.IDLISTA AND IDUSUARIO=?");
    $query->bind_param('i', $idusuario);
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $listas = $resultQuery->fetch_all(MYSQLI_ASSOC);
    }

    return $listas;

}


?>