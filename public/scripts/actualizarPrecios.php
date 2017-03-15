<?php
require_once '../../vendor/autoload.php';
include 'db.inc.php';

$arrayPrecios=$_POST["precios"];
$arrayId=$_POST["ids"];
$filasAfectadas=0;

for($i=0;$i<sizeof($arrayPrecios);$i++)
{
    $filasAfectadas=$filasAfectadas+$viva->update("Producto",["Precio_venta_producto"=>$arrayPrecios[$i]],["idProducto"=>$arrayId[$i]]);
}



print json_encode($filasAfectadas);