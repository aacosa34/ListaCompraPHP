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

function closeConnection(){
    global $conn;
    if ($conn != NULL){
        $conn -> close();
    }
}

function getUserById($idusuario){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT * FROM USUARIOS WHERE IDUSUARIO=?");
    $query->bind_param("i", $idusuario);
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

    $query = $conn->prepare("SELECT IDUSUARIO FROM USUARIOS WHERE EMAIL=? AND PASSWORD=?");
    $query->bind_param('ss', $email, $password);
    $query->execute();
    $resultQuery = $query->get_result();

    // Si encontramos un usuario, devolvemos true
    if ($resultQuery->num_rows > 0) {
      $usuario = $resultQuery->fetch_assoc();

      // Liberamos memoria despues de obtener los resultados de la consulta
      $resultQuery -> free();

      return $usuario;
    }
    else{
        return '';
    }

}

// function getListas($idusuario){
//     global $conn;
//     getConnection();

//     $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA=L.IDLISTA WHERE IDUSUARIO=?");
//     $query->bind_param('i', $idusuario);
//     $query->execute();
//     $resultQuery = $query->get_result();

//     if($resultQuery->num_rows > 0){
//         $listas = $resultQuery->fetch_all(MYSQLI_ASSOC);
//         // Liberamos memoria despues de obtener los resultados de la consulta
//         $resultQuery -> free();

//     }

    
//     return $listas;

// }

function getListaById($idlista, $idusuario){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA=L.IDLISTA WHERE IDUSUARIO=? AND L.IDLISTA=?");
    $query->bind_param('ii', $idusuario, $idlista);
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $lista = $resultQuery->fetch_assoc();
        // Liberamos memoria despues de obtener los resultados de la consulta
        $resultQuery -> free();

    }
    
    return $lista;

}

function getSizeOfListas($idusuario){
    global $conn;
    getConnection();

    $query = $conn -> prepare("SELECT L.IDLISTA FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ?");
    $query -> bind_param('i', $idusuario);
    
    $query -> execute();
    $resultQuery = $query -> get_result();

    return $resultQuery -> num_rows;
}

function getListasAlphabeticOrder($idusuario, $privilegio, $pagina){
    global $conn;
    getConnection();

    $offset = 6;
    $minimo = 0;
    $inicio = (($offset * $pagina) - $offset) > 0 ? ($offset * $pagina) - $offset : $minimo;

    // Las vistas con todos los filtros de privilegios son iguales a las de sin filtro
    if (count($privilegio) == 0 || count($privilegio) == 3){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? ORDER BY L.NOMBRE ASC LIMIT ?, ? ;");
        $query->bind_param('iii', $idusuario, $inicio, $offset);
    }
    else if (count($privilegio) == 1){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND G.PRIVILEGIOS = ? ORDER BY L.NOMBRE ASC LIMIT ?, ?;");
        $query->bind_param('iiii', $idusuario, $privilegio[0], $inicio, $offset);
    }
    else if (count($privilegio) == 2){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (G.PRIVILEGIOS = ? OR G.PRIVILEGIOS = ?) ORDER BY L.NOMBRE ASC LIMIT ?, ? ;");
        $query->bind_param('iiiii', $idusuario, $privilegio[0], $privilegio[1], $inicio, $offset);
    }
    
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $listas = $resultQuery->fetch_all(MYSQLI_ASSOC);
        // Liberamos memoria despues de obtener los resultados de la consulta
        $resultQuery -> free();
    }

    
    return $listas;
}



