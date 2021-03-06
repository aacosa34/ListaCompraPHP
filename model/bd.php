<?php

$conn = NULL;

function getConnection(){
    global $conn;

    if ($conn == NULL){
        $conn = new mysqli("localhost", "adrianpedro2122", "W1F7SMzT", "adrianpedro2122");
        if ($conn->connect_error) {
            die('Error de conexión: ' . $conn->connect_error);
        }
      }

    return $conn;
}

function closeConnection(){
    global $conn;
    if ($conn != NULL){
        $conn -> close();
    }
}

function closeConn($con){
    if($con != NULL){
        $con->close();
    }
}

/* Backup de la BBDD completa */
function DB_backup() {
    global $conn;
    getConnection();
    // Obtener listado de tablas
    $tablas = array();
    $result = mysqli_query($conn,'SHOW TABLES');
    while ($row = mysqli_fetch_row($result))
      $tablas[] = $row[0];
    
    // Salvar cada tabla
    $salida = '';
    foreach ($tablas as $tab) {
        if ($tab == 'USUARIOS'){
            $result = mysqli_query($conn,'SELECT IDUSUARIO, DNI, NOMBRE, APELLIDOS, TELEFONO, EMAIL, PASSWORD, FNAC, SEXO, ROL, ESTADO, IMGTYPE, TO_BASE64(IMGBINARY), LASTLOGIN  FROM '.$tab);
        }
        else if ($tab == 'LISTA') {
            $result = mysqli_query($conn,'SELECT IDLISTA, IDPROPIETARIO, NOMBRE, DESCRIPCION, FECHA, IMGTYPE, TO_BASE64(IMGBINARY) FROM '.$tab);
        }
        else {
            $result = mysqli_query($conn,'SELECT * FROM '.$tab);
        }

        $num = mysqli_num_fields($result);
        
        $salida .= 'DROP TABLE IF EXISTS '.$tab.';';
        $row2 = mysqli_fetch_row(mysqli_query($conn,'SHOW CREATE TABLE '.$tab));
        $salida .= "\n\n".$row2[1].";\n\n";
        
        while ($row = mysqli_fetch_row($result)) {
            $salida .= 'INSERT INTO '.$tab.' VALUES(';
            // Recorre las columnas
            for ($j=0; $j < $num; $j++) {
                if (!is_null($row[$j])) {
                    if (isset($row[$j])){
                        if ($j == $num - 2 && $tab == 'USUARIOS'){
                            $salida .= 'FROM_BASE64("'.$row[$j].'")';
                        }
                        else if($j == $num - 1 && $tab == 'LISTA'){
                            $salida .= 'FROM_BASE64("'.$row[$j].'")';
                        }   
                        else {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
        
                            $salida .= '"'.$row[$j].'"';

                        }
                    }
                    else {
                        $salida .= '""';
                    }   
                } else
                    $salida .= 'NULL';
                if ($j < ($num-1))
                    $salida .= ',';
            }
            $salida .= ");\n";
        }
        $salida .= "\n\n\n";
    }

    //save file
    $f = fopen('/~adrianpedro2122/proyecto/bd/backup/conn-backup-'.time().'-'.(md5(implode(',',$tablas))).'.sql','w+');
    fwrite($f,$salida);
    fclose($f);
    closeConnection();
    return $salida;
}

/* Restauración de la BBDD completa */
function DB_init() {
    global $conn;
    getConnection();
    $error = "";
    $show = mysqli_query($conn, 'SHOW TABLES');

    if($show -> num_rows == 0){
        $conn->autocommit(TRUE);

        // Restauramos los trigers
        $sql = file_get_contents("./bd/reset.sql");
        $queries = explode(';',$sql);
        foreach ($queries as $q) {
            $q = trim($q);
            if ($q!='' and !mysqli_query($conn,$q)){
                $error .= mysqli_error($conn);
            }
        }
        // Restauramos los trigers
    
        $sql = file_get_contents("./bd/triggers.sql");
        $queries = explode('//',$sql);
        foreach ($queries as $q) {
            $q = trim($q);
            if ($q!='' and !mysqli_query($conn,$q))
                $error .= mysqli_error($conn);
            
        }

        // Restauramos los datos por defecto
        mysqli_query($conn,'SET FOREIGN_KEY_CHECKS=0');
        $sql = file_get_contents("./bd/basicdata.sql");
        $queries = explode(';',$sql);
        foreach ($queries as $q) {
            $q = trim($q);
            if ($q!='' and !mysqli_query($conn,$q))
                $error .= mysqli_error($conn);    
        }        
    }    

    return $error;
}


