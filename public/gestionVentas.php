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


//$compra = new Viva\Compra($viva);
//$db = new Viva\BaseDatos($viva);

$arrayVentas=$viva->select("Venta(V)",["[>]Cliente(C)" => ["V.idCliente" => "idCliente"]], ["V.idVenta","V.Fecha", "V.Importe","V.saldoImporte","V.Forma_pago","V.Referencia_pago","V.Estado","V.Iva_cliente","C.Nombre"]);

//dump($arrayVentas);

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
    <title>Gestion Ventas</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
</head>
<body>

<?php include 'scripts/logout.inc.php'; ?>
<div class="container">
    <h2>Venats</h2>
    <button><a href="nuevaVenta.php?titulo=Nueva Venta">Nueva Venta</a></button>
    <table id="tablaVentas" class="table table-striped">
        <thead>
        <tr>
            <th>ID Venta</th>
            <th>Fecha</th>
            <th>Forma de Pago</th>
            <th>Codigo de Referencia Pago</th>
            <th>Importe</th>
            <th>Saldo</th>
            <th>Estado</th>
            <th>IVA Cliente</th>
            <th>Cliente</th>
            <th>Detalles Venta</th>


        </tr>
        </thead>

        <tbody>
        <?php foreach($arrayVentas as $venta):?>
            <tr>
                <form action="cambiarEstadoCompra.php" method="post">
                    <input type="hidden" name="idVenta"  id="venta" value="<?php echo $venta["idVenta"]; ?>">
                    <td><?php echo $venta["idVenta"]; ?></td>
                    <td><?php echo $venta["Fecha"];?></td>
                    <td><?php echo $venta["Forma_pago"];?></td>
                    <td><?php echo $venta["Referencia_pago"];?></td>
                    <td><?php echo $venta["Importe"];?></td>
                    <td><?php echo $venta["saldoImporte"];?></td>
                    <td><?php echo $venta["Estado"];?></td>
                    <td><?php echo $venta["Iva_cliente"];?></td>
                    <td><?php echo $venta["Nombre"];?></td>
                    <td> <input type="button" class="btn btn-success" id="btnMasDetalles"  data-toggle="modal" data-target="#modalDetallesVenta" value="Mas Detalles" action=""></td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>

    <div id="resultado">

    </div>

    <div class="modal fade" id="modalDetallesVenta" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <table id="productosVenta" class="table table-striped">
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


        var table=$('#tablaVentas').dataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });

        var table2=$('#productosVenta').dataTable({
            "info":false,
            "searching":false,
            "paging":false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });



        $(function(){
            var venta;
            $('.btn-success').click(function(){

                venta=$(this).closest("tr").find('[id*="venta"]').first().val();
                $.ajax({
                    type: 'POST',
                    url:'/scripts/recuperarDetallesVenta.php',
                    data:{venta:venta},
                    dataType:'json',
                    success: function(response){
                        table2.fnClearTable();
                        for(var i = 0; i < response.length; i++) {

                            table2.fnAddData([response[i].Cantidad, response[i].Producto, response[i].Precio, response[i].Total]);
                        }
                    }


                });


            });

        });

    });
</script>

</body>

</html>