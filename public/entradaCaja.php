<?php
use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';
require_once 'acceso.inc.php';
include 'scripts/db.inc.php';

use JasperPHP\JasperPHP;

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

if (userHasRole('1') && $_SESSION["turno"]=="Sin iniciar") {
    $error = 'Debe iniciar su turno para realizar este tipo de operaciones';
    include 'accesoDenegado.php';
    exit();
}

$usuario = new Viva\Usuario($viva);

//Obtener operaciones de entrada y comprobantes que las respaldan para llenar dropdown lists
$entradaCaja = $viva->select("Caja_operacion", "*", ["Tipo" => "Entrada"]);
/*$comprobantes = $viva->select("Caja_comprobante", ["[><]Caja_operacion" => ["Respalda_operacion" => "idCaja_operacion"]],
    [
        "Caja_comprobante.idComprobante",
        "Caja_comprobante.Descripcion"
    ],
    ["Caja_operacion.Tipo" => "Entrada"]);*/

//Obtener nombre y id de clientes HABILITADOS, con CUENTA CORRIENTE y DEUDAS para generar recibos
$clientes = $viva->select("Cliente", ["idCliente", "Nombre"], ["AND" => ["Cuenta_corriente" => "Si", "Habilitado" => "Si"]]);

//dump($_SESSION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //dump($_POST);

    //Este metodo solo se activa en el caso de que se ingrese un pago de tarjeta de credito/debito

    $datosUsuario = $usuario->selectNombreUsuarioPassword($viva, $_SESSION["usuario"], $_SESSION["password"]);

    if(isset($_SESSION["idTurno"])) $idTurno=$_SESSION["idTurno"];
    else $idTurno=0;

    $viva->insert("Caja",[
        "idUsuario"=>$datosUsuario[0]["idUsuario"],
        "Fecha"=>date("Y-m-d H:i:s"),
        "Tipo"=>"Entrada",
        "Concepto"=>$_POST["concepto"],
        "Debe"=>$_POST["importe"],
        "Haber"=>"0.00",
        "Observaciones"=>$_POST["observaciones"],
        "Referencia"=>4,
        "idReferencia"=>$_POST["referencia"],
        "idTurno"=>$idTurno
        ]);

    

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Entrada de Caja</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
    <script src="js/vendor/jquery-1.12.0.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</head>

<body>

<?php include 'scripts/logout.inc.php'; ?>