/* Restauración de la BBDD completa */
function DB_restore($f) {
    global $conn;
    getConnection();
    $error = "";


    mysqli_query($conn,'SET FOREIGN_KEY_CHECKS=0');
    DB_delete($conn);
    $conn->autocommit(TRUE);
    $error = [];
    $sql = file_get_contents($f);
    $queries = explode(';',$sql);
    foreach ($queries as $q) {
        $q = trim($q);
        if ($q!='' and !mysqli_query($conn,$q))
            $error .= mysqli_error($conn);
        
    }
    mysqli_commit($conn);
    mysqli_query($conn,'SET FOREIGN_KEY_CHECKS=1');

    // Restauramos los trigers

    $sql = file_get_contents("../bd/triggers.sql");
    $queries = explode('//',$sql);
    foreach ($queries as $q) {
        $q = trim($q);
        if ($q!='' and !mysqli_query($conn,$q))
            $error .= mysqli_error($conn);
        
    }

    closeConnection();
    return $error;
}

/* Borrar el contenido de las tablas de la BBDD */
function DB_delete($conn) {
    $result = mysqli_query($conn,'SHOW TABLES');
    while ($row = mysqli_fetch_row($result)){
        mysqli_query($conn,'DELETE FROM '.$row[0]); 
        mysqli_query($conn,'ALTER TABLE '.$row[0].' AUTO_INCREMENT=1'); 
    }
          
    mysqli_commit($conn);
}

function DB_borrar() {
    global $conn;

    getConnection();
    $result = mysqli_query($conn,'SHOW TABLES');

    while ($row = mysqli_fetch_row($result)){
        if($row[0] === 'USUARIOS'){
            mysqli_query($conn,'DELETE FROM '.$row[0].' WHERE IDUSUARIO!=1'); 
        }else{
            mysqli_query($conn,'DELETE FROM '.$row[0]);  
        }
        
        mysqli_query($conn,'ALTER TABLE '.$row[0].' AUTO_INCREMENT=1'); 
    }
          
    mysqli_commit($conn);
    closeConnection();
}


function getLog(){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT FECHA,DESCRIPCION FROM LOG ORDER BY FECHA DESC");
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows>0){
        $log = $resultQuery->fetch_all(MYSQLI_ASSOC);
    }

    return $log;
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

function getUserByEmail($email){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT * FROM USUARIOS WHERE EMAIL=?");
    $query->bind_param("s", $email);
    $query->execute();

    $resultQuery = $query->get_result();

    if($resultQuery->num_rows>0){
        $usuario = $resultQuery->fetch_assoc();
    }

    return $usuario;
}

function isUserInList($idusuario, $idlista){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT * FROM GRUPOS WHERE IDUSUARIO=? AND IDLISTA=?");
    $query->bind_param("ii", $idusuario, $idlista);
    $query->execute();

    $resultQuery = $query->get_result();

    if($resultQuery->num_rows>0){
        return true;
    }else{
        return false;
    }
}

function addUserToList($idusuario, $privilegios, $idlista){
    global $conn;
    getConnection();

    $query = $conn->prepare("INSERT INTO GRUPOS(IDLISTA,IDUSUARIO,PRIVILEGIOS) VALUES (?,?,?)");
    $query->bind_param("iis", $idlista, $idusuario, $privilegios);
    $query->execute();

    if($query->affected_rows != 1){
        echo "No se ha añadido correctamente el usuario";
    }
}


function getEstadisticas(){
    global $conn;
    getConnection();
    $estadisticas = '';

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
    $historico_productos = '';

    $query = $conn->prepare("SELECT NOMBRE,CANTIDAD FROM HISTORICOPRODUCTOS");
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

    $query = $conn->prepare("SELECT IDUSUARIO,ESTADO FROM USUARIOS WHERE EMAIL=? AND PASSWORD=?");
    $query->bind_param('ss', $email, $password);
    $query->execute();
    $resultQuery = $query->get_result();
    $num_rows = $resultQuery->num_rows;
    // Si encontramos un usuario, devolvemos true
    $usuario = $resultQuery->fetch_assoc();

    // Liberamos memoria despues de obtener los resultados de la consulta
    $resultQuery -> free();

    if($usuario['ESTADO'] == "Activo" && $num_rows > 0){
        return $usuario;
    }
    else{
        return '';
    }

}

