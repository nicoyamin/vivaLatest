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
if (!userHasRole('2') && !userHasRole('3'))
{
    $error = 'Solo administradores pueden acceder a esta pagina';
    include 'accesoDenegado.php';
    exit();
}


//$usuario = new Viva\Usuario($viva);

$usuarios=$viva->select('Usuario',["[>]Persona" => ["idPersona"=>"idPersona"]], [
    "Persona.Apellido",
    "Persona.Nombre",
    "Usuario.idUsuario"
]
);
$conceptos = $viva->select("Caja_operacion", "*", ["Tipo[!]" => "Cierre"]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["btnBuscar"]) )
{


    $strWhere="";
    $condiciones=array();


    //Por cada condicion de filtrado, agregar la sentencia SQL correspondiente al array condiciones
    if($_POST["empleado"]!=0) array_push($condiciones, "C.idUsuario=".$_POST["empleado"]);
    if($_POST["tipo"]=="Entrada") array_push($condiciones, "C.Tipo='Entrada'");
    if($_POST["tipo"]=="Salida") array_push($condiciones, "C.Tipo='Salida'");
    if($_POST["concepto"]!=0) array_push($condiciones, "C.Concepto=".$_POST["concepto"]);
    if($_POST["concepto"]!=0) array_push($condiciones, "C.Concepto=".$_POST["concepto"]);
    if($_POST["fechaMin"]!="") array_push($condiciones, "date(C.Fecha)>=".$_POST["fechaMin"]);
    if($_POST["fechaMax"]!="") array_push($condiciones, "date(C.Fecha)<=".$_POST["fechaMax"]);


    //Si existen condiciones, armar una clausula WHERE, seprando cada elemento del array con AND
    if(!empty($condiciones))$strWhere = "WHERE ".implode(' AND ', $condiciones).";";

    //sentencia SQL para traer, por defecto, TODOS los movimientos de caja
    $sql="SELECT C.idCaja as Movimiento, P.Nombre, P.Apellido, C.Fecha, C.Tipo, CO.Descripcion as Concepto, C.Debe, C.Haber, C.Observaciones, CC.Descripcion as Comprobante, C.idReferencia as Numero FROM Caja as C LEFT JOIN Caja_operacion as CO ON C.Concepto=CO.idCaja_operacion LEFT JOIN Caja_comprobante as CC ON C.Referencia=CC.idComprobante LEFT JOIN Usuario as U ON C.idUsuario=U.idUsuario LEFT JOIN Persona as P ON U.idPersona=P.idPersona ".$strWhere;



    $resultados=$viva->query($sql)->fetchAll();

    //dump($resultados);


}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Historial Caja</title>
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
    <h2>Historial de Registros de Caja</h2>

    <h3>Seleccione los filtros</h3>

    <form action="resultadosHistorialCaja.php" method="post">

        <div>
            <label>Fecha Desde: <input type="date" id="min" name="fechaMin"></label>

            <label>Fecha Hasta:<input type="date" id="max" name="fechaMax"> </label>
        </div>


        <div>
            <label>Buscar por tipo de Movimiento: </label>
            <select class="selectUnico" name="tipo" id="tipo" data-placeholder="Seleccione">
                <option value="0">Todos</option>
                <option value="Entrada">Entrada</option>
                <option value="Salida">Salida</option>
            </select>
        </div>

        <div><label>Buscar por concepto:</label>
            <select class="selectUnico" name="concepto" id="concepto" data-placeholder="Seleccione">
                <option value="0">Todos</option>
                <?php foreach($conceptos as $con):?>
                    <option value="<?php echo $con["idCaja_operacion"];?>"><?php echo $con["Descripcion"];?></option>
                <?php endforeach; ?>
            </select></div>


        <div>
            <label>Realizados por: </label>
            <select class="selectUnico" name="empleado" id="empleado" data-placeholder="Seleccione">
                <option value="0">Todos</option>
                <?php foreach($usuarios as $empleado):?>
                    <option value="<?php echo $empleado["idUsuario"];?>"><?php echo $empleado["Nombre"]." ".$empleado["Apellido"];?></option>
                <?php endforeach; ?>
            </select>
        </div>



        <input type="submit" name="btnBuscar" value="Buscar" action="">




    </form>


</div>

<script type="text/javascript">
    $(document).ready(function(){

        $('#tipo').select2({
            dropdownAutoWidth: 'true'
        });

        $('#concepto').select2();
        $('#empleado').select2();
    });

</script>

</body>

</html>

