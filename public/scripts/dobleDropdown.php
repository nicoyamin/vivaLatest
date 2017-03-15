<?php
require_once '../../vendor/autoload.php';

$sel_id=$_GET['sel_id'];


//$sel_id=5;


/// Preventing injection attack ////
/*if(!is_numeric($sel_id)){
    echo "Data Error";
    exit;
}*/
/// end of checking injection attack ////
require "db.inc.php";



$result=$viva->select("Producto", ["idProducto","Nombre_producto","Cantidad_unitaria_producto","Unidad_producto","Existencia_producto","Stock_minimo_producto"],["AND"=>["idProveedor"=>"$sel_id","Habilitado"=>"Si"]]);

$main = array('data'=>$result);

echo json_encode($main);


?>