<?php

require_once '../../vendor/autoload.php';
include 'db.inc.php';

$accion=$_POST["accion"];

if($accion==1 || $accion==2)
{
    $id = $_POST["id"];

    if($accion==1)      //Traer detalles del producto seleccionado en el dropdown de la pagina de ventas
    {
        $producto = $viva->select("Producto", "*", ["idProducto" => $id]);
    }

    elseif($accion==2)  //Traer detalles del producto seleccionado mediante codigo de barras
    {
        $producto = $viva->select("Producto", "*", ["Codigo_barras_producto" => $id]);
    }
    $detalles = array();

    foreach ($producto as $detalle) {
        $e = array();

        $e["Producto"] = $detalle["Nombre_producto"]." ".$detalle["Cantidad_unitaria_producto"]." ".$detalle["Unidad_producto"];
        $e["Id"] = $detalle["idProducto"];
        $e["Stock"] = $detalle["Existencia_producto"];
        $e["Precio"] = $detalle["Precio_venta_producto"];

        array_push($detalles, $e);
    }

    echo json_encode($detalles);

}

if($accion==3)      //traer clientes habiltados a realizar compras a cuenta corriente
{
    $clientes = $viva->select("Cuenta_corriente(CC)", ["[>]Cliente(C)" => ["CC.idCliente" => "idCliente"]],
        [
            "C.Nombre",
            "CC.idCuenta_corriente"
        ],
        ["CC.Estado" => "Activa"]
    );

    $main = array('data'=>$clientes);

    echo json_encode($main);
}

if($accion==4)      //Traer detalles de la cuenta corriente seleccionada
{
    $id = $_POST["id"];
    $clientes = $viva->select("Cuenta_corriente(CC)",
        [
            "CC.idCuenta_corriente",
            "CC.Margen",
            "CC.Balance"
        ],
        ["CC.idCuenta_corriente" => $id]
    );

    $detalles = array();

    foreach ($clientes as $detalle) {
        $e = array();

        $e["id"] = $detalle["idCuenta_corriente"];
        $e["margen"] = $detalle["Margen"];
        $e["balance"] = $detalle["Balance"];

        array_push($detalles, $e);
    }


    echo json_encode($detalles);
}