function actualizarSesion($idusuario){
    global $conn;
    getConnection();

    $query = $conn->prepare('UPDATE USUARIOS SET LASTLOGIN = NOW() WHERE IDUSUARIO=?');
    $query->bind_param('i', $idusuario);
    $query-> execute();
    
}

function getListaById($idlista, $idusuario){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT L.IDLISTA,NOMBRE,DESCRIPCION,PRIVILEGIOS,IMGBINARY,IMGTYPE FROM LISTA AS L INNER JOIN GRUPOS AS G ON G.IDLISTA=L.IDLISTA WHERE IDUSUARIO=? AND L.IDLISTA=?");
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

function borrarLista($idlista){
    global $conn;
    getConnection();

    $query = $conn->prepare("DELETE FROM LISTA WHERE IDLISTA=?");
    $query->bind_param('i', $idlista);
    $query->execute();

    if($query->affected_rows != 1){
        echo "Error al borrar la lista";
    }
}

function getProductosLista($idlista){
    global $conn;
    getConnection();
    $productos = '';

    $query = $conn->prepare("SELECT P.NOMBRE,LP.CANTIDAD,LP.IDPRODUCTO FROM LISTAPRODUCTOS AS LP INNER JOIN PRODUCTOS AS P ON P.IDPRODUCTO=LP.IDPRODUCTO WHERE LP.IDLISTA=?");
    $query->bind_param('i', $idlista);
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $productos = $resultQuery->fetch_all(MYSQLI_ASSOC);

        $resultQuery -> free();
    }

    return $productos;
}

function borrarProductoById($idproducto, $idlista){
    global $conn;
    getConnection();

    $query = $conn->prepare("DELETE FROM LISTAPRODUCTOS WHERE IDPRODUCTO=? AND IDLISTA=?");
    $query->bind_param('ii', $idproducto, $idlista);
    $query->execute();

    if($query->affected_rows != 1){
        echo $query->affected_rows;
        echo "Error en el borrado de productos";
    }
}

function getProductoById($idproducto, $idlista){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT NOMBRE,CANTIDAD FROM LISTAPRODUCTOS AS L INNER JOIN PRODUCTOS AS P ON L.IDPRODUCTO=P.IDPRODUCTO WHERE P.IDPRODUCTO=? AND IDLISTA=?");
    $query->bind_param('ii', $idproducto, $idlista);
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $producto = $resultQuery->fetch_assoc();
        // Liberamos memoria despues de obtener los resultados de la consulta
        $resultQuery -> free();
    }

    return $producto;
    
}

function modificarValoresProducto($producto, $idlista){
    global $conn;
    getConnection();

    // Actualizamos la cantidad del producto en la lista
    if(isset($producto['CANTIDAD']) && !empty($producto['CANTIDAD'])){
        $query = $conn->prepare("UPDATE LISTAPRODUCTOS SET CANTIDAD=? WHERE IDLISTA=? AND IDPRODUCTO=?");
        $query->bind_param('iii', $producto['CANTIDAD'], $idlista, $producto['IDPRODUCTO']);
        $query->execute();
    }

    // Cambiamos el nombre del producto
    if(isset($producto['NOMBRE']) && !empty($producto['NOMBRE'])){
        $query = $conn->prepare("UPDATE PRODUCTOS SET NOMBRE=? WHERE IDPRODUCTO=?");
        $query->bind_param('si', $producto['NOMBRE'], $producto['IDPRODUCTO']);
        $query->execute();
    }

}

