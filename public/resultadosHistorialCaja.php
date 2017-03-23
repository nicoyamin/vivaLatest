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

$usuarios=$viva->select('Usuario',["[>]Persona" => ["idPersona"=>"idPersona"]], [
        "Persona.Apellido",
        "Persona.Nombre",
        "Usuario.idUsuario"
    ]
);
$conceptos = $viva->select("Caja_operacion", "*", ["Tipo[!]" => "Cierre"]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["btnBuscar"]) )
{
    //dump()

    $strWhere="";
    $condiciones=array();

    if($_POST["fechaMin"]!="") $fechaMin="'".$_POST["fechaMin"]."'";
    if($_POST["fechaMax"]!="") $fechaMax="'".$_POST["fechaMax"]."'";

    //Por cada condicion de filtrado, agregar la sentencia SQL correspondiente al array condiciones
    if($_POST["empleado"]!=0) array_push($condiciones, "C.idUsuario=".$_POST["empleado"]);
    if($_POST["tipo"]=="Entrada") array_push($condiciones, "C.Tipo='Entrada'");
    if($_POST["tipo"]=="Salida") array_push($condiciones, "C.Tipo='Salida'");
    if($_POST["concepto"]!=0) array_push($condiciones, "C.Concepto=".$_POST["concepto"]);
    if($_POST["concepto"]!=0) array_push($condiciones, "C.Concepto=".$_POST["concepto"]);
    if($_POST["fechaMin"]!="") array_push($condiciones, "date(C.Fecha)>=".$fechaMin);
    if($_POST["fechaMax"]!="") array_push($condiciones, "date(C.Fecha)<=".$fechaMax);


    //Si existen condiciones, armar una clausula WHERE, seprando cada elemento del array con AND
    if(!empty($condiciones))$strWhere = "WHERE ".implode(' AND ', $condiciones).";";

    //sentencia SQL para traer, por defecto, TODOS los movimientos de caja
    $sql="SELECT C.idCaja as Movimiento, P.Nombre, P.Apellido, C.Fecha, C.Tipo, CO.Descripcion as Concepto, C.Debe, C.Haber, C.Observaciones, CC.Descripcion as Comprobante, C.idReferencia as Numero FROM Caja as C LEFT JOIN Caja_operacion as CO ON C.Concepto=CO.idCaja_operacion LEFT JOIN Caja_comprobante as CC ON C.Referencia=CC.idComprobante LEFT JOIN Usuario as U ON C.idUsuario=U.idUsuario LEFT JOIN Persona as P ON U.idPersona=P.idPersona ".$strWhere;



    $resultados=$viva->query($sql)->fetchAll();

    //dump($fechaMin);



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
    <h2>Movimientos de Caja</h2>

    <?php if(empty($resultados)): ?>
        <h3>No se encontraron resultados con los filtros elegidos</h3>
    <?php else: ?>

        <table  id="tablaResultados" class="table table-striped">
            <thead>
            <tr>
                <th>Movimiento Nro.</th>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Tipo Movimiento</th>
                <th>Concepto</th>
                <th>Debe</th>
                <th>Haber</th>
                <th>Observaciones</th>
                <th>Comprobante</th>
                <th>Nro comprobante</th>

            </tr>
            </thead>

            <tbody>
            <?php foreach($resultados as $caja):?>
                <tr>
                    <td><?php echo $caja["Movimiento"]; ?></td>
                    <td><?php echo $caja["Nombre"]." ".$caja["Apellido"];?></td>
                    <td><?php echo $caja["Fecha"];?></td>
                    <td><?php echo $caja["Tipo"];?></td>
                    <td><?php echo $caja["Concepto"];?></td>
                    <td><?php echo "$".$caja["Debe"]; ?></td>
                    <td><?php echo "$".$caja["Haber"]; ?></td>
                    <td><?php echo $caja["Observaciones"];?></td>
                    <td><?php echo $caja["Comprobante"];?></td>
                    <td><?php echo $caja["Numero"];?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

    <a href="historialCaja.php"><button>Atras</button></a>




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

