<?php
require_once "../vendor/autoload.php";
include_once "../model/bd.php";

$loader = new \Twig\Loader\FilesystemLoader('../view');
$twig = new \Twig\Environment($loader);

session_start();

if(isset($_SESSION['idusuario'])){
    $user = getUserById($_SESSION['idusuario']);

    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $_SESSION['idlista'] = $_GET['idlista']; // Guardamos el id de la lista en la sesion
    }
    $lista = getListaById($_SESSION['idlista'], $_SESSION['idusuario']);
    $foto = formatImageB64($lista);
    $productos = getProductosLista($lista['IDLISTA']);
    $usuarios_compartidos = getUsersOnList($lista['IDLISTA']);

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if($_POST['boton'] == "Añadir"){
            $producto['NOMBRE'] = $_POST['nomProducto'];
            $producto['CANTIDAD'] = $_POST['cantProducto'];

            insertarProducto($producto, $_SESSION['idlista']);
        }else if($_POST['boton'] == "Modificar"){
            $prodActualizado = Array('IDPRODUCTO' => $_POST['idproducto'],
                                     'NOMBRE' => $_POST['nombre'],
                                     'CANTIDAD' => $_POST['cantidad']);
            modificarValoresProducto($prodActualizado, $_SESSION['idlista']);
        }else if($_POST['boton'] == "Comprado"){
            $prod = getProductoById($_POST['idproducto'], $_SESSION['idlista']);
            marcarProductoComprado($prod['NOMBRE'], $prod['CANTIDAD']);
            // Borramos el producto tras la actualizacion en el historico
            borrarProductoById($_POST['idproducto'], $_SESSION['idlista']);
        }
        else if($_POST['boton'] == "Borrar"){
            borrarProductoById($_POST['idproducto'], $_SESSION['idlista']);
        }
    }

    $productos = getProductosLista($_SESSION['idlista']);
    
}

echo $twig->render('vista_lista.html', ['lista' => $lista,
                                         'foto' => $foto,
                                         'productos' => $productos,
                                         'usuarios' => $usuarios_compartidos,
                                         'user' => $user]);
?>