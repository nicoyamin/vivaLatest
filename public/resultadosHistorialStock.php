<?php
use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';
require_once  'acceso.inc.php';
include 'scripts/db.inc.php';

if (!userIsLoggedIn())
{
    include 'login.php';
    exit();
}
if (!userHasRole('1') && !userHasRole('2') && !userHasRole('3'))
{
    $error = 'Solo administradores pueden acceder a esta pagina';
    include 'accesoDenegado.php';
    exit();
}

$listaProds=$viva->select("Producto",["idProducto","Nombre_producto", "Cantidad_unitaria_producto","Unidad_producto"]);
$conceptos=$viva->select("Stock_concepto",["idStock_concepto","Descripcion"]);
$movimientos=$viva->select("Stock_tipo_movimientos",["idStock_tipo_movimientos","Descripcion"]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["btnBuscar"]) )
{


    $strWhere="";
    $condiciones=array();


    if($_POST["fechaMin"]!="") $fechaMin="'".$_POST["fechaMin"]."'";
    if($_POST["fechaMax"]!="") $fechaMax="'".$_POST["fechaMax"]."'";

    //Por cada condicion de filtrado, agregar la sentencia SQL correspondiente al array condiciones
    if($_POST["prodCod"]!="") array_push($condiciones, "P.Codigo_barras_producto=".$_POST["prodCod"]);
    if($_POST["prodNombre"]!=0) array_push($condiciones, "P.idProducto=".$_POST["prodNombre"]);
    if($_POST["fechaMin"]!="") array_push($condiciones, "date(SM.Fecha)>=".$fechaMin);
    if($_POST["fechaMax"]!="") array_push($condiciones, "date(SM.Fecha)<=".$fechaMax);
    if($_POST["movimiento"]!=0) array_push($condiciones, "SM.idStock_tipo_movimientos=".$_POST["movimiento"]);
    if($_POST["concepto"]!=0) array_push($condiciones, "SM.idStock_concepto=".$_POST["concepto"]);

    //Si existen condiciones, armar una clausula WHERE, seprando cada elemento del array con AND
    if(!empty($condiciones))$strWhere = "WHERE ".implode(' AND ', $condiciones).";";

    //sentencia SQL para traer, por defecto, TODOS los movimientos de stock
    $sql="SELECT P.Nombre_producto,P.Cantidad_unitaria_producto,P.Unidad_producto,SM.Cantidad,SM.Fecha,TM.Descripcion as Movimiento,SC.Descripcion as Concepto,SM.Observaciones, SM.Stock_luego, SM.Stock_antes FROM Stock_movimientos as SM LEFT JOIN Producto as P ON SM.idProducto=P.idProducto LEFT JOIN Stock_tipo_movimientos as TM on SM.idStock_tipo_movimientos=TM.idStock_tipo_movimientos LEFT JOIN Stock_concepto as SC on SM.idStock_concepto=SC.idStock_concepto ".$strWhere;



    $resultados=$viva->query($sql)->fetchAll();

    //dump($resultados);


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movimientos de Stock</title>
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

<div class="container">
    <h2>Movimientos de Stock</h2>

    <?php if(empty($resultados)): ?>
        <h3>no se encontraron resultados con los filtros elegidos</h3>
    <?php else: ?>

        <table  id="tablaResultados" class="table table-striped">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad Afectada</th>
                <th>Fecha</th>
                <th>Tipo Movimiento</th>
                <th>Concepto</th>
                <th>Cantidad Antes</th>
                <th>Cantidad Despues</th>
                <th>Observaciones</th>

            </tr>
            </thead>

            <tbody>
            <?php foreach($resultados as $producto):?>
                <tr>
                        <td><?php echo $producto["Nombre_producto"]." ".$producto["Cantidad_unitaria_producto"]." ".$producto["Unidad_producto"]; ?></td>
                        <td><?php echo $producto["Cantidad"];?></td>
                        <td><?php echo $producto["Fecha"];?></td>
                        <td><?php echo $producto["Movimiento"];?></td>
                        <td><?php echo $producto["Concepto"];?></td>
                        <td><?php echo $producto["Stock_antes"]; ?></td>
                        <td><?php echo $producto["Stock_luego"]; ?></td>
                        <td><?php echo $producto["Observaciones"];?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

    <a href="historialStock.php"><button>Atras</button></a>




</div>

<script type="text/javascript">
    $(document).ready(function(){

        var table=$('#tablaResultados').dataTable({
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

