<?php

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


$usuario = new Viva\Usuario($viva);
$db = new Viva\BaseDatos($viva);

$listaUsuarios=$usuario->selectEmpleados($viva);


if (isset($_POST['submit']) && $_SERVER['REQUEST_METHOD'] === 'POST')
{
    $id="<script>document.writeln(idUsuario);</script>";
    dump($id);

}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Minimum Setup</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/fullcalendar.css">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="screen, projection">

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>

    <script src="js/moment.min.js"></script>
    <script src="js/fullcalendar.min.js"></script>
    <script src='js/es.js'></script>

</head>

<body>
<?php include 'scripts/logout.inc.php'; ?>

<div id='wrap'>

    <div id='external-events'>
        <div id='external-events-listing'>
            <h4>Empleados Activos</h4>
            <?php foreach($listaUsuarios as $listaUsuario):?>
                <li value="<?php echo ($listaUsuario["idUsuario"]);?>" class='fc-event'><?php echo ($listaUsuario["Nombre"]." ".$listaUsuario["Apellido"]);?></li>
            <?php endforeach; ?>
        </div>
        <!--<p>
            <input type='checkbox' id='drop-remove' />
            <label for='drop-remove'>Quitar de la lista luego de asignar</label>
        </p>-->
    </div>

    <div id='calendar'></div>

    <div style='clear:both'></div>

    <div class="modal fade" id="modalAsignar" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> X </button>
                    <h4 class="modal-title">Asignar Turno</h4>
                </div>
                    <div class="radio">
                        <label><input type="radio" name="optradio" value="1" checked>Ma&#241ana</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="optradio" value="2">Tarde</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="optradio" value="3">Noche</label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"  id="cancelar" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-insertar" id="insertar">Insertar</button>
                    </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalDetallesTurno" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> X </button>
                    <h4 class="modal-title" id="modalTitle">Informacion del Turno</h4>
                    <p hidden id="modalId" class="modal-body"> </p>
                    <h4><strong>Empleado:</strong><span id="modalDescripcion" class="modal-body"></span></h4>
                    <h4><strong>Entrada:</strong><span id="modalEntrada" class="modal-body"></span></h4>
                    <h4><strong>Salida:</strong><span id="modalSalida" class="modal-body"></span></h4>
                    <h4><strong>Codigo:</strong><span id="modalCodigo" class="modal-body"></span></h4>
                    <h4><strong>Check-In:</strong><span id="modalCheckin" class="modal-body"></span></h4>
                    <h4><strong>Check-Out:</strong><span id="modalCheckout" class="modal-body"></span></h4>
                    <h4><strong>Confirmacion:</strong><span id="modalConfirmado" class="modal-body"></span></h4>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"  id="Cerrar" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-danger" id="eliminar">Eliminar</button>
                </div>
            </div>
        </div>

    </div>


