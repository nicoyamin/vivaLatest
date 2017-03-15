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

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(isset($_POST["idCompra"]))
    {

        //Recuperar informacion de la compra
        $estadoCompra=$viva->select("Compra(C)",["[>]Proveedor(P)"=>["C.idProveedor"=>"idProveedor"],"[>]Compra_estado(CE)"=>["C.Estado"=>"idCompra_estado"]],
            [
                "C.idCompra",
                "P.Proveedor_nombre",
                "C.Estado",
                "CE.Descripcion"
            ],
            ["C.idCompra"=>$_POST["idCompra"]]);

        //Recuperar prodcutos que constituyen la compra
        $detallesCompra = $viva->select("Compra_Producto(CP)", ["[>]Producto(P)" => ["CP.idProducto" => "idProducto"]],
            [
                "CP.Cantidad_pendiente",
                "P.idProducto",
                "P.Nombre_producto",
                "P.Cantidad_unitaria_producto",
                "P.Unidad_producto",
                "P.Perecedero"
            ],
            ["CP.idCompra" => $_POST["idCompra"]]
        );

        $compra = array();

        foreach ($detallesCompra as $detalle) {
            $e = array();

            $e["Cantidad"] = $detalle["Cantidad_pendiente"];
            $e["Producto"] = $detalle["Nombre_producto"] . " " . $detalle["Cantidad_unitaria_producto"] . " " . $detalle["Unidad_producto"];
            $e["Perecedero"] = $detalle["Perecedero"];
            $e["idProducto"]=$detalle["idProducto"];

            array_push($compra, $e);
        }

    }



    if(isset($_POST["action"]))
    {
        $alta=array();

        $cant=count($_POST["idProducto"]);

        //Convertir en array asociativo
        for($i=0;$i<$cant;$i++)
        {
            $alta[$i]["idProducto"]=$_POST["idProducto"][$i];
            $alta[$i]["cantidad"]=$_POST["cantidadR"][$i];
            $alta[$i]["fechaV"]=$_POST["fechaV"][$i];
            $alta[$i]["destino"]=$_POST["destino"][$i];
        }

        //Modificar las tablas segun corresponda
        foreach($alta as $producto)
        {
            //Obtener cantidad actual del producto a dar de alta
            $unidades=$viva->select("Producto",["Existencia_producto"],["idProducto"=>$producto["idProducto"]]);
            $suma=$unidades[0]["Existencia_producto"]+$producto["cantidad"];
            //Actualizar tabla productos, trasladando stock entrante a Existencias
            $viva->update("Producto",["Existencia_producto[+]"=>$producto["cantidad"], "Stock_entrante_producto[-]"=>$producto["cantidad"]],["idProducto"=>$producto["idProducto"]]);
            //Insertar productos al stock como movimientos de compra
            $viva->insert("Stock_movimientos",["idProducto"=>$producto["idProducto"],"Cantidad"=>$producto["cantidad"], "Fecha"=>date('Y-m-d H:i:s'),"idStock_tipo_movimientos"=>1, "idStock_concepto"=>4, "Stock_luego"=>$suma, "Stock_antes"=>$unidades[0]["Existencia_producto"]]);

            //Actualizar cantidad pendiente, util para compras parciales
            $viva->update("Compra_Producto",["Cantidad_pendiente[-]"=>$producto["cantidad"]],["AND"=>["idCompra"=>$_POST["codCompra"],"idProducto"=>$producto["idProducto"]]]);

        }

        if($_POST["completa"]=="true")
        {
            //Cerrar compra y orden de compra
            $viva->update("Orden_de_compra",["Estado"=>"Cerrada"],["idCompra"=>$_POST["codCompra"]]);
            $viva->update("Compra",["Estado"=>7],["idCompra"=>$_POST["codCompra"]]);

        }

        if($_POST["completa"]=="false")
        {
            //Actualizar orden de compra a pendiente y compra a recibida, pero no cerrada
            $viva->update("Orden_de_compra",["Estado"=>"Entregas Pendientes"],["idCompra"=>$_POST["codCompra"]]);
            $viva->update("Compra",["Estado"=>6],["idCompra"=>$_POST["codCompra"]]);


        }

        header('Location: gestionCompras.php');
    }
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
    <h2>Alta de Stock</h2>


    <h4>Compra Nro: <label id="idCompra"><?php echo $estadoCompra[0]["idCompra"]?></label></h4>
    <h4>Proveedor: <label><?php echo $estadoCompra[0]["Proveedor_nombre"]?></label></h4>
    <h4>Estado Actual: <label><?php echo $estadoCompra[0]["Descripcion"]?></label></h4>

    <h2>Detalles de Compra: </h2>

    <form action="" id="formProd"  method="post">
        <table id="tablaDetalles" class="table table-striped">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad Ordenada</th>
                <th>Cantidad Recibida</th>
                <th>Fecha de Vencimiento</th>
                <th>Destino</th>


            </tr>
            </thead>

            <tbody>
            <?php foreach($compra as $producto):?>

                    <tr>

                            <input type="hidden" name="codCompra" id="codCompra" value="<?php echo $estadoCompra[0]["idCompra"];?>">
                            <input type="hidden" name="completa" id="completa" value="true">
                            <input type="hidden" name="idProducto[]" id="idProducto" value="<?php echo $producto["idProducto"]; ?>">
                            <input type="hidden" name="cantidadE[]" id="cantidadE"  value="<?php echo $producto["Cantidad"]; ?>">
                            <td><?php echo $producto["Producto"]; ?></td>
                            <td id="cantE"><?php echo $producto["Cantidad"];?></td>
                            <td><input id="cantidadR" class="input add-new-data" type="number" name="cantidadR[]" value="<?php echo $producto["Cantidad"];?>"></td>
                            <td>
                                <?php if($producto["Perecedero"]=="Si"):?>
                                    <input id="fechaV" class="input add-new-data" type="date" name="fechaV[]">
                                 <?php else:?>
                                    <label>No perecedero</label>
                                    <input type="hidden" name="fechaV[]" id="fechaV"  value="">
                                <?php endif ?>
                            </td>
                            <td><input id="destino" class="input add-new-data" type="text" name="destino[]" value="GNC Virgen del Valle"></td>


                    </tr>


            <?php endforeach; ?>
            </tbody>

        </table>
        <input id="altaStock" name="action" class="btn btn-success" value="Alta en Stock">
    </form>



