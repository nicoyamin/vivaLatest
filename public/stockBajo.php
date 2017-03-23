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
$producto = new Viva\Producto($viva);

$stockBajo=$viva->query("SELECT P.idProducto,
P.Nombre_producto,
P.Existencia_producto+P.Stock_entrante_producto as Stock_actual,
P.Stock_minimo_producto as Stock_minimo,
PR.idProveedor,
PR.Proveedor_nombre
FROM Producto as P LEFT JOIN Proveedor as PR
ON P.idProveedor=PR.idProveedor
WHERE P.Existencia_producto+P.Stock_entrante_producto < Stock_minimo_producto*1.20
ORDER BY Proveedor_nombre;")->fetchAll();

dump($stockBajo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nueva Compra</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
    <script src="js/vendor/jquery-1.12.0.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>


</head>

<body>
<?php include 'scripts/logout.inc.php'; ?>

<h1>Stock Bajo por Proveedores</h1>

<table id="tblStock" class="table table-striped">
    <thead>
    <tr>
        <th>Producto</th>
        <th>Stock Actual</th>
        <th>Stock Minimo</th>
        <th>Cantidad recomendada</th>
        <th>Proveedor</th>
        <th>Accion</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($stockBajo as $stock):?>
        <tr>
            <form action="comprasAuto.php" method="post">
                <input type="hidden" name="idProveedor" value="<?php echo $stock["idProveedor"];?>">
                <td><?php echo $stock["Nombre_producto"];?></td>
                <td><?php echo $stock["Stock_actual"];?></td>
                <td><?php echo $stock["Stock_minimo"];?></td>
                <td><?php echo $stock["Stock_minimo"]*1.20;?></td>
                <td><?php echo $stock["Proveedor_nombre"];?></td>
                <td> <input type="submit" class="btn btn-success" value="Aceptar"></td>
            </form>
        </tr>
    <?php endforeach?>
    </tbody>
</table>

<script>
    $(document).ready(function(){

        var table=$('#tblStock').DataTable({

            "info":false,
            "paging":false,
            "order":[[4,"asc"]],
            dom: 'Bfrtip',
            buttons: [
                'pdf'
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });
    });

</script>

</body>

</html>

