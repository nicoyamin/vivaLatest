<?php
require_once '../../vendor/autoload.php';
include 'db.inc.php';


$detallesCierre = $viva->select("Caja(C)", ["[>]Caja_operacion(CO)" => ["C.Concepto" => "idCaja_operacion"], "[>]Caja_comprobante(CC)"=>["C.Referencia"=>"idComprobante"]],
    [
        "C.Fecha",
        "C.Tipo",
        "CO.Descripcion(Concepto)",
        "C.Debe",
        "C.Haber",
        "CC.Descripcion(Comprobante)",
        "C.idReferencia"
    ],
    ["C.idTurno" => $_POST["turno"]]
);

$cierre = array();

foreach ($detallesCierre as $detalle) {
    $e = array();

    $e["Hora"] = date("h:i:s", strtotime($detalle["Fecha"]));
    $e["Tipo"] = $detalle["Tipo"];
    $e["Concepto"] = $detalle["Concepto"];
    if($detalle["Debe"]!="0") $e["Importe"] = $detalle["Debe"];
    if($detalle["Haber"]!="0") $e["Importe"] = $detalle["Haber"];
    $e["Comprobante"] = $detalle["Comprobante"];
    $e["Referencia"] = $detalle["idReferencia"];

    array_push($cierre, $e);
}

print json_encode($cierre);