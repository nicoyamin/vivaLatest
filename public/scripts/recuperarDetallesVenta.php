<?php

require_once '../../vendor/autoload.php';
include 'db.inc.php';



$detallesVenta = $viva->select("Venta_Producto(VP)", ["[>]Producto(P)" => ["VP.idProducto" => "idProducto"]],
        [
            "VP.Cantidad",
            "VP.Precio",
            "P.Nombre_producto",
            "P.Cantidad_unitaria_producto",
            "P.Unidad_producto"
        ],
        ["VP.idVenta" => $_POST["venta"]]
);

    $venta = array();

    foreach ($detallesVenta as $detalle) {
        $e = array();

        $e["Cantidad"] = $detalle["Cantidad"];
        $e["Producto"] = $detalle["Nombre_producto"] . " " . $detalle["Cantidad_unitaria_producto"] . " " . $detalle["Unidad_producto"];
        $e["Precio"] = $detalle["Precio"];
        $e["Total"] = $detalle["Precio"] * $detalle["Cantidad"];

        array_push($venta, $e);
    }

    print json_encode($venta);



?>