<div class="container">
    <h2>Registrar entrada de caja</h2>


    <form action="" method="post" id="formEntrada">


        <div><label>Concepto:</label>
            <select class="selectUnico" name="concepto" id="concepto" data-placeholder="Seleccione" required>
                <option value="0">Seleccione Concepto</option>
                <?php foreach ($entradaCaja as $entrada): ?>
                    <option
                        value="<?php echo $entrada["idCaja_operacion"]; ?>"><?php echo $entrada["Descripcion"]; ?></option>
                <?php endforeach; ?>
            </select></div>

        <div>
            <label>Importe: $ <input type="number" step="0.01" name="importe" id="importe" min="0" required></label>
        </div>


        <div>
            <label>Numero de referencia del comprobante: <input type="number" name="referencia" id="referencia"
                                                            min="1" required></label>
        </div>

        <div>
            <label>Observaciones: </label>

            <div><textarea name="observaciones" id="observaciones"></textarea></div>

        </div>

        <button data-toggle="modal" type="button" data-target="" class="btn btn-primary" value="Generar Comprobante" id="btnModalComprobante">Aceptar</button>
    </form>



    <div class="modal fade" id="modalAlivioCaja" tabindex="-1" role="dialog" aria-labelledby="basicModal"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                    <h4 class="modal-title" id="myModalLabel">Generar Comprobante de alivio de caja</h4>
                </div>
                <form action="" method="post">
                    <div>
                        <label id="lblImporte" name="importe"></label>
                        <input type="hidden" name="importe" id="inputImporte">
                    </div>
                    <div>
                        <label id="lblObservaciones" style='white-space:pre'></label>
                        <input type="hidden" name="observaciones" id="inputObservaciones">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <input name="submit" class="btn btn-primary" id="btnGenerarComprobante"
                               value="Generar Comprobante">
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRecibo" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="myModalLabel">Generar Recibo</h4>
            </div>
            <form action="" method="post">
                <div><label>Cliente:</label>
                    <select class="selectUnico" name="cliente" id="cliente" data-placeholder="Seleccione" required>
                        <option value="0">Seleccione Cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option
                                value="<?php echo $cliente["idCliente"]; ?>"><?php echo $cliente["Nombre"]; ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div><label>Forma de Pago: <input type="text" name="formaPago" id="formaPago"></label></div>
                <div><label>Importe: $<input type="number" step="0.01" name="importeRecibo" id="importeRecibo" min="0"></label>
                </div>

                <label>Observaciones: </label>

                <div><textarea name="observaciones" id="observacionesRecibo"></textarea></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <input  name="submit" class="btn btn-primary" id="btnGenerarRecibo" value="Generar Recibo" action="">
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {


        $('#concepto').select2();
        $('#comprobante').select2();
        $('#cliente').select2();


        //Funcion que permite generar comprobante de acuerdo al concepto elegido
        $('#concepto').change(function () {
            var concepto = $('#concepto').val();

            //Si el concepto es alivio de caja o cobranza de cuenta, se puede generar comprobante. Se abrira el modal correspondiente para su generacion
                if (concepto == 2) $('#btnModalComprobante').prop('type', 'button').attr('data-target', '#modalRecibo');
                if (concepto == 3) $('#btnModalComprobante').prop('type', 'button').attr('data-target', '#modalAlivioCaja');
                if (concepto == 4) $('#btnModalComprobante').attr('data-target', false).prop('type', 'submit');




        });

        $('#modalAlivioCaja').on('show.bs.modal', function (e) {
            //SI el campo importe esta vacio, no se puede continuar
            if ($('#importe').val() == "") {
                alert("Por favor complete el campo Importe");
                $(this).close();
            }
            //LLenar los campos del modal con importe y observaciones
            else {
                var text = $('#observaciones').val();
                //var obs=text.replace(/\n\r?/g, "<br />");
                $('#lblImporte').text("Importe: $" + $('#importe').val());
                $('#lblObservaciones').text("Observaciones: \n" + text);
                $('#inputImporte').val($('#importe').val());
                $('#inputObservaciones').val(text);
            }
        });

        $('#btnGenerarComprobante').click(function () {
            var importe = $('#importe').val();
            var observaciones = $('#observaciones').val().replace(/\r?\n/g, "\r\n");
            var concepto = $('#concepto').val();
            $.ajax({
                type: 'POST',
                url: '/scripts/alivioCaja.php',
                data: {importe: importe, observaciones: observaciones, concepto: concepto, referencia: 11},
                dataType: 'json',
                success: function (response) {
                    alert("Comprobante de Alivio de Caja Nro " + response + " generado con exito");
                    $("#modalAlivioCaja .close").click();
                    location.reload();
                }
            });
        });

        $('#btnGenerarRecibo').click(function () {
            var importe = $('#importeRecibo').val();
            var observaciones = $('#observacionesRecibo').val().replace(/\r?\n/g, "\r\n");
            var cliente = $('#cliente option:selected').text();
            var formaPago=$('#formaPago').val();
            var idCliente=$('#cliente').val();

            if(cliente == 0 || formaPago=="" || importe=="")
            {
                alert("Debe completar los campos de cliente, importe y forma de pago");
            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: '/scripts/recibo.php',
                    data: {importeRecibo: importe, observaciones: observaciones, cliente: cliente, formaPago:formaPago, idCliente:idCliente},
                    dataType: 'json',
                    success: function (response) {
                        alert("Recibo Nro " + response + " generado con exito");
                        $("#modalRecibo .close").click();
                        //location.reload();
                    }
                });
            }

        });




    })

</script>

</body>

</html>