function getSizeOfListasAlphabeticOrder($idusuario, $privilegio){
    global $conn;
    getConnection();

    // Las vistas con todos los filtros de privilegios son iguales a las de sin filtro
    if (count($privilegio) == 0 || count($privilegio) == 3){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? ORDER BY L.NOMBRE;");
        $query->bind_param('i', $idusuario);
    }
    else if (count($privilegio) == 1){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND G.PRIVILEGIOS = ? ORDER BY L.NOMBRE ASC;");
        $query->bind_param('ii', $idusuario, $privilegio[0]);
    }
    else if (count($privilegio) == 2){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (G.PRIVILEGIOS = ? OR G.PRIVILEGIOS = ?) ORDER BY L.NOMBRE ASC;");
        $query->bind_param('iii', $idusuario, $privilegio[0], $privilegio[1]);
    }
    
    $query->execute();
    $resultQuery = $query->get_result();

    $returnsizeof = $resultQuery -> num_rows;
    
    // Liberamos memoria despues de obtener los resultados de la consulta
    $resultQuery -> free();

    return $returnsizeof;
}


function getListasAlphabeticOrderSearch($idusuario, $privilegio, $text, $pagina){
    global $conn;
    getConnection();

    $text = "%" . $text . "%";
    $offset = 6;
    $minimo = 0;
    $inicio = (($offset * $pagina) - $offset) > 0 ? ($offset * $pagina) - $offset : $minimo;

    // Las vistas con todos los filtros de privilegios son iguales a las de sin filtro
    if (count($privilegio) == 0 || count($privilegio) == 3){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.NOMBRE ASC  LIMIT ?, ?;");
        $query->bind_param('issii', $idusuario, $text, $text, $inicio, $offset);
    }
    else if (count($privilegio) == 1){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND G.PRIVILEGIOS = ? AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.NOMBRE ASC LIMIT ?, ?;");
        $query->bind_param('iissii', $idusuario, $privilegio[0], $text, $text, $inicio, $offset);
    }
    else if (count($privilegio) == 2){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (G.PRIVILEGIOS = ? OR G.PRIVILEGIOS = ?) AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.NOMBRE ASC  LIMIT ?, ?;");
        $query->bind_param('iiissii', $idusuario, $privilegio[0], $privilegio[1], $text, $text, $inicio, $offset);
    }
    
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $listas = $resultQuery->fetch_all(MYSQLI_ASSOC);
        // Liberamos memoria despues de obtener los resultados de la consulta
        $resultQuery -> free();
    }
    
    return $listas;
}

function getSizeOfListasAlphabeticOrderSearch($idusuario, $privilegio, $text){
    global $conn;
    getConnection();

    $text = "%" . $text . "%";

    // Las vistas con todos los filtros de privilegios son iguales a las de sin filtro
    if (count($privilegio) == 0 || count($privilegio) == 3){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.NOMBRE ASC;");
        $query->bind_param('iss', $idusuario, $text, $text);
    }
    else if (count($privilegio) == 1){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND G.PRIVILEGIOS = ? AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.NOMBRE ASC;");
        $query->bind_param('iiss', $idusuario, $privilegio[0], $text, $text);
    }
    else if (count($privilegio) == 2){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (G.PRIVILEGIOS = ? OR G.PRIVILEGIOS = ?) AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.NOMBRE ASC;");
        $query->bind_param('iiiss', $idusuario, $privilegio[0], $privilegio[1], $text, $text);
    }
    
    $query->execute();
    $resultQuery = $query->get_result();

    $returnsizeof = $resultQuery -> num_rows;

    // Liberamos memoria despues de obtener los resultados de la consulta
    $resultQuery -> free();

    
    return $returnsizeof;
}


function getListasDateOrder($idusuario, $privilegio, $pagina){
    global $conn;
    getConnection();

    $offset = 6;
    $minimo = 0;
    $inicio = (($offset * $pagina) - $offset) > 0 ? ($offset * $pagina) - $offset : $minimo;

    if (count($privilegio) == 0 || count($privilegio) == 3){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? ORDER BY L.FECHA DESC LIMIT ?, ?;");
        $query->bind_param('iii', $idusuario, $inicio, $offset);
    }
    else if (count($privilegio) == 1){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND G.PRIVILEGIOS = ? ORDER BY L.FECHA DESC LIMIT ?, ?;");
        $query->bind_param('iiii', $idusuario, $privilegio[0], $inicio, $offset);
    }
    else if (count($privilegio) == 2){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (G.PRIVILEGIOS = ? OR G.PRIVILEGIOS = ?) ORDER BY L.FECHA DESC LIMIT ?, ?;");
        $query->bind_param('iiiii', $idusuario, $privilegio[0], $privilegio[1], $inicio, $offset);
    }
    
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $listas = $resultQuery->fetch_all(MYSQLI_ASSOC);
        // Liberamos memoria despues de obtener los resultados de la consulta
        $resultQuery -> free();
    }

    
    return $listas;
}

