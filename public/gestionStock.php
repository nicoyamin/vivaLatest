<?php
require_once '../vendor/autoload.php';

include 'scripts/db.inc.php';

require_once  'acceso.inc.php';

if (!userIsLoggedIn())
{
    include 'login.php';
    exit();
}
if (!userHasRole('2') && !userHasRole('3'))
{
    $error = 'Solo administradores pueden acceder a esta pagina';
    include 'accesoDenegado.php';
    exit();
}


$listaProds=$viva->select("Producto",["idProducto","Nombre_producto", "Cantidad_unitaria_producto","Unidad_producto"]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["btnBuscar"]) )
{
dump($_POST);
    if($_POST["prodCod"]!="")
    {
        //Buscar producto por codigo de barras
        $producto=$viva->select("Producto(P)",["[><]Proveedor(PR)"=>["P.idProveedor"=>"idProveedor"]], [
            "P.idProducto",
            "P.Nombre_producto",
            "P.Cantidad_unitaria_producto",
            "P.Unidad_producto",
            "P.Existencia_producto",
            "P.Stock_entrante_producto",
            "P.Stock_minimo_producto",
            "PR.Proveedor_nombre"
        ],["Codigo_barras_producto"=>$_POST["prodCod"]]);
    }

    else if($_POST["prodNombre"]!=0)
    {
        //Buscar producto por nombre
        $producto=$viva->select("Producto(P)",["[><]Proveedor(PR)"=>["P.idProveedor"=>"idProveedor"]], [
            "P.idProducto",
            "P.Nombre_producto",
            "P.Cantidad_unitaria_producto",
            "P.Unidad_producto",
            "P.Existencia_producto",
            "P.Stock_entrante_producto",
            "P.Stock_minimo_producto",
            "PR.Proveedor_nombre"
        ],["idProducto"=>$_POST["prodNombre"]]);
    }

    $conceptos=$viva->select("Stock_concepto",["idStock_concepto","Descripcion"]);
    $movimientos=$viva->select("Stock_tipo_movimientos",["idStock_tipo_movimientos","Descripcion"]);



}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["modificar"]) )
{
    //Traer Stock actual de producto a modificar
    $unidades=$viva->select("Producto",["Existencia_producto"],["idProducto"=>$_POST["idProducto"]]);
    $suma=$unidades[0]["Existencia_producto"];
   //Si el movimiento es entrada, sumar cantidad a existencias del producto
    if($_POST["movimiento"]==1)
   {
        $suma=$suma+$_POST["cantidad"];
        $viva->update("Producto",["Existencia_producto[+]"=>$_POST["cantidad"]],["idProducto"=>$_POST["idProducto"]]);

   }
    //Si el movimiento es salida, restar cantidad a existencias del producto
    if($_POST["movimiento"]==2)
    {
        $suma=$suma-$_POST["cantidad"];
        $viva->update("Producto",["Existencia_producto[-]"=>$_POST["cantidad"]],["idProducto"=>$_POST["idProducto"]]);
    }

    //Si el movimiento es traslado, las cantidades no se modifican
    //insertar a tabla de movimeintos

    $viva->insert("Stock_movimientos",["idProducto"=>$_POST["idProducto"],"Cantidad"=>$_POST["cantidad"],"Fecha"=>date('Y-m-d H:i:s'),"idStock_tipo_movimientos"=>$_POST["movimiento"],"idStock_concepto"=>$_POST["concepto"],"Observaciones"=>$_POST["observaciones"], "Stock_luego"=>$suma, "Stock_antes"=>$unidades[0]["Existencia_producto"]]);
}

?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Alta Stock</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
    <script src="js/vendor/jquery-1.12.0.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>

<?php include 'scripts/logout.inc.php'; ?>

<div class="container">
    <h2>Gestion de Stock</h2>

    <form action="" method="post">
        <div><label>Buscar por codigo de barras: <input type="text" name="prodCod" value=""></label></div>
        <div><label>Buscar por nombre de producto:</label>
            <select class="selectUnico" name="prodNombre" id="prodNombre" data-placeholder="Seleccione">
                <option value="0">Seleccione producto</option>
                <?php foreach($listaProds as $listaProd):?>
                    <option value="<?php echo $listaProd["idProducto"];?>"><?php echo $listaProd["Nombre_producto"]." ".$listaProd["Cantidad_unitaria_producto"]." ".$listaProd["Unidad_producto"];?></option>
                <?php endforeach; ?>
            </select></div>
        <input type="submit" name="btnBuscar" value="Buscar" action="">
    </form>

    <?php if(isset($producto)): ?>

        <h2>Producto seleccionado</h2>

    <div><label>Producto: <?php echo $producto[0]["Nombre_producto"]." ".$producto[0]["Cantidad_unitaria_producto"]." ".$producto[0]["Unidad_producto"];?></label></div>
    <div><label>Proveedor: <?php echo $producto[0]["Proveedor_nombre"];?></label></div>
    <div><label>Stock Actual: <?php echo $producto[0]["Existencia_producto"];?></label></div>
    <div><label>Stock Entrante: <?php echo $producto[0]["Stock_entrante_producto"];?></label></div>
    <div><label>Stock minimo: <?php echo $producto[0]["Stock_minimo_producto"];?></label></div>

        <h2>Detalles del movimiento</h2>

        <form action="" method="post">
            <input type="hidden" name="idProducto" value="<?php echo $producto[0]["idProducto"];?>">
            <div><label>movimiento: </label>
                <select class="selectUnico" name="movimiento" id="movimiento" data-placeholder="Seleccione">
                    <?php foreach($movimientos as $movimiento):?>
                        <option value="<?php echo $movimiento["idStock_tipo_movimientos"];?>"><?php echo $movimiento["Descripcion"];?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div><label>Concepto: </label>
                <select class="selectUnico" name="concepto" id="concepto" data-placeholder="Seleccione">
                    <?php foreach($conceptos as $concepto):?>
                        <option value="<?php echo $concepto["idStock_concepto"];?>"><?php echo $concepto["Descripcion"];?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label>Cantidad: <input type="number" name="cantidad" value="0"></label></div>
            <div><label>Observaciones(opcional): <input type="text" name="observaciones"></label></div>
            <div><input type="submit" name="modificar" value="Aceptar"></div>
        </form>

    <?php endif; ?>


</div>

<script type="text/javascript">
    $(document).ready(function(){

        $("#prodNombre").select2();
        $("#concepto").select2();
        $("#movimiento").select2();
    });

</script>

</body>

</html>