</div>

<script type="text/javascript">

    $(document).ready(function() {


        $('input[id=cantidadR]').change(function(){

            var cantEsperada=parseInt($(this).parent().prev().text());
            var cantRecibida=parseInt($(this).val());

            if(cantRecibida=="" || cantRecibida<0 || cantRecibida>cantEsperada )//Chequea que cantidad no este vacio
               {

                   alert("Debe introducir una cantidad valida");
                   $(this).val(cantEsperada);
               }

        });

        $('input[id=fechaV]').change(function(){

            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!

            var yyyy = today.getFullYear();
            if(dd<10){
                dd='0'+dd;
            }
            if(mm<10){
                mm='0'+mm;
            }
            today = yyyy+'-'+mm+'-'+dd;

            var venc=$(this).val();

            if( venc<today )//Chequea que la fecha de vencimiento no sea menor al dia de la fecha
            {

                alert("Este producto se encuentra vencido");
                $(this).val("");
            }

        });

        $('input[id=altaStock]').click(function(){

            var cont=0;
            //var prodCantidad =[];

            //Iterar la tabla, verificando si la orden se cumple parcial o completamente
            $("#tablaDetalles tbody tr").each(function(i) {
                var cantEsperada=parseInt($(this).find("#cantE").text());
                var cantRecibida = parseInt($(this).find("#cantidadR").val());
                if(cantRecibida<cantEsperada) cont++;
            });
            if(cont > 0)
            {
                var conf=confirm("Algunas cantidades recibidas no coinciden con las ordenadas. Se daran de alta estas cantidades, pero la orden seguira abierta hasta que se complete. Desea continuar?");
                if(conf==true)
                {
                    $("input#completa").val("false");
                    $("form#formProd").submit();
                }
            }

            else $("form#formProd").submit();

        });


    });


</script>

</body>

</html>
