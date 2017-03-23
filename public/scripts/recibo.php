<?php

require_once '../../vendor/autoload.php';
include 'db.inc.php';

use JasperPHP\JasperPHP;

session_start();
date_default_timezone_set("America/Argentina/Salta");

$usuario = new Viva\Usuario($viva);

$datosUsuario = $usuario->selectNombreUsuarioPassword($viva, $_SESSION["usuario"], $_SESSION["password"]);
$empleado = $datosUsuario[0]["Nombre"] . " " . $datosUsuario[0]["Apellido"];

$nroRecibo = $viva->select("Recibo", ["Numero"], ["Numero[>=]" => 1, "ORDER" => ["Numero" => "DESC"], "LIMIT" => 1]);
if ($nroRecibo != null) $nroRecibo = $nroRecibo[0]["Numero"] + 1;
else $nroRecibo = 1;

$importe = $_POST["importeRecibo"];
$observaciones = $_POST["observaciones"];
$cliente=$_POST["cliente"];
$medioPago=$_POST["formaPago"];
$idCliente=$_POST["idCliente"];
$fecha = date('d/m/Y h:ia');

$nombreReporte = "Recibo_numero_" . $nroRecibo;

$jasper = new JasperPHP;
$jasper->process(
// Ruta y nombre de archivo de entrada del reporte
    '../reports/recibos/recibo.jasper',
    '../reports/recibos/' . $nombreReporte, // Ruta y nombre de archivo de salida del reporte (sin extensión)
    array('pdf'), // Formatos de salida del reporte
    array('nroRecibo' => $nroRecibo,
        'empleado' => $empleado,
        'Importe' => $importe,
        'observaciones' => $observaciones,
        'fecha' => $fecha,
        'cliente'=>$cliente,
        'formaPago'=>$medioPago
    )
)->execute();

$nombre=$nombreReporte.".pdf";

$ruta='reports/recibos/'.$nombre;

$atras="entradaCaja.php";
$link="ventanaDescarga.php?nombre=$nombre&ruta=$ruta&atras=$atras";

//Insertar registro de recibo en tabla Recibo y obtener el id del mismo
$idRecibo=$viva->insert("Recibo",["Numero"=>$nroRecibo,"Observaciones"=>$observaciones, "Fecha"=>date("Y-m-d")]);

//Insertar registro a movimiento de cuenta corriente y actualizar balance y fecha de cuenta corriete
//Actualizar la mas antigua venta del cliente que tenga saldo pendiente, restando de este campo el valor importe. Si el saldo cae a cero, cerrar la venta
//Si el valor de importe es mayor a cero, repetir el proceso hasta que sea cero.



$actualizar=$viva->pdo->prepare("CALL pagoCuentaCorriente(:importe,:cliente, :reciboId, :medioPago)"); //Stored procedure que hace lo descrito anteriormente

$actualizar->bindParam(':importe', $importe, PDO::PARAM_STR);
$actualizar->bindParam(':cliente', $idCliente, PDO::PARAM_INT);
$actualizar->bindParam(':reciboId', $idRecibo, PDO::PARAM_INT);
$actualizar->bindParam(':medioPago', $medioPago, PDO::PARAM_STR);

$actualizar->execute();

if(isset($_SESSION["idTurno"])) $idTurno=$_SESSION["idTurno"];
else $idTurno=0;

//Insertar registro de movimiento de caja en tabla caja
$viva->insert("Caja",["idUsuario"=>$datosUsuario[0]["idUsuario"], "Fecha"=>date("Y-m-d H:i:s"), "Tipo"=>"Entrada", "Concepto"=>2,"Debe"=>0.00, "Haber"=>$importe,"Observaciones"=>$observaciones,"Referencia"=>3,"idReferencia"=>$nroRecibo, "idTurno"=>$idTurno]);


echo json_encode($link);