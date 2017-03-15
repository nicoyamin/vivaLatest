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


$arrayAsistencias=array();

    $asistencia = new Viva\Asistencia($viva);

    $arrayAsistencias=$asistencia->selectAllAsistencias($viva);

    //dump($arrayAsistencias);

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{




    

    //$timediff=strtotime($arrayAsistencia["Check_out"])-strtotime($arrayAsistencia["Check_in"]);
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
    <h2>Registro de Asistencias</h2>

    <table border="0" cellspacing="5" cellpadding="5">
        <tbody><tr>
            <td>Desde:</td>
            <td><input type="date" id="min" name="min"></td>
        </tr>
        <tr>
            <td>Hasta:</td>
            <td><input type="date" id="max" name="max"></td>
        </tr>
    </tbody>
    </table>

    <table id="tablaAsistencias" class="table table-striped">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Hora Entrada</th>
            <th>Hora Salida</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Codigo</th>
            <th>Confirmado</th>
            <th>Turno</th>
            <th>Usuario</th>
            <th>Horas Trabajadas <br> (H:M:S)</th>


        </tr>
        </thead>

        <tbody>

        <?php foreach($arrayAsistencias as $arrayAsistencia):?>
            <tr>
                <form action="" method="post">
                    <input type="hidden" name="id" value="<?php echo $arrayAsistencia["idAsistencia"]; ?>">
                    <td><?php echo date("d-m-Y",strtotime($arrayAsistencia["Fecha"]));?></td>
                    <td><?php echo $arrayAsistencia["Hora_entrada"];?></td>
                    <td><?php echo $arrayAsistencia["Hora_salida"];?></td>
                    <td><?php echo $arrayAsistencia["Check_in"];?></td>
                    <td><?php echo $arrayAsistencia["Check_out"];?></td>
                    <td><?php echo $arrayAsistencia["Codigo"];?></td>
                    <td><?php echo $arrayAsistencia["Confirmado"];?></td>
                    <td><?php echo $arrayAsistencia["Descripcion_turno"];?></td>
                    <td><?php echo $arrayAsistencia["Nombre_usuario"];?></td>
                    <?php 
                    $timediff=strtotime($arrayAsistencia["Check_out"])-strtotime($arrayAsistencia["Check_in"]);
                    //$timediff->format("H:i:s");
                    ?>
                    <td><?php echo gmdate("H:i:s",$timediff);?></td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>
</div>



<script src="js/vendor/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/plug-ins/1.10.12/sorting/datetime-moment.js"></script>


<script type="text/javascript">

    $.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var desde =  $('#min').val();
        var hasta =  $('#max').val();
        var fechaS=data[0]
        var fecha =  moment(fechaS, 'DD-MM-YYYY').format('YYYY-MM-DD');  // use data for the age column
        
        var comp=fecha<desde;

        if ( ( desde=="" && hasta=="" ) ||
             ( desde=="" && fecha <= hasta ) ||
             ( desde <= fecha  && hasta=="" ) ||
             ( desde <= fecha  && fecha <= hasta ) )
        {
            return true;
        }
        return false;
        /*if ( min === "Invalid date" && max=== "Invalid date" )
        {
            return true;
        }
        else if ( min >= age && max === "Invalid date")
        {
            return true;
        }
        else if ( max <= age && min === "Invalid date")
        {
            return true;
        }
        else if (min >= age && max <= age)
        {
            return true;
        }
        return false;*/
    }
);

$(document).ready( function () {

        $.fn.dataTable.moment( 'DD-MM-YYYY' );

       var table= $('#tablaAsistencias').DataTable({
            "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });


$('#min, #max').change( function() {
    //alert(max);
        table.draw();
    } );

} );
</script>

</body>

</html>

