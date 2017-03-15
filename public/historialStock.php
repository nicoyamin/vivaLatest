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


    //Por cada condicion de filtrado, agregar la sentencia SQL correspondiente al array condiciones
    if($_POST["prodCod"]!="") array_push($condiciones, "P.Codigo_barras_producto=".$_POST["prodCod"]);
    if($_POST["prodNombre"]!=0) array_push($condiciones, "P.idProducto=".$_POST["prodNombre"]);
    if($_POST["fechaMin"]!="") array_push($condiciones, "date(SM.Fecha)>=".$_POST["fechaMin"]);
    if($_POST["fechaMax"]!="") array_push($condiciones, "date(SM.Fecha)<=".$_POST["fechaMax"]);
    if($_POST["movimiento"]!=0) array_push($condiciones, "SM.idStock_tipo_movimientos=".$_POST["movimiento"]);
    if($_POST["concepto"]!=0) array_push($condiciones, "SM.idStock_concepto=".$_POST["concepto"]);

    //Si existen condiciones, armar una clausula WHERE, seprando cada elemento del array con AND
    if(!empty($condiciones))$strWhere = "WHERE ".implode(' AND ', $condiciones).";";

    //sentencia SQL para traer, por defecto, TODOS los movimientos de stock
    $sql="SELECT P.Nombre_producto,P.Cantidad_unitaria_producto,P.Unidad_producto,P.Existencia_producto,SM.Cantidad,SM.Fecha,TM.Descripcion as Movimiento,SC.Descripcion as Concepto,SM.Observaciones FROM Stock_movimientos as SM LEFT JOIN Producto as P ON SM.idProducto=P.idProducto LEFT JOIN Stock_tipo_movimientos as TM on SM.idStock_tipo_movimientos=TM.idStock_tipo_movimientos LEFT JOIN Stock_concepto as SC on SM.idStock_concepto=SC.idStock_concepto ".$strWhere;



    $resultados=$viva->query($sql)->fetchAll();

    dump($resultados);


}

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

</head>

<body>

<?php include 'scripts/logout.inc.php'; ?>

<div class="container">
    <h2>Historial de Movimientos de Stock</h2>

    <h3>Seleccione los filtros</h3>

    <form action="resultadosHistorialStock.php" method="post">

        <div><label>Buscar por codigo de barras: <input type="text" name="prodCod" value=""></label></div>

        <div><label>Buscar por nombre de producto:</label>
            <select class="selectUnico" name="prodNombre" id="prodNombre" data-placeholder="Seleccione">
                <option value="0">Seleccione producto</option>
                <?php foreach($listaProds as $listaProd):?>
                    <option value="<?php echo $listaProd["idProducto"];?>"><?php echo $listaProd["Nombre_producto"]." ".$listaProd["Cantidad_unitaria_producto"]." ".$listaProd["Unidad_producto"];?></option>
                <?php endforeach; ?>
            </select></div>

        <div>
            <label>Fecha Desde: <input type="date" id="min" name="fechaMin"></label>

            <label>Fecha Hasta: </label><input type="date" id="min" name="fechaMax">
        </div>

        <div>
            <label>Con movimiento: </label>
            <select class="selectUnico" name="movimiento" id="movimiento" data-placeholder="Seleccione">
                <option value="0">Todos</option>
                <?php foreach($movimientos as $movimiento):?>
                    <option value="<?php echo $movimiento["idStock_tipo_movimientos"];?>"><?php echo $movimiento["Descripcion"];?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Concepto: </label>
            <select class="selectUnico" name="concepto" id="concepto" data-placeholder="Seleccione">
                <option value="0">Todos</option>
                <?php foreach($conceptos as $concepto):?>
                    <option value="<?php echo $concepto["idStock_concepto"];?>"><?php echo $concepto["Descripcion"];?></option>
                <?php endforeach; ?>
            </select>
        </div>


        <input type="submit" name="btnBuscar" value="Buscar" action="">




    </form>


</div>

<script type="text/javascript">
    $(document).ready(function(){

        $('#prodNombre').select2({
            dropdownAutoWidth: 'true'
        });

        $('#concepto').select2();
        $('#movimiento').select2();
    });

</script>

</body>

</html>