function getSizeOfListasDateOrder($idusuario, $privilegio){
    global $conn;
    getConnection();

    if (count($privilegio) == 0 || count($privilegio) == 3){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? ORDER BY L.FECHA DESC;");
        $query->bind_param('i', $idusuario);
    }
    else if (count($privilegio) == 1){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND G.PRIVILEGIOS = ? ORDER BY L.FECHA DESC;");
        $query->bind_param('ii', $idusuario, $privilegio[0]);
    }
    else if (count($privilegio) == 2){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (G.PRIVILEGIOS = ? OR G.PRIVILEGIOS = ?) ORDER BY L.FECHA DESC;");
        $query->bind_param('iii', $idusuario, $privilegio[0], $privilegio[1]);
    }
    
    $query->execute();
    $resultQuery = $query->get_result();

    $returnsizeof =  $resultQuery -> num_rows;

    // Liberamos memoria despues de obtener los resultados de la consulta
    $resultQuery -> free();
    
    return $returnsizeof;
}

function getListasDateOrderSearch($idusuario, $privilegio, $text, $pagina){
    global $conn;
    getConnection();

    $text = "%" . $text . "%";
    $offset = 6;
    $minimo = 0;
    $inicio = (($offset * $pagina) - $offset) > 0 ? ($offset * $pagina) - $offset : $minimo;


    if (count($privilegio) == 0 || count($privilegio) == 3){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.FECHA DESC LIMIT ?, ?;");
        $query->bind_param('issii', $idusuario, $text, $text, $inicio, $offset);
    }
    else if (count($privilegio) == 1){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND G.PRIVILEGIOS = ? AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.FECHA DESC LIMIT ?, ? ;");
        $query->bind_param('iissii', $idusuario, $privilegio[0], $text, $text, $inicio, $offset);
    }
    else if (count($privilegio) == 2){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (G.PRIVILEGIOS = ? OR G.PRIVILEGIOS = ?) AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.FECHA DESC LIMIT ?, ?;");
        $query->bind_param('iiissii', $idusuario, $privilegio[0], $privilegio[1], $text, $text, $inicio, $offset);
    }
    
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $listas = $resultQuery->fetch_all(MYSQLI_ASSOC);
        // Liberamos memoria despues de obtener los resultados de la consulta
        $resultQuery -> free();
    }


    return $listas;
}


function getSizeOfListasDateOrderSearch($idusuario, $privilegio, $text){
    global $conn;
    getConnection();

    $text = "%" . $text . "%";

    if (count($privilegio) == 0 || count($privilegio) == 3){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.FECHA DESC;");
        $query->bind_param('iss', $idusuario, $text, $text);
    }
    else if (count($privilegio) == 1){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND G.PRIVILEGIOS = ? AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.FECHA DESC;");
        $query->bind_param('iiss', $idusuario, $privilegio[0], $text, $text);
    }
    else if (count($privilegio) == 2){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? AND (G.PRIVILEGIOS = ? OR G.PRIVILEGIOS = ?) AND (L.NOMBRE LIKE ? OR L.DESCRIPCION LIKE ?) ORDER BY L.FECHA DESC;");
        $query->bind_param('iiiss', $idusuario, $privilegio[0], $privilegio[1], $text, $text);
    }
    
    $query->execute();
    $resultQuery = $query->get_result();

    $returnsizeof = $resultQuery->num_rows;

    // Liberamos memoria despues de obtener los resultados de la consulta
    $resultQuery -> free();

    return $returnsizeof;
}

