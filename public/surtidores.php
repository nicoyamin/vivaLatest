<?php
use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';

include 'scripts/db.inc.php';

include("acceso.inc.php");


if (!userIsLoggedIn())
{
    include 'login.php';
    exit();
}
if ($_SESSION["surtidores"]=="Si")
{
    $error = 'Ya se realizo el control de surtidores para este turno';
    include 'accesoDenegado.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION["surtidores"]="Si";

    header("Location: index.php");
    die();
    //dump($_SESSION);

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Control de Surtidores</title>
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
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/plug-ins/1.10.16/api/sum().js"></script>


</head>
<body>

<?php include 'scripts/logout.inc.php'; ?>

<h1>Control de Surtidores</h1>

<input type="hidden" id="usuario" value="<?php echo $_SESSION["usuario"]; ?>" />

<form action="" method="post" id="formSurtidores">

    <label>Surtidor Nro: <input type="number" min="1" id="surtidorNro"></label>
    <label>Inicio (m3): <input type="number" step="0.01" min="0" id="inicio"></label>
    <label>Salida (m3): <input type="number" step="0.01" min="0" id="salida"></label>
    <label>Precio : <input type="number" step="0.01" min="0" id="precio"></label>

    <div>
        <button type="button" class="btn btn-primary" id="btnNuevaFila">Nueva Fila</button>
        <button type="button" class="btn btn-success" id="btnAceptar">Aceptar</button>

    </div>

    <table  id="tablaSurtidores" class="table table-striped">
        <thead>
        <tr>
            <th>Surtidor</th>
            <th>Inicio</th>
            <th>Salida</th>
            <th>Metros 3</th>
            <th>Precio</th>
            <th>Total</th>
            <th>Quitar fila</th>
        </tr>
        </thead>

        <tfoot>
        <tr>
            <th colspan="0" style="text-align:left">Total Gral.</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>



        </tfoot>

    </table>


</form>



<script>
    $(document).ready(function(){

        var nroFilas=1;
        var totalInicio=0;
        var totalFin=0;
        var totalMetros=0;
        var totalGral=0;
        var hoy = new Date().toJSON().slice(0,10).replace(/-/g,'/');

        var table=$('#tablaSurtidores').DataTable({

            "info":false,
            "ordering":false,
            "paging":false,
            "searching":false,
            dom: 'Bfrtip',
            buttons: [
               {
                   extend: 'pdfHtml5',
                   exportOptions: {
                       columns: [0, 1, 2, 3, 4, 5]
                   },
                    footer:true,
                    className: "btnSurtidores",
                    filename: "Control de Surtidores"+"-"+$("#usuario").val()+"-"+hoy,
                   pageSize:"A4",
                   text:""
                }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });

        table.buttons('.btnSurtidores').disable();





        $("#btnNuevaFila").on("click", function(){
            nuevaFila();
        });

        function nuevaFila()
        {
            var surtidorNro=$("#surtidorNro").val();
            var inicio=$("#inicio").val();
            var salida=$("#salida").val();
            var precio=$("#precio").val();
            var metros= salida-inicio;



            //totalInicio=parseFloat(totalInicio)+parseFloat(inicio);
            //totalFin=parseFloat(totalFin)+parseFloat(salida);
            //totalMetros=parseFloat(totalMetros)+parseFloat(metros);
            //totalGral=parseFloat(totalGral)+parseFloat(precio*metros);

            var filaSurtidor=$('<label>').text(surtidorNro).prop('outerHTML');
            var filaInicio=$('<label>').text(inicio).prop('outerHTML');
            //var filaInicio=$('<input>').attr({type: 'number', step:'0.01', min:0, value:inicio}).prop('outerHTML');
            var filaSalida=$('<label>').text(salida).prop('outerHTML');
            var filaMetros=$('<label>').text(metros.toFixed(2)).prop('outerHTML');
            var filaPrecio=$('<label>').text(precio).prop('outerHTML');
            var filaTotal=$('<label>').text((precio*metros).toFixed(2)).prop('outerHTML');
            var filaQuitar=$('<input>').attr({type:'button',action:"", id:'quitarFila',name:nroFilas, value:'Quitar'}).prop('outerHTML');
            //Agregar la fila dinamicamente a la tabla
            var fila = table.row.add([
                filaSurtidor,
                filaInicio,
                filaSalida,
                filaMetros,
                filaPrecio,
                filaTotal,
                filaQuitar
            ]).draw(false);


            //alert(totalInicio);

            /*table.column( 1 ).every( function () {
                var sum = this
                    .data()
                    .reduce( function (a,b) {
                        return  parseFloat(a)+ parseFloat(b);
                    } );

                $( table.column(1).footer() ).html(sum);
            } );*/
            totalInicio=table.column( 1 ).data().sum();
            totalFin=table.column( 2 ).data().sum();
            totalMetros=table.column( 3 ).data().sum();
            totalGral=table.column( 5 ).data().sum();
            //alert(totalInicio);

            $( table.column(1).footer() ).html(totalInicio.toFixed(2));
            $( table.column(2).footer() ).html(totalFin.toFixed(2));
            $( table.column(3).footer() ).html(totalMetros.toFixed(2));
            $( table.column(5).footer() ).html("$ "+totalGral.toFixed(2));


            $("#surtidorNro").val(++surtidorNro);
            $("#salida").val("");
            $("#inicio").val("");


        }

        $("#tablaSurtidores").on( 'click', 'input[id=quitarFila]', function () {
            //alert("Hola");
            table
                .row( $(this).parents('tr') )
                .remove()
                .draw();

            //calcularTotal();
        } );

        $("#btnAceptar").on("click", function(){
            table.buttons('.btnSurtidores').trigger();
            $("#formSurtidores").submit();

        });






    });

</script>


</body>
</html>