function marcarProductoComprado($nombre, $cantidad){
    global $conn;
    getConnection();

    // Insertamos en el historico el producto comprado
    $query = $conn->prepare("INSERT INTO HISTORICOPRODUCTOS (NOMBRE,CANTIDAD) VALUES (?, ?)");
    $query->bind_param('si', $nombre, $cantidad);
    $query->execute();

    if($query->affected_rows != 1){
        echo "Error en la insercion en el historico de productos";
    }else{
        $mensaje_log = "Comprado producto ".$nombre;

        $query = $conn->prepare("INSERT INTO LOG (FECHA,DESCRIPCION) VALUES (NOW(),?)");
        $query->bind_param('s', $mensaje_log);
        $query->execute();

        if($query->affected_rows != 1){
            echo "Error en la insercion en el historico de productos";
        }
    }

    


}


function getUsersOnList($idlista){
    global $conn;
    getConnection();

    $query = $conn->prepare("SELECT U.NOMBRE, U.IDUSUARIO FROM USUARIOS AS U INNER JOIN GRUPOS AS G ON U.IDUSUARIO=G.IDUSUARIO WHERE G.IDLISTA=?");
    $query->bind_param('i', $idlista);
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        $usuariosOnList = $resultQuery->fetch_all(MYSQLI_ASSOC);

        $resultQuery -> free();
    }

    return $usuariosOnList;
}

function eliminarColaborador($idusuario, $idlista){
    global $conn;
    getConnection();

    $query = $conn->prepare("DELETE FROM GRUPOS WHERE IDUSUARIO=? AND IDLISTA=?");
    $query->bind_param('ii', $idusuario, $idlista);
    $query->execute();
}

function insertarProducto($producto, $idlista){
    global $conn;
    getConnection();

    // Insertamos el producto en la tabla de productos por su nombre
    $query = $conn->prepare("INSERT INTO PRODUCTOS(NOMBRE) VALUES (?)");
    $query->bind_param('s', $producto['NOMBRE']);
    $query->execute();

    if($query->affected_rows != 1){
        echo "Error en la insercion de producto";
    }

    // Obtenemos el ID del producto que acabamos de introducir
    $query = $conn->prepare("SELECT IDPRODUCTO FROM PRODUCTOS WHERE NOMBRE=?");
    $query->bind_param('s', $producto['NOMBRE']);
    $query->execute();

    $resultQuery = $query->get_result();

    if($resultQuery->num_rows == 1){
        $idproducto = $resultQuery->fetch_assoc();

        $resultQuery->free();
    }

    $query = $conn->prepare("INSERT INTO LISTAPRODUCTOS VALUES (?, ?, ?)");
    $query->bind_param('iii', $idlista, $idproducto['IDPRODUCTO'], $producto['CANTIDAD']);
    $query->execute();

    if($query->affected_rows != 1){
        echo "Error en la insercion de producto";
    }
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
    $listas = '';

    $offset = 3;
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
    $listas = '';

    $text = "%" . $text . "%";
    $offset = 3;
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
    $listas = '';

    $offset = 3;
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
    $listas = '';

    $text = "%" . $text . "%";
    $offset = 3;
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
    $usuarios = '';

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

//*************************************************************************************************************************

// METODOS DE CONTROL DE USUARIOS Y SU PAGINACION

//*************************************************************************************************************************

//*************************************************************************************************************************

// METODOS DE PAGINACION

//*************************************************************************************************************************
/*
 * Funcion de obtencion de la informacion del tamanio total de la tabla para limitar el n de paginas, es dependencia de
 * getUserPage($pagina)
 */
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


/*
 * Funcion de paginacion de la lista de usuarios
 * $pagina es la pagina que debe pasarse como parametro por defecto es la 1 si no espera ningun parametro
 * El control de la ultima pagina debe hacerse ya que la vista necesita saberla para desactivar los botones
 */
function getUserPage($pagina){
    global $conn;
    getConnection();

    $offset = 5;
    $minimo = 0;
    $inicio = (($offset * $pagina) - $offset) > 0 ? ($offset * $pagina) - $offset : $minimo;

    // Necesitamos controlar la ultima pagina
    $tamanio = getSizeOfUserPage();
    $ultima_pag = ceil($tamanio/$offset);

    $query = $conn->prepare("SELECT * FROM USUARIOS LIMIT ?, ?");
    $query->bind_param('ii', $inicio, $offset);
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        // Si estamos en pagina 1 el inicio es 0 por (1*5) - 5
        $usuarios = $resultQuery->fetch_all(MYSQLI_ASSOC);
        for($i=0; $i<count($usuarios); $i=$i+1){
            $usuarios[$i]['foto'] = "data:" . $usuarios[$i]['IMGTYPE'] .  ";base64," . base64_encode($usuarios[$i]['IMGBINARY']);
        }
    }

    // Preparamos el retorno con los usuarios y la informacion de la ultima pagina
    $listado_paginado['pagina'] = $usuarios;
    $listado_paginado['ultima_pag'] = $ultima_pag;

    return $listado_paginado;
}

