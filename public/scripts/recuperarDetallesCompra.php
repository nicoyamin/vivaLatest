<?php

require_once '../../vendor/autoload.php';
include 'db.inc.php';

use JasperPHP\JasperPHP;

$db = new Viva\BaseDatos($viva);

if($_POST["opcion"]==1) {
    $detallesCompra = $viva->select("Compra_Producto(CP)", ["[>]Producto(P)" => ["CP.idProducto" => "idProducto"]],
        [
            "CP.Cantidad",
            "P.Nombre_producto",
            "P.Cantidad_unitaria_producto",
            "P.Unidad_producto",
            "P.Precio_unitario_producto"
        ],
        ["CP.idCompra" => $_POST["compra"]]
    );

    $compra = array();

    foreach ($detallesCompra as $detalle) {
        $e = array();

        $e["Cantidad"] = $detalle["Cantidad"];
        $e["Producto"] = $detalle["Nombre_producto"] . " " . $detalle["Cantidad_unitaria_producto"] . " " . $detalle["Unidad_producto"];
        $e["Precio"] = $detalle["Precio_unitario_producto"];
        $e["Total"] = $detalle["Precio_unitario_producto"] * $detalle["Cantidad"];

        array_push($compra, $e);
    }

    print json_encode($compra);
}

if($_POST["opcion"]==2)
{
    $detallesCompra=$viva->select("Orden_de_compra",["idOrden_de_compra","numero","Estado"],["idCompra"=>$_POST["compra"]]);

    $compra = array();

    if(empty($detallesCompra))
    {
        $e=array();

        $e["Numero"] = "No generada";
        $e["Estado"] = "No generada";
        array_push($compra, $e);
    }

    else {
        foreach ($detallesCompra as $detalle) {
            $e = array();
            $e["idCompra"] = $detalle["idOrden_de_compra"];
            $e["Numero"] = $detalle["numero"];

            $e["Estado"] = $detalle["Estado"];


            array_push($compra, $e);
        }
    }
    print json_encode($compra);
}

if($_POST["opcion"]==3)
{
    $viva->update("Compra",["Estado"=>$_POST["nuevoEstado"]],["idCompra"=>$_POST["compra"]]);

}

if($_POST["opcion"]==4)
{
    $detallesCompra = $viva->select("Compra(C)", ["[><]Proveedor(P)" => ["C.idProveedor" => "idProveedor"]],
        [
            "P.Proveedor_nombre",
            "C.Condiciones_pago",
            "C.Lugar_entrega",
            "C.Fecha_entrega",
            "C.Enviar_por"
        ],
        ["C.idCompra" => $_POST["compra"]]
    );

    $compra = array();

    foreach ($detallesCompra as $detalle) {
        $e = array();

        $e["proveedor"] = $detalle["Proveedor_nombre"];
        $e["pago"] = $detalle["Condiciones_pago"];
        $e["lugarE"] = $detalle["Lugar_entrega"];
        $e["fechaE"] = $detalle["Fecha_entrega"];
        $e["envio"] = $detalle["Enviar_por"];

        array_push($compra, $e);
    }
    $jasper = new JasperPHP;

    $database = [
        'driver' => 'mysql',
        'username' => 'homestead',
        'password'=> 'secret',
        'database'=> 'VIVA',
        'host' => '127.0.0.1',
        'port' => '3306'
    ];

    $stockEntrante=$viva->select("Compra_Producto",["idProducto","Cantidad"],["idCompra"=>$_POST["compra"]]);

    foreach($stockEntrante as $stock)
    {
        $viva->update("Producto",
        [
            "Stock_entrante_producto[+]" => $stock["Cantidad"]
        ], [
            "idProducto" => $stock["idProducto"]
        ]);
    }


    $nroOrden=$viva->select("Orden_de_compra",["numero"],["numero[>=]"=>1, "ORDER"=>["numero"=>"DESC"],"LIMIT"=>1]);
    $nroOrden=$nroOrden[0]["numero"]+1;
    $viva->insert("Orden_de_compra",[
        "idCompra"=>$_POST["compra"],
        "numero"=>$nroOrden,
        "Fecha"=>date("Y-m-d"),
        "Estado"=>"Activa"
    ]);

    $proveedor=$compra[0]["proveedor"];
    $nombre=$proveedor."-Orden_de_compra_Numero-".$nroOrden;

    $jasper->process(
    // Ruta y nombre de archivo de entrada del reporte
        '../reports/compras/ordenCompra.jasper',
        '../reports/compras/'.$nombre, // Ruta y nombre de archivo de salida del reporte (sin extensión)
        array('pdf'), // Formatos de salida del reporte
        array('proveedor' => $compra[0]["proveedor"],
            'pago'=>$compra[0]["pago"],
            'lugar'=>$compra[0]["lugarE"],
            'fechaEntrega'=>$compra[0]["fechaE"],
            'envio'=>$compra[0]["envio"],
            'fecha'=>date("d/m/Y"),
            'idCompra'=>$_POST["compra"],
            'nroOrden'=>$nroOrden
        ) // Parámetros del reporte
        ,$database
    )->execute();

    $viva->update("Compra",["Estado"=>3],["idCompra"=>$_POST["compra"]]); //Cambiar estadio a orden de compra generada

}
?>

