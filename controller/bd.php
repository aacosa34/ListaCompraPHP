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

function insertUser(){
    global $conn;
    getConnection();

    $query = $conn -> prepare("INSERT INTO USUARIOS (DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, IMGBINARY) VALUES  (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $query->bind_param('sssssssssssb', $dni, $nombre, $apellidos, $telefono, $email, $password, $fechanac, $sexo, $rol, $estado, $imgtype, $imgbin);
    $query->execute();


    if ($query -> affected_rows != 1){
        echo "Error en la insercion";
    }
}

// funcion que actualiza la columna lastlogin a NOW()
function loginUser(){

}


function getTypeImg($img_name){
    $find_jpg = strpos($img_name, 'jpg');
    $find_jpeg = strpos($img_name, 'jpeg');
    $find_png = strpos($img_name, 'png');
    
    if ($find_jpg !== false){
        return 'image/jpg';
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
    if (is_uploaded_file($validacion['foto']['tmp_name']) && !empty(getTypeImg($validacion['foto']['name']))) {
       // echo "Archivo ". $validacion['foto']['name'] ." subido con exito y formato correcto.\n";
       return true;
     } 
     else {
       //echo "Fallo en el archivo subido, foto no subida correctamente o formato invalido: Formatos: aceptados jpg, jpeg, png";
       return false;
     }
}

function checkFormulario($validacion, $foto){
        if(empty($foto) || !checkValidImage($foto)){
            $validacion['errorfoto'] = "Fallo en el archivo subido, foto no subida correctamente o formato invalido: Formatos: aceptados jpg, jpeg, png";
            $validacion['sinerrores'] = false;
        }
    
        if(empty($validacion["nombre"]) || !preg_match('/^([A-ZÁÉÍÓÚ]{1}[a-zñáéíóú]+[\s]*)+$/', $validacion["nombre"])){
            $validacion['errornombre'] = "Debe escribir su Nombre (solo letras)";
            $validacion['sinerrores'] = false;
        }
    
        if(empty($validacion["apellido"]) || !preg_match('/^([A-ZÁÉÍÓÚ]{1}[a-zñáéíóú]+[\s]*)+$/', $validacion["apellido"])){
          $validacion['errorapellido'] = "Debe escribir su Apellido (solo letras)";
          $validacion['sinerrores'] = false;
        }

        if(empty($validacion["dni"]) || !preg_match('/^([0-9]{8}[A-Z]{1})$/', $validacion["dni"])){
            $validacion['errordni'] = "El DNI no es válido";
            $validacion['sinerrores'] = false;
        }
    
        if(empty($validacion["email"]) || !filter_var($validacion["email"], FILTER_VALIDATE_EMAIL)){
            $validacion['erroremail'] = "Formato de email no valido (formato aceptado: john@example.com)";
            $validacion['sinerrores'] = false;
        }
    
        if(empty($validacion["email2"]) || $validacion["email2"] != $validacion["email"]){
            $valicacion['erroremail2'] = "Emails no coincidentes. Por favor, revise ambos email";
            $validacion['sinerrores'] = false;
        }
    
        if(empty($validacion["telefono"]) || !preg_match('/^(\+34|0034|34)?[ -]*(6|7|8|9)[ -]*([0-9][ -]*){8}$/', $validacion["telefono"])){
            $validacion['errortelefono'] = "El numero de telefono no es correcto";
            $validacion['sinerrores'] = false;
        }
        
        if(empty($validacion['contrasenia'])){
            $validacion['errorcontrasenia'] = "Debe escribir una contrasña";
            $validacion['sinerrores'] = false;
        }

        if(empty($validacion['contrasenia2']) || $validacion['contrasenia2'] != $validacion['contrasenia']){
            $validacion['errorcontrasenia2'] = "Las contraseñas no coinciden. Pruebe de nuevo.";
            $validacion['sinerrores'] = false;
        }
    
        if(empty($validacion["sexo"])){
          $validacion['errorsexo'] = "Debe seleccionar una opción obligatoriamente";
          $validacion['sinerrores'] = false;
        }
    
        if($validacion["boton"] == "Enviar datos" && !isset($validacion['sinerrores'])){
            $validacion['sinerrores']=true;
        }

        return $validacion;
}


?>