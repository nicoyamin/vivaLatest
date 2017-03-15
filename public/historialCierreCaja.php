<?php
require_once '../vendor/autoload.php';

include 'scripts/db.inc.php';

include("acceso.inc.php");

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


$db = new Viva\BaseDatos($viva);
$cierres=$viva->select('Caja_Arqueo(CA)',["[>]Asistencia(A)" => ["CA.idTurno"=>"idAsistencia"],
    "[>]Turno(T)" => ["A.idTurno"=>"idTurno"],
    "[>]Usuario(U)" => ["A.idUsuario"=>"idUsuario"],
    "[>]Persona(P)" => ["U.idPersona"=>"idPersona"]
], [
    "P.Apellido",
    "P.Nombre",
    "T.Descripcion_turno",
    "CA.Fecha",
    "CA.Arqueo_sistema",
    "CA.Arqueo_empleado_efectivo",
    "CA.Arqueo_empleado_valores",
    "CA.Diferencia",
    "CA.idTurno",
    "CA.idCaja_arqueo"
]);


//dump($detallesCierre);

if(isset($_POST['action']) and $_POST['action']=="Detalles del cierre")
{
    dump($_POST);
}



?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
</head>
<body>

<?php include 'scripts/logout.inc.php'; ?>
<div class="container">
    <h2>Cierres de caja</h2>
    <table id="tablaCierres" class="table table-striped">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Turno</th>
            <th>Efectivo</th>
            <th>Valores</th>
            <th>Total</th>
            <th>Recaudado segun sistema</th>
            <th>Diferencia</th>
            <th>Detalles</th>

        </tr>
        </thead>

        <tbody>
        <?php foreach($cierres as $cierre):?>
            <tr>
                <form action="" method="post">
                    <input type="hidden" name="idCierre" value="<?php echo $cierre["idCaja_arqueo"]; ?>">
                    <input type="hidden" name="idTurno" id="idTurno" value="<?php echo $cierre["idTurno"]; ?>">
                    <td><?php echo $cierre["Fecha"];?></td>
                    <td><?php echo $cierre["Nombre"]." ".$cierre["Apellido"];?></td>
                    <td><?php echo $cierre["Descripcion_turno"];?></td>
                    <td><?php echo $cierre["Arqueo_empleado_efectivo"];?></td>
                    <td><?php echo $cierre["Arqueo_empleado_valores"];?></td>
                    <td><?php echo $cierre["Arqueo_empleado_efectivo"]+$cierre["Arqueo_empleado_valores"];?></td>
                    <td><?php echo $cierre["Arqueo_sistema"];?></td>
                    <td><?php echo $cierre["Diferencia"];?></td>
                    <td> <input type="button" class="btn btn-success" id="btnDetallesCierre"  data-toggle="modal" data-target="#modalDetallesCierre" value="Mas Detalles" action=""></td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>
</div>

<div class="modal fade" id="modalDetallesCierre" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div>
                <h3>Entradas</h3>
            </div>
            <table id="detallesEntradas" class="table table-striped">
                <thead>
                <tr>
                    <th>Hora</th>
                    <th>Concepto</th>
                    <th>Importe</th>
                    <th>Referencia</th>
                    <th>Nro Referencia</th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <th>Hora</th>
                    <th>Concepto</th>
                    <th>Importe</th>
                    <th>Referencia</th>
                    <th>Nro Referencia</th>
                </tr>
                </tbody>

            </table>

            <div>
                <h3>Salidas</h3>
            </div>

            <table id="detallesSalidas" class="table table-striped">
                <thead>
                <tr>
                    <th>Hora</th>
                    <th>Concepto</th>
                    <th>Importe</th>
                    <th>Referencia</th>
                    <th>Nro Referencia</th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <th>Hora</th>
                    <th>Concepto</th>
                    <th>Importe</th>
                    <th>Referencia</th>
                    <th>Nro Referencia</th>
                </tr>
                </tbody>

            </table>
        </div>
    </div>

</div>



<script src="js/vendor/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>

<script type="text/javascript">
    $(document).ready( function () {
        var table=$('#tablaCierres').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });

        var tableSalidas=$('#detallesSalidas').dataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });

        var tableEntradas=$('#detallesEntradas').dataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });


        $(".btn-success").click(function(){

           var turno=$(this).closest("tr").find('[id*="idTurno"]').first().val();

            $.ajax({
                type: 'POST',
                url:'/scripts/recuperarDetallesCierre.php',
                data:{turno:turno},
                dataType:'json',
                success: function(response){
                    //alert(response);
                    tableEntradas.fnClearTable();
                    tableSalidas.fnClearTable();
                    for(var i = 0; i < response.length; i++) {

                        if(response[i].Tipo=="Entrada") tableEntradas.fnAddData([response[i].Hora, response[i].Concepto, response[i].Importe, response[i].Comprobante, response[i].Referencia]);
                        else tableSalidas.fnAddData([response[i].Hora, response[i].Concepto, response[i].Importe, response[i].Comprobante, response[i].Referencia]);
                        //alert(response.length);
                    }
                }

            });

        });
    });
</script>

</body>

</html>

