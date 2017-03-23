<?php

require_once '../../vendor/autoload.php';
include 'db.inc.php';


$db = new Viva\BaseDatos($viva);

$data = json_decode(stripslashes($_POST['prodCantidad']));

//$viva->update("Producto",["Stock_entrante_producto[-]"=>$_POST[""]])