<script>
    $(document).ready(function(){

        var idUsuario;
        var fechaTurno;
        var idAsistencia;

        $('#external-events .fc-event').each(function() {

            // store data so the calendar knows to render an event upon drop
            $(this).data('event', {
                title: $.trim($(this).text()), // use the element's text as the event title
                stick: true // maintain when user navigates (see docs on the renderEvent method)
            });

            // make the event draggable using jQuery UI
            $(this).draggable({
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0  //  original position after the drag
            });

        });

        // page is now ready, initialize the calendar...

        $('#calendar').fullCalendar({
            header: {
                right: 'today,month,agendaDay,agendaWeek prev,next'
            },
            events: {
                url: '/scripts/calendarioTurnosJSON.php',
                type: 'POST', // Send post data
                error: function() {
                    alert('There was an error while fetching events.');
                }
            },
            eventConstraint: {
                start: moment().format('YYYY-MM-DD'),
                end: '2100-01-01' // hard coded goodness unfortunately
            },
            displayEventEnd: true,
            timeFormat:'H(:mm)',
            editable: true,
            droppable: true, // this allows things to be dropped onto the calendar
            dragRevertDuration: 0,
            drop: function(date) {
                // is the "remove after drop" checkbox checked?
                if ($('#drop-remove').is(':checked')) {
                    // if so, remove the element from the "Draggable Events" list
                    $(this).remove();
                }

                idUsuario = $(this).val(); //Obtener el ID del usuario que se arrastra al calendario
                fechaTurno=date.format();//Obtener el fecha del evento

                //alert($(this).start());

                //Abrir y mostrar el modal de seleccion de turnos
                $('#modalTitle').html($(this).title);
                $('#modalAsignar').modal();

            },
            eventDragStop: function( event, jsEvent, ui, view ) {

                if(isEventOverDiv(jsEvent.clientX, jsEvent.clientY)) {
                    $('#calendar').fullCalendar('removeEvents', event._id);
                    var el = $( "<div class='fc-event'>" ).appendTo( '#external-events-listing' ).text( event.title );
                    el.draggable({
                        zIndex: 999,
                        revert: true,
                        revertDuration: 0
                    });
                    el.data('event', { title: event.title, id :event.id, start:event.start, stick: true });

                }

            },
            eventClick:  function(event, jsEvent, view) {
                //var id=event.id;
                $('#modalId').html(event.id);
                $('#modalDescripcion').html(event.title);
                $('#modalEntrada').html(event.entrada);
                $('#modalSalida').html(event.salida);
                $('#modalCodigo').html(event.codigo);
                $('#modalCheckin').html(event.checkin);
                $('#modalCheckout').html(event.checkout);
                $('#modalConfirmado').html(event.confirmado);
                $('#modalDetallesTurno').modal();
            }
        });
        var isEventOverDiv = function(x, y) {

            var external_events = $( '#external-events' );
            var offset = external_events.offset();
            offset.right = external_events.width() + offset.left;
            offset.bottom = external_events.height() + offset.top;

            // Compare
            if (x >= offset.left
                && y >= offset.top
                && x <= offset.right
                && y <= offset .bottom) { return true; }
            return false;

        }

        document.getElementById('insertar').addEventListener('click', function() {
            var turno=$('input[name=optradio]:checked').val();
            //alert(fechaTurno);

            $.ajax({
                type: 'POST',
                url: '/scripts/insertarNuevoTurno.php',
                data: { turno: turno, usuario: idUsuario, fecha:fechaTurno },
                success: function(html) {
                    //alert("El turno se ha creado con exito");
                    $('#calendar').fullCalendar('removeEvents', event._id); //Quita el evento soltado
                    $('#calendar').fullCalendar( 'refetchEvents' );//Actualiza los eventos
                }
            });

            $("#modalAsignar .close").click()
        }, false);



        document.getElementById('eliminar').addEventListener('click', function(event) {

            var confirmado=$('span[id=modalConfirmado]').text();

            if(confirmado!="Pendiente")
            {
                alert("No puede eliminarse un turno que se haya iniciado");
            }

            else {
                var id = $('p[id=modalId]').text();
                //alert(id);

                $.ajax({
                    type: 'POST',
                    url: '/scripts/borrarTurnoAsignado.php',
                    data: {idAsistencia: id},
                    success: function (html) {
                        alert("El turno se ha eliminado");
                        $('#calendar').fullCalendar('removeEvents', event._id); //Quita el evento soltado
                        $('#calendar').fullCalendar('refetchEvents');//Actualiza los eventos
                    }
                });

                $("#modalDetallesTurno .close").click()
            }
        }, false);

        document.getElementById('cancelar').addEventListener('click', function() {

            $('#calendar').fullCalendar('removeEvents', event._id); //Quita el evento soltado
            $('#calendar').fullCalendar( 'refetchEvents' );//Actualiza los eventos

        }, false);



    });
</script>

</body>
</html>
