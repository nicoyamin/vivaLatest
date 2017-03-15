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


$compra = new Viva\Compra($viva);
$db = new Viva\BaseDatos($viva);

$arrayCompras=$compra->selectCompras($viva);

$arrayEstados=$compra->selectEstados($viva);

if (isset($_POST['action']) and $_POST['action'] == 'cambiarEstado')
{
    //$detallesCompra=$viva->select("Orden_de_compra",["numero","Estado"],["idCompra"=>107]);
    //if($_POST["estadoOC"]==)
    //dump($_POST);
}

?>



<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <title>Gestion de Compras</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
</head>
<body>

<?php include 'scripts/logout.inc.php'; ?>
<div class="container">
    <h2>Compras</h2>
    <button><a href="nuevaCompraBackup.php?titulo=Nueva Compra">Nueva Compra</a></button>
    <table id="tablaCompras" class="table table-striped">
        <thead>
        <tr>
            <th>ID Compra</th>
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>Condiciones de Pago</th>
            <th>Lugar de Entrega</th>
            <th>Fecha De Entrega</th>
            <th>Enviar Por</th>
            <th>Estado</th>
            <th>Cambiar Estado</th>
            <th>Detalles Compra</th>


        </tr>
        </thead>

        <tbody>
        <?php foreach($arrayCompras as $arrayCompra):?>
            <tr>
                <form action="cambiarEstadoCompra.php" method="post">
                    <input type="hidden" name="id"  id="idCompra" value="<?php echo $arrayCompra["idCompra"]; ?>">
                    <input type="hidden" name="idP" id="idProv" value="<?php echo $arrayCompra["idProveedor"]; ?>">
                    <td><?php echo $arrayCompra["idCompra"]; ?></td>
                    <td><?php echo $arrayCompra["Fecha"];?></td>
                    <td><?php echo $arrayCompra["Proveedor_nombre"];?></td>
                    <td><?php echo $arrayCompra["Condiciones_pago"];?></td>
                    <td><?php echo $arrayCompra["Lugar_entrega"];?></td>
                    <td><?php echo $arrayCompra["Fecha_entrega"];?></td>
                    <td><?php echo $arrayCompra["Enviar_por"];?></td>
                    <td><?php echo $arrayCompra["Descripcion"];?></td>
                    <td> <input type="submit" method="POST" class="btn btn-success" value="Cambiar Estado"></td>
                    <td> <input type="button" class="btn btn-success" id="btnMasDetalles"  data-toggle="modal" data-target="#modalDetallesCompra" value="Mas Detalles" action=""></td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>

    <div id="resultado">

    </div>

    <div class="modal fade" id="modalDetallesCompra" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                    <div>
                        <input type="hidden" id="codigoCompra" name="codCompra">
                        <label id="cc"></label>
                        <h3>Orden de Compra Nro:<label id="nroOC"></label></h3>
                        <h3>Estado:<label id="estadoOC" name="estadoOc"></label></h3>
                    </div>
                <div>
                    <input type="submit" class="btn btn-success" id="btnGenerarOrden" value="Generar Orden de Compra" action="">
                </div>
                <table id="productosCompra" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Total</th>
                    </tr>
                    </thead>

                    <tbody>
                        <tr>

                            <th>Cantidad</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Total</th>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>

    </div>




</div>



<script src="js/vendor/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>

<script type="text/javascript">
$(document).ready( function () {


    var table=$('#tablaCompras').dataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });

    var table2=$('#productosCompra').dataTable({
        "info":false,
        "searching":false,
        "paging":false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });



$(function(){
    var compra;
    $('.btn-success').click(function(){
    	
    	compra=$(this).closest("tr").find('[id*="idCompra"]').first().val();
        $.ajax({
            type: 'POST',
            url:'/scripts/recuperarDetallesCompra.php',
            data:{compra:compra, opcion:1},
            dataType:'json',
            success: function(response){
                table2.fnClearTable();
                for(var i = 0; i < response.length; i++) {

                    table2.fnAddData([response[i].Cantidad, response[i].Producto, response[i].Precio, response[i].Total]);
                }
            }


        });

        $.ajax({
            type: 'POST',
            url:'/scripts/recuperarDetallesCompra.php',
            data:{compra:compra, opcion:2},
            dataType:'json',
            success: function(response){
                $("#codigoCompra").empty();
                $("#codigoCompra").append(compra);
                $("#nroOC").empty();
                $("#nroOC").append(response[0]["Numero"]);
                $("#estadoOC").empty();
                $("#estadoOC").append(response[0]["Estado"]);

                if(response[0]["Numero"]=="No generada")
                {
                    $("#btnGenerarOrden").show();
                }
                else
                {
                    $("#btnGenerarOrden").hide();
                }
            }


        });

    });

    $('#btnCambiarEstado').click(function() {

        var compra=$('#codigoCompra').text();
       /* var estadoOC = $('#estadoOC').text();
        var nroOC = $('#nroOC').text();
        var nuevoEstado = $('#estadoDrop option:selected').val();

        if (estadoOC == "Activa" && (nuevoEstado == 1 || nuevoEstado == 2)) {
            alert("La orden ya fue generada. No puede cambiar las opciones de cotizacion");
        }
        else if(nuevoEstado == 0) alert("Por favor, seleccione un estado valido");

        else
        {
            $.ajax({
                type: 'POST',
                url: '/scripts/recuperarDetallesCompra.php',
                data: {compra: compra, estadoOC: estadoOC, nroOC: nroOC, nuevoEstado: nuevoEstado, opcion: 3},
                dataType: 'json'


            });
            $('#modalDetallesCompra').modal('hide');

        }*/
    });

    $('#btnGenerarOrden').click(function()
    {
        var compra=$('#codigoCompra').text();


        $.ajax({
            type: 'POST',
            url:'/scripts/recuperarDetallesCompra.php',
            data:{compra:compra, opcion:4},
            dataType:'json',
            success: function(response){
                alert("Orden de Compra generada con exito");
            }


        });
        $('#modalDetallesCompra').modal('hide');

    })
});

});
</script>

</body>

</html>