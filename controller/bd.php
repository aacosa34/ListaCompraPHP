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


function getListasAlphabeticOrder($idusuario, $privilegio){
    global $conn;
    getConnection();

    // Las vistas con todos los filtros de privilegios son iguales a las de sin filtro
    if (count($privilegio) == 0 || count($privilegio) == 3){
        $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA = L.IDLISTA WHERE G.IDUSUARIO = ? ORDER BY L.NOMBRE ASC;");
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

    if($resultQuery->num_rows > 0){
        $listas = $resultQuery->fetch_all(MYSQLI_ASSOC);
        // Liberamos memoria despues de obtener los resultados de la consulta
        $resultQuery -> free();
    }

    
    return $listas;
}



function getListasAlphabeticOrderSearch($idusuario, $privilegio, $text){
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

    if($resultQuery->num_rows > 0){
        $listas = $resultQuery->fetch_all(MYSQLI_ASSOC);
        // Liberamos memoria despues de obtener los resultados de la consulta
        $resultQuery -> free();
    }
    
    return $listas;
}


function getListasDateOrder($idusuario, $privilegio){
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

    if($resultQuery->num_rows > 0){
        $listas = $resultQuery->fetch_all(MYSQLI_ASSOC);
        // Liberamos memoria despues de obtener los resultados de la consulta
        $resultQuery -> free();
    }

    
    return $listas;
}

function getListasDateOrderSearch($idusuario, $privilegio, $text){
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

    if($resultQuery->num_rows > 0){
        $listas = $resultQuery->fetch_all(MYSQLI_ASSOC);
        // Liberamos memoria despues de obtener los resultados de la consulta
        $resultQuery -> free();
    }


    return $listas;
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



?>