function getUserList(){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT * FROM USUARIOS");
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $usuarios = $resultQuery->fetch_all(MYSQLI_ASSOC);
        for($i=0; $i<count($usuarios); $i=$i+1){
            $usuarios[$i]['foto'] = "data:" . $usuarios[$i]['IMGTYPE'] .  ";base64," . base64_encode($usuarios[$i]['IMGBINARY']);
        }
    }


    return $usuarios;
}


function getUserPage($pagina){
    global $conn;
    getConnection();

    $offset = 5;
    $minimo = 0;
    $inicio = (($offset * $pagina) - $offset) > 0 ? ($offset * $pagina) - $offset : $minimo;

    $query = $conn->prepare("SELECT * FROM USUARIOS LIMIT ?, ?");
    $query->bind_param('ii', $inicio, $offset);
    $query->execute();
    $resultQuery = $query->get_result();

    print($inicio);

    if($resultQuery->num_rows > 0){
        echo "Hola";
        // Si estamos en pagina 1 el inicio es 0 por (1*5) - 5
        $usuarios = $resultQuery->fetch_all(MYSQLI_ASSOC);
        for($i=0; $i<count($usuarios); $i=$i+1){
            $usuarios[$i]['foto'] = "data:" . $usuarios[$i]['IMGTYPE'] .  ";base64," . base64_encode($usuarios[$i]['IMGBINARY']);
        }
    }

    return $usuarios;
}


function getSizeOfUserPage(){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT * FROM USUARIOS");
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        // Si estamos en pagina 1 el inicio es 0 por (1*5) - 5
        $usuarios = $resultQuery->fetch_all(MYSQLI_ASSOC);
        $size = count($usuarios);
    }

    return $size;
}



// funcion que actualiza la columna lastlogin a NOW()
function loginUser(){

}


function getTypeImg($img_name){
    $find_jpg = strpos($img_name, 'jpg');
    $find_jpeg = strpos($img_name, 'jpeg');
    $find_png = strpos($img_name, 'png');
    
    if ($find_jpg !== false){
        return  'image/jpg';
    }
    else if ($find_jpeg !== false){
        return 'image/jpeg'; 
    }
    else if ($find_png !== false){
        return 'image/png';
    }
    else {
        return  "";
    }
}

function checkValidImage($validacion){ 
    if (is_uploaded_file($validacion['tmp_name']) && !empty(getTypeImg($validacion['name']))) {
       // echo "Archivo ". $validacion['foto']['name'] ." subido con exito y formato correcto.\n";
       return true;
     } 
     else {
       //echo "Fallo en el archivo subido, foto no subida correctamente o formato invalido: Formatos: aceptados jpg, jpeg, png";
       return false;
     }
}


function formatImageB64($userfoto){
    // $type = getTypeImg($foto['foto']['name']);
    
    return "data:" . $userfoto['IMGTYPE'] .  ";base64," . base64_encode($userfoto['IMGBINARY']);

}

