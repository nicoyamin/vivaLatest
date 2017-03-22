<?php
require_once '../../vendor/autoload.php';
include 'db.inc.php';

use JasperPHP\JasperPHP;

session_start();
date_default_timezone_set("America/Argentina/Salta");

$jasper = new JasperPHP;

$usuario = new Viva\Usuario($viva);

$nroComprobante = $viva->select("Caja_comprobante_alivio", ["Numero"], ["Numero[>=]" => 1, "ORDER" => ["Numero" => "DESC"], "LIMIT" => 1]);
if ($nroComprobante != null) $nroComprobante = $nroComprobante[0]["Numero"] + 1;
else $nroComprobante = 1;

$datosUsuario = $usuario->selectNombreUsuarioPassword($viva, $_SESSION["usuario"], $_SESSION["password"]);
$nombre = $datosUsuario[0]["Nombre"] . " " . $datosUsuario[0]["Apellido"];

$importe = $_POST["importe"];
$observaciones = $_POST["observaciones"];



$fecha = date('d/m/Y h:ia');

$nombreReporte = "Alivio_de_caja_numero_" . $nroComprobante;

$jasper->process(
// Ruta y nombre de archivo de entrada del reporte
    '../reports/alivios/comprobanteAlivio.jasper',
    '../reports/alivios/' . $nombreReporte, // Ruta y nombre de archivo de salida del reporte (sin extensión)
    array('pdf'), // Formatos de salida del reporte
    array('nroComprobante' => $nroComprobante,
        'nombre' => $nombre,
        'importe' => $importe,
        'observaciones' => $observaciones,
        'fecha' => $fecha
    )
)->execute();

$nombre=$nombreReporte.".pdf";

$ruta='reports/alivios/'.$nombre;

$atras="entradaCaja.php";
$link="ventanaDescarga.php?nombre=$nombre&ruta=$ruta&atras=$atras";

if(isset($_SESSION["idTurno"])) $idTurno=$_SESSION["idTurno"];
else $idTurno=0;

$viva->insert("Caja_comprobante_alivio",["Numero"=>$nroComprobante,"Observaciones"=>$observaciones, "Fecha"=>date("Y-m-d")]);

$viva->insert("Caja",["idUsuario"=>$datosUsuario[0]["idUsuario"], "Fecha"=>date("Y-m-d H:i:s"), "Tipo"=>"Entrada", "Concepto"=>$_POST["concepto"],"Debe"=>0.00, "Haber"=>$_POST["importe"],"Observaciones"=>$observaciones,"Referencia"=>$_POST["referencia"],"idReferencia"=>$nroComprobante,"idTurno"=>$idTurno]);

echo json_encode($link);
?>