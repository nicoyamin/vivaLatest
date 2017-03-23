<?php
require_once '../../vendor/autoload.php';
include 'db.inc.php';


$db = new Viva\BaseDatos($viva);

$categoria = [
        'Categoria_nombre' => $_POST['nombre'],
        'Categoria_descripcion' => $_POST['descripcion'],
    ];

    $db->insertar('Producto_categoria', $categoria);
?>