function acceptUser($validacion, $idusuario, $rolinsert){
    // Preparacion de la imagen

    $imgbin = get_contents($validacion['foto']);
    // Concatenacion de los campos de fechas
    $birthdate = $validacion['mes'] . "/" . $validacion['dia'] . "/" . $validacion['anyo'];
        
    if ($rolinsert == "Administrador"){

     }
     // Inicio de insercion
    global $conn;
    getConnection();
 
    $query = $conn -> prepare("UPDATE USUARIOS SET ROL = ?, ESTADO = ? WHERE IDUSUARIO = ?;");
    $query->bind_param('ssi', $rol, $estado, $idusuario);
    $query->execute();
 
 
    if ($query -> affected_rows != 1){
        echo "Error en la insercion";
    }
 }
 function insertUser($validacion, $rolinsert="No"){
    // Preparacion de la imagen
    $path = realpath("../assets/tmpusuarios/");

    // echo "PATH: " . $path . "\n";
    $pathname = $path . "/" . $_COOKIE['name'];

    // echo $pathname;

    // echo " NOMBRE FOTO SESION " . $_COOKIE['name'];

    $imgbin = file_get_contents($pathname);
    $imgtype = getTypeImg($_COOKIE['name']);

    // Concatenacion de los campos de fechas
    $date = strtotime($validacion['anyo'] . "-" .  $validacion['mes'] . "-" . $validacion['dia']);
    $birthdate = date('Y-m-d', $date);

     if ($rolinsert == "Administrador"){
         $rol = $validacion['ROL'];
         $estado = $validacion['ESTADO'];
     }else{
         $rol = "Usuario";
         $estado = "Inactivo";
     }
     // Inicio de insercion
    global $conn;
    getConnection();
      
    $query = $conn -> prepare("INSERT INTO USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY) VALUES  (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $query->bind_param('sssssssssssb', $validacion['DNI'], $validacion['NOMBRE'], $validacion['APELLIDOS'], $validacion['TELEFONO'], $validacion['EMAIL'], $validacion['PASSWORD'], $birthdate, $validacion['SEXO'], $rol, $estado, $imgtype, $imgbin);
    $query->send_long_data(11, $imgbin); 
    $query->execute();
 
 
    if ($query -> affected_rows != 1){
        echo "Error en la insercion";
    }
}

/**
  * Funcion de Borrado de Usuarios desde el usuario solicitado
  * Usa la COOKIE iusermod para obtener el ID en el controlador validar_user.php
  * 
  * 
  **/
function borrarUsuarioAdmin($idusuario){
     // Inicio de insercion
     global $conn;
     getConnection();
       
     $query = $conn -> prepare("DELETE FROM USUARIOS WHERE IDUSUARIO = ?;");
     $query->bind_param('i', $idusuario);
     $query->execute();
  
  
     if ($query -> affected_rows != 1){
         echo "Error en la insercion";
     }
}

/*
* Necesita la COOKIE name para poder obtener el nombre de la foto
* Validacion con admin necesita todos los campos
*
*
 */
function modificarUsuarioAdmin($validacion){
    // Preparacion de la imagen
    $path = realpath("../assets/tmpusuarios/");

    // echo "PATH: " . $path . "\n";
    $pathname = $path . "/" . $_COOKIE['name'];

    // echo $pathname;

    // echo " NOMBRE FOTO SESION " . $_COOKIE['name'];

    $imgbin = file_get_contents($pathname);
    $imgtype = getTypeImg($_COOKIE['name']);

    // Concatenacion de los campos de fechas
    $date = strtotime($validacion['anyo'] . "-" .  $validacion['mes'] . "-" . $validacion['dia']);
    $birthdate = date('Y-m-d', $date);

     
    // Inicio de insercion
    global $conn;
    getConnection();
      
    // $query = $conn -> prepare("UPDATE USUARIOS SET DNI = ?, NOMBRE = ?, APELLIDOS = ?, TELEFONO = ?, EMAIL = ?, PASSWORD = ?, FNAC = ?, SEXO = ?, ROL = ?, ESTADO = ?, IMGTYPE = ?, IMGBINARY = ?) WHERE IDUSUARIO = ?;");
    // $query->bind_param('sssssssssssb', $validacion['DNI'], $validacion['NOMBRE'], $validacion['APELLIDOS'], $validacion['TELEFONO'], $validacion['EMAIL'], $validacion['PASSWORD'], $birthdate, $validacion['SEXO'], $rol, $estado, $imgtype, $imgbin);
    // $query->send_long_data(11, $imgbin); 
    // $query->execute();
 
 
    if ($query -> affected_rows != 1){
        echo "Error en la insercion";
    }
}

function activateUser($idusuario){
    global $conn;
    getConnection();

    $activo = "Activo";

    $query = $conn->prepare("UPDATE USUARIOS SET ESTADO=? WHERE IDUSUARIO=?");
    $query->bind_param('si', $activo , $idusuario);
    $query->execute();

    if($query->affected_rows != 1){
        echo "Error al activar el usuario";
    }
}
 

function checkFormulario($validacion, $foto){
        if(empty($foto) || !checkValidImage($foto)){
            $validacion['errorfoto'] = "Fallo en el archivo subido, foto no subida correctamente o formato invalido: Formatos: aceptados jpg, jpeg, png";
            $validacion['sinerrores'] = false;
        }else{
            // Copia de foto a carpeta temporal
            $filename = $foto["name"];
            $tempname = $foto["tmp_name"];
            $folder = realpath("../assets/tmpusuarios/") . "/" . $filename;
            $folder_relative = "../assets/tmpusuarios/" . $filename;
            $validacion['foto'] = $folder_relative;
            
            if (!move_uploaded_file($tempname, $folder)) {
                echo "Failed to upload image!";
            }
        }
    
        if(empty($validacion["NOMBRE"]) || !preg_match('/^([A-ZÁÉÍÓÚ]{1}[a-zñáéíóú]+[\s]*)+$/', $validacion["NOMBRE"])){
            $validacion['errornombre'] = "Debe escribir su Nombre (solo letras)";
            $validacion['sinerrores'] = false;
        }
    
        if(empty($validacion["APELLIDOS"]) || !preg_match('/^([A-ZÁÉÍÓÚ]{1}[a-zñáéíóú]+[\s]*)+$/', $validacion["APELLIDOS"])){
          $validacion['errorapellido'] = "Debe escribir su Apellido (solo letras)";
          $validacion['sinerrores'] = false;
        }

        if(empty($validacion["DNI"]) || !preg_match('/^([0-9]{8}[A-Z]{1})$/', $validacion["DNI"])){
            $validacion['errordni'] = "El DNI no es válido";
            $validacion['sinerrores'] = false;
        }
    
        if(empty($validacion["EMAIL"]) || !filter_var($validacion["EMAIL"], FILTER_VALIDATE_EMAIL)){
            $validacion['erroremail'] = "Formato de email no valido (formato aceptado: john@example.com)";
            $validacion['sinerrores'] = false;
        }
    
        if(empty($validacion["TELEFONO"]) || !preg_match('/^(\+34|0034|34)?[ -]*(6|7|8|9)[ -]*([0-9][ -]*){8}$/', $validacion["TELEFONO"])){
            $validacion['errortelefono'] = "El numero de telefono no es correcto";
            $validacion['sinerrores'] = false;
        }
        
        if(empty($validacion['PASSWORD'])){
            $validacion['errorcontrasenia'] = "Debe escribir una contrasña";
            $validacion['sinerrores'] = false;
        }

        if(empty($validacion['contrasenia2']) || $validacion['contrasenia2'] != $validacion['PASSWORD']){
            $validacion['errorcontrasenia2'] = "Las contraseñas no coinciden. Pruebe de nuevo.";
            $validacion['sinerrores'] = false;
        }
    
        if(empty($validacion["SEXO"])){
          $validacion['errorsexo'] = "Debe seleccionar una opción obligatoriamente";
          $validacion['sinerrores'] = false;
        }
        
        if(empty($validacion['dia']) || (!preg_match( '/^[0-9]{1,2}$/', $validacion['dia']) && $validacion['dia'] > 0 && $validacion['dia'] <= 31)) {
            $validacion['errorfecha'] = "La fecha introducida no es correcta. Compruébela.";
        }

        if(empty($validacion['mes']) || (!preg_match( '/^[0-9]{1,2}$/', $validacion['mes']) && $validacion['mes'] > 0 && $validacion['mes'] <= 12)) {
            $validacion['errorfecha'] = "La fecha introducida no es correcta. Compruébela.";
        } 

        if(empty($validacion['anyo']) || (!preg_match('/^[0-9]{4}$/',$validacion['anyo']) && $validacion['anyo'] > 1900 && $validacion['anyo'] <= 2010)) {
            $validacion['errorfecha'] = "La fecha introducida no es correcta. Compruébela.";
        }
        
        if($validacion["boton"] == "Enviar" && (!isset($validacion['sinerrores']) || empty($validacion['sinerrores']))){
            $validacion['sinerrores']=true;
        }

        return $validacion;
}


?>