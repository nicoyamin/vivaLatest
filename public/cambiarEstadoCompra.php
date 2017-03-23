<?php

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';

include 'scripts/db.inc.php';

include("acceso.inc.php");

use JasperPHP\JasperPHP;



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


if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST["id"]))
    {
        $estadoCompra=$viva->select("Compra(C)",["[>]Proveedor(P)"=>["C.idProveedor"=>"idProveedor"],"[>]Compra_estado(CE)"=>["C.Estado"=>"idCompra_estado"]],
            [
                "C.idCompra",
                "P.Proveedor_nombre",
                "C.Estado",
                "CE.Descripcion",
                "CE.Accion"
            ],
            ["C.idCompra"=>$_POST["id"]]);

        //Chequear el estado actual de la orden de compra para decidir que estados posibles devolver.

        //Si el estado es "Cotizacion generada" o "cotizacion enviada"  solo puede traer "Cotizacion Generada", "Cotizacion enviada" y "Cancelada"
        if($estadoCompra[0]["Estado"]==1 || $estadoCompra[0]["Estado"]==2 )
        {
            $cotizacion=true;
            $nuevoEstado=$viva->select("Compra_estado",["idCompra_estado", "Descripcion", "Accion"], ["idCompra_estado"=>[1,2,8]]);
        }


        //Si el estado esta entre "Orden de compra generada" y "compra recibida", traer todas las opciones entre "Orden de compra generada" y "devuelta"
        else $nuevoEstado=$viva->select("Compra_estado",["idCompra_estado", "Descripcion", "Accion"], ["idCompra_estado"=>[3,4,5,6,8,9]]);

    }



    if(isset($_POST["estadoRadio"]))
    {
        //Si el estado nuevo esta entre "cotizacion generada" y "compra recibida", solo actualizar el estado en tabla compras
        /*if(in_array($_POST['estadoRadio'], array(1, 2, 3, 4, 5, 6)) )
        {
            $viva->update("Compra",["Estado"=>$_POST["estadoRadio"]],["idCompra"=>$_POST["idCompra"]]);
        }*/

        //Si el estado nuevo es "cancelada" o "devuelta", actualzar tabla compras, pero tambien quitar los productos de esta compra de stock entrante en tabla productos
        if(in_array($_POST['estadoRadio'], array(8,9)) )
        {
            //si la cancelacion viene de una cotizacion, solo actualizar en tabla compras
            if(in_array($_POST['estadoAnterior'], array(1,2))) $viva->update("Compra",["Estado"=>$_POST["estadoRadio"]],["idCompra"=>$_POST["idCompra"]]);

            else
            {
                //Obtener los detalles de la compra para restarlos del stock entrante, y actualizar uno por uno los registros de productos.
                $detallesCompas=$viva->select("Compra_Producto",["idProducto","Cantidad"],["idCompra"=>$_POST["idCompra"]]);

                foreach($detallesCompas as $stock)
                {
                    $viva->update("Producto",
                        [
                            "Stock_entrante_producto[-]" => $stock["Cantidad"]
                        ], [
                            "idProducto" => $stock["idProducto"]
                        ]);
                }
            }
        }


        $viva->update("Compra",["Estado"=>$_POST["estadoRadio"]],["idCompra"=>$_POST["idCompra"]]);
        //Si la compra se cancela, devuelve o cierra, actualizar el estado de la orden de compra correspondietne a "Inactiva"
        if(in_array($_POST['estadoRadio'], array(7,8,9))) $viva->update("Orden_de_compra",["Estado"=>"Inactiva"],["idCompra"=>$_POST["idCompra"]]);
        header('Location: gestionCompras.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Cambio estado compra</title>
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
    <h2>Detalles de Compra</h2>


    <h4>Compra Nro: <label><?php echo $estadoCompra[0]["idCompra"]?></label></h4>
    <h4>Proveedor: <label><?php echo $estadoCompra[0]["Proveedor_nombre"]?></label></h4>
    <h4>Estado Actual: <label><?php echo $estadoCompra[0]["Descripcion"]." - ".$estadoCompra[0]["Accion"]?></label></h4>

    <h2>Seleccione nuevo estado: </h2>

        <?php if($estadoCompra[0]["Estado"]==7 || $estadoCompra[0]["Estado"]==8 || $estadoCompra[0]["Estado"]==9):?>
            <h2>Esta compra fue cerrada, devuelta o cancelada. No puede modificarse su estado</h2>
        <?php else:?>
        <form action="" id="formEstado" method="post">
            <?php foreach($nuevoEstado as $estado):?>
                <div>
                    <input type="radio" name="estadoRadio" value="<?php echo $estado["idCompra_estado"]; ?>"><?php echo $estado["Descripcion"]." - ".$estado["Accion"]?>

                </div>
                <br>
            <?php endforeach ?>
            <input type="hidden" name="estadoAnterior"  value="<?php echo $estadoCompra[0]["Estado"]; ?>">
            <input type="hidden" name="idCompra"  value="<?php echo $estadoCompra[0]["idCompra"]; ?>">
            <input type="submit" value="Cambiar Estado"  onclick="return confirm('Seguro que desea cambiar el estado?')">

            <?php if(!isset($cotizacion)):?>
                <input type="button" onclick="submitForm('altaStock.php')" value="Dar de alta en stock" />
            <?php endif?>

        </form>
        <?php endif ?>


</div>

<script type="text/javascript">
    function submitForm(action)
    {
        document.getElementById('formEstado').action = action;
        document.getElementById('formEstado').submit();
    }
</script>

</body>

</html>
