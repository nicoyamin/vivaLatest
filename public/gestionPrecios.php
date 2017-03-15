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


$producto = new Viva\Producto($viva);
$db = new Viva\BaseDatos($viva);

$arrayPrecios=$viva->select("Producto", [
    "idProducto",
    "Nombre_producto",
    "Cantidad_unitaria_producto",
    "Unidad_producto",
    "Codigo_barras_producto",
    "Precio_venta_producto"
],["ORDER"=>"Nombre_producto"]);


if (isset($_POST['action']) and $_POST['action'] == 'Aceptar')
{

    dump($_POST);
}


?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <title>Gestion Precios</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
</head>
<body>

<?php include 'scripts/logout.inc.php'; ?>
<div class="container">
    <h2>Precios de Productos</h2>
    <table id="tablaProductos" class="table table-striped">
        <thead>
        <tr>
            <th>Codigo Producto</th>
            <th>Producto</th>
            <th>Codigo de Barras</th>
            <th>Precio Actual</th>
            <th>Precio Nuevo</th>

        </tr>
        </thead>

        <tbody>
        <?php foreach($arrayPrecios as $precio):?>
            <tr>
                <form action="" method="post">
                    <td><?php echo $precio["idProducto"]; ?></td>
                    <td><?php echo $precio["Nombre_producto"]." ".$precio["Cantidad_unitaria_producto"]." ".$precio["Unidad_producto"];?></td>
                    <td><?php echo $precio["Codigo_barras_producto"];?></td>
                    <td><?php echo "$ ".$precio["Precio_venta_producto"];?></td>
                    <td><input type="number" step="0.01" min="0" name="precioNuevo"></td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>
    <button id="btnActualizar">Actualizar Precios</button>

</div>



<script src="js/vendor/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>

<script type="text/javascript">
    $(document).ready( function () {
        var table=$('#tablaProductos').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            "columnDefs": [
                {
                    "targets": [ 2 ],
                    "visible": false
                },
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ]
        });

        $("#btnActualizar").click(function(){

            var precios=[];
            var ids=[];

            table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                var cellP = table.cell({ row: rowIdx, column: 4 }).node();

                if($('input', cellP).val()!="")
                {
                    //alert(table.column(0).row(rowIdx).value);
                    var precio=$('input', cellP).val();
                    var id=table.cell({ row: rowIdx, column: 0 }).data();
                    //alert(precio+"=>"+id);

                    precios.push(precio);
                    ids.push(id);

                }


            });


            if(!jQuery.isEmptyObject( precios ))
            {
                $.ajax({
                    type: 'POST',
                    url:'/scripts/actualizarPrecios.php',
                    data:{precios:precios, ids:ids},
                    dataType:'json',
                    success: function(response){
                        alert("Se modificaron "+response+" precios");
                        location.reload();
                    }

                });
            }

        });



    } );
</script>

</body>

</html>