//*************************************************************************************************************************

// METODOS DE PROCESADO DE IMAGENES

//*************************************************************************************************************************

/*
 * Funcion obtencion de la informacion del tipo de imagen
 * $img_name es el nombre completo del fichero de imagen que contiene el tipo de archivo como *.jpg
 * Dependencia de
 * checkValidImage($imagen)
 */
function getTypeImg($img_name){
    if(!empty($img_name)){
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
            // Tipo no valido
            return  "";
        }
    }
    else {
        // Error imagen no encontrada
        return  "";
    }

}

/*
 * Funcion de comprobacion de formato de imagen valido
 * $imagen es el $_FILES o la variable de imagen que contiene el binario completo y abierto
 * Dependencia de
 *
 */
function checkValidImage($imagen){
    if (is_uploaded_file($imagen['tmp_name']) && !empty(getTypeImg($imagen['name']))) {
       // echo "Archivo ". $validacion['foto']['name'] ." subido con exito y formato correcto.\n";
       return true;
     }
     else {
       //echo "Fallo en el archivo subido, foto no subida correctamente o formato invalido: Formatos: aceptados jpg, jpeg, png";
       return false;
     }
}

/*
 * Funcion de comprobacion de formato de imagen valido
 * $img_name es el nombre completo del fichero de imagen que contiene el tipo de archivo como *.jpg
 * Dependencia de
 *
 */
function formatImageB64($foto){
    return "data:" . $foto['IMGTYPE'] .  ";base64," . base64_encode($foto['IMGBINARY']);
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

function insertList($valores){
    // Preparacion de la imagen - Indicamos la ruta de almacenamiento temporal de fotos
    $path = realpath("../assets/tmplistas/");
    $pathname = $path . "/" . $_COOKIE['nombrefoto'];

    // Carga de imagen y obtencion del tipo para su insercion
    $imgbin = file_get_contents($pathname);
    $imgtype = getTypeImg($_COOKIE['nombrefoto']);

    // Inicio de insercion
    global $conn;
    getConnection();

    $query = $conn -> prepare("INSERT INTO LISTA (NOMBRE, IDPROPIETARIO, DESCRIPCION, FECHA, IMGTYPE, IMGBINARY) VALUES  (?, ?, ?, NOW(), ?, ?)");
    $query->bind_param('sissb', $valores['NOMBRE'], $_SESSION['idusuario'], $valores['DESCRIPCION'], $imgtype, $imgbin);
    $query->send_long_data(4, $imgbin); 
    $query->execute();

    if ($query -> affected_rows != 1){
        echo "Error en la insercion";
    }


    $query = $conn->prepare("SELECT IDLISTA FROM LISTA WHERE NOMBRE=? AND IDPROPIETARIO=?");
    $query->bind_param('si', $valores['NOMBRE'], $_SESSION['idusuario']);
    $query->execute();
    $resultQuery = $query->get_result();

    if($resultQuery->num_rows > 0){
        // Si encontramos la lista, cogemos su id
        $lista = $resultQuery->fetch_assoc();
    }

    $idusuario = $_SESSION['idusuario'];
    $idlista = $lista['IDLISTA'];
    $privilegio = "Propietario";

    $query = $conn -> prepare("INSERT INTO GRUPOS (IDLISTA, IDUSUARIO, PRIVILEGIOS) VALUES (?, ?, ?)");
    $query->bind_param('iis', $idlista, $idusuario, $privilegio);
    $query->execute();

    if ($query -> affected_rows != 1){
        echo "Error en la insercion";
    }


    // Borramos la cookie una vez terminada la insercion
    unset($_COOKIE['nombrefoto']);
}

function checkLista($validacion, $foto){
     $validacion['sinerrores'] = true;

     if(empty($foto) || !checkValidImage($foto)){
         $validacion['errorfoto'] = "Fallo en el archivo subido, formato invalido: Formatos: aceptados jpg, jpeg, png";
         $validacion['sinerrores'] = false;
     }else{
         // Copia de foto a carpeta temporal
         $filename = $foto["name"];
         $tempname = $foto["tmp_name"];
         $folder = realpath("../assets/tmplistas/") . "/" . $filename;
         $folder_relative = "../assets/tmplistas/" . $filename;
         $validacion['foto'] = $folder_relative;

         if (!move_uploaded_file($tempname, $folder)) {
            $validacion['errorfoto'] = "Fallo foto no subida correctamente";
            $validacion['sinerrores'] = false;
        }
     }

     if(empty($validacion["NOMBRE"])){
         $validacion['errornombre'] = "Debe escribir el nombre de la lista";
         $validacion['sinerrores'] = false;
     }

     return $validacion;
}

function modificarInfoLista($validacion, $idusuario, $idlista){
    global $conn;
    getConnection();

    // Preparacion de la imagen - Indicamos la ruta de almacenamiento temporal de fotos
    $path = realpath("../assets/tmplistas/");
    $pathname = $path . "/" . $_COOKIE['nombrefoto'];

    // Carga de imagen y obtencion del tipo para su insercion
    $imgbin = file_get_contents($pathname);
    $imgtype = getTypeImg($_COOKIE['nombrefoto']);
    
    $descripcion = $validacion['DESCRIPCION'] ?? '';
    
    $query = $conn->prepare("UPDATE LISTA SET NOMBRE=?, DESCRIPCION=?, IMGTYPE=?, IMGBINARY=? WHERE IDPROPIETARIO=? AND IDLISTA=?");
    $query->bind_param('sssbii', $validacion['NOMBRE'], $descripcion, $imgtype, $imgbin, $idusuario, $idlista);
    $query->send_long_data(3, $imgbin);
    $query->execute();


    if ($query -> affected_rows != 1){
        echo "Error en la modificacion de lista";
    }

}


 /**
   * COOKIES esperada $_COOKIE['nombrefoto'] para poder realizar la insercion correcta de la foto
   */
 function insertUser($validacion, $rolinsert="No"){
    // Preparacion de la imagen - Indicamos la ruta de almacenamiento temporal de fotos
    $path = realpath("../assets/tmpusuarios/");
    $pathname = $path . "/" . $_COOKIE['nombrefoto'];
    // Carga de imagen y obtencion del tipo para su insercion
    $imgbin = file_get_contents($pathname);
    $imgtype = getTypeImg($_COOKIE['nombrefoto']);
    // Concatenacion de los campos de fechas
    $date = strtotime($validacion['anyo'] . "-" .  $validacion['mes'] . "-" . $validacion['dia']);
    $birthdate = date('Y-m-d', $date);

    // Campos de ROL y ESTADO solo controlados por el administrador
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

    // Borramos la cookie una vez terminada la insercion
    unset($_COOKIE['nombrefoto']);

}

/**
  * Funcion de Borrado de Usuarios desde el usuario solicitado
  * Usa la COOKIE iusermod para obtener el ID en el controlador validar_user.php
  **/
function borrarUsuarioAdmin($idusuario){
     // Inicio de insercion
     global $conn;
     getConnection();

     $query = $conn->prepare("DELETE FROM LISTA WHERE IDPROPIETARIO=?");
     $query->bind_param('i', $idusuario);
     $query->execute();

     $query = $conn -> prepare("DELETE FROM USUARIOS WHERE IDUSUARIO = ?");
     $query->bind_param('i', $idusuario);
     $query->execute();

     if ($query -> affected_rows != 1){
         echo "Error en el borrado de usuario";
     }
}

/**
  * Funcion de modificar informacion del usuario como administrador
  * Su controlador es: validar_user
  * Necesita la COOKIE nombrefoto para poder obtener el nombre de la foto
  * Validacion con admin necesita todos los campos
  **/
function modificarUsuario($validacion, $idusuario){
    // Preparacion de la imagen - Indicamos la ruta de almacenamiento temporal de fotos
    $path = realpath("../assets/tmpusuarios/");
    $pathname = $path . "/" . $_COOKIE['nombrefoto'];
    // Carga de imagen y obtencion del tipo para su insercion
    $imgbin = file_get_contents($pathname);
    $imgtype = getTypeImg($_COOKIE['nombrefoto']);

    // Inicio de Actualizacion
    global $conn;
    getConnection();

    $query = $conn -> prepare("UPDATE USUARIOS SET PASSWORD = ?, EMAIL = ?, TELEFONO = ?, IMGTYPE = ?, IMGBINARY = ? WHERE IDUSUARIO = ?;");
    $query->bind_param('ssssbi', $validacion['PASSWORD'], $validacion['EMAIL'], $validacion['TELEFONO'], $imgtype, $imgbin, $idusuario);
    $query->send_long_data(4, $imgbin);
    $query->execute();


    if ($query -> affected_rows != 1){
        echo "Error en la insercion";
    }
}

/* Modificar todos los campos como administrador */
function modificarUsuarioAdmin($validacion, $idusuario){
    // Preparacion de la imagen - Indicamos la ruta de almacenamiento temporal de fotos
    $path = realpath("../assets/tmpusuarios/");
    $pathname = $path . "/" . $_COOKIE['nombrefoto'];
    // Carga de imagen y obtencion del tipo para su insercion
    $imgbin = file_get_contents($pathname);
    $imgtype = getTypeImg($_COOKIE['nombrefoto']);
    // Concatenacion de los campos de fechas
    $date = strtotime($validacion['anyo'] . "-" .  $validacion['mes'] . "-" . $validacion['dia']);
    $birthdate = date('Y-m-d', $date);

    // Inicio de Actualizacion
    global $conn;
    getConnection();

    $query = $conn -> prepare("UPDATE USUARIOS SET DNI = ?, NOMBRE = ?, APELLIDOS = ?,
        TELEFONO = ?, EMAIL = ?, PASSWORD = ?,  FNAC = ?,
        SEXO = ?, ROL = ?, ESTADO = ?, IMGTYPE = ?,
        IMGBINARY = ? WHERE IDUSUARIO = ?;");
    $query->bind_param('sssssssssssbs', $validacion['DNI'], $validacion['NOMBRE'], $validacion['APELLIDOS'],
        $validacion['TELEFONO'], $validacion['EMAIL'], $validacion['PASSWORD'], $birthdate,
        $validacion['SEXO'], $validacion['ROL'], $validacion['ESTADO'], $imgtype,
        $imgbin, $idusuario);
    $query->send_long_data(11, $imgbin);
    $query->execute();


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
    $validacion['sinerrores'] = true;

        if(empty($foto) || !checkValidImage($foto)){
            $validacion['errorfoto'] = "Fallo en el archivo subido, formato invalido: Formatos: aceptados jpg, jpeg, png";
            $validacion['sinerrores'] = false;
        }else{
            // Copia de foto a carpeta temporal
            $filename = $foto["name"];
            $tempname = $foto["tmp_name"];
            $folder = realpath("../assets/tmpusuarios/") . "/" . $filename;
            $folder_relative = "../assets/tmpusuarios/" . $filename;
            $validacion['foto'] = $folder_relative;

            if (!move_uploaded_file($tempname, $folder)) {
                $validacion['errorfoto'] = "Fallo foto no subida correctamente";
                $validacion['sinerrores'] = false;
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
            $validacion['errordia'] = true;
            $validacion['sinerrores'] = false;
        }

        if(empty($validacion['mes']) || (!preg_match( '/^[0-9]{1,2}$/', $validacion['mes']) && $validacion['mes'] > 0 && $validacion['mes'] <= 12)) {
            $validacion['errorfecha'] = "La fecha introducida no es correcta. Compruébela.";
            $validacion['errormes'] = true;
            $validacion['sinerrores'] = false;
        }

        if(empty($validacion['anyo']) || (!preg_match('/^[0-9]{4}$/',$validacion['anyo']) && $validacion['anyo'] > 1900 && $validacion['anyo'] <= 2010)) {
            $validacion['errorfecha'] = "La fecha introducida no es correcta. Compruébela.";
            $validacion['erroranyo'] = true;
            $validacion['sinerrores'] = false;
        }

        // if($validacion["boton"] == "Enviar" && (!isset($validacion['sinerrores']) || empty($validacion['sinerrores']))){
        //     $validacion['sinerrores']=true;
        // }

        return $validacion;
}


?>
