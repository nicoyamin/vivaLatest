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
if (!userHasRole('1') && !userHasRole('2'))
{
    $error = 'Solo administradores pueden acceder a esta pagina';
    include 'accesoDenegado.php';
    exit();
}


$listaProds=$viva->select("Producto",["idProducto","Nombre_producto","Unidad_producto","Cantidad_unitaria_producto","Codigo_barras_producto"],["AND"=>["Habilitado"=>"Si","Existencia_producto[>]"=>0]]);

//dump($clientes);
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{

    dump($_POST);
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
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>


</head>
<body>

<?php include 'scripts/logout.inc.php'; ?>

<h1>Nueva Venta</h1>
<h2>Seleccione los productos</h2>

<label for="codBarras">Por codigo de barras: <input type="text" name="codBarras" id="codBarras"></label>


<label for="producto"> Por Nombre:
    <select class="selectUnico" name="producto" id="producto" data-placeholder="Seleccione">
        <option value="0">Seleccione producto</option>
        <?php foreach($listaProds as $listaProd):?>
            <option value="<?php echo $listaProd["idProducto"];?>"><?php echo $listaProd["Nombre_producto"]." ".$listaProd["Cantidad_unitaria_producto"]." ".$listaProd["Unidad_producto"];?></option>
        <?php endforeach; ?>
    </select>
</label>



<form action="" method="post" id="formProductos">


    <table  id="tablaProductos" class="table table-striped">
        <thead>
        <tr>
            <th>Producto</th>
            <th>Stock Disponible</th>
            <th>Precio Unitario</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Quitar Fila</th>
        </tr>
        </thead>

    </table>

    <div><label>Descuento: <input type="number" step="0.01" min="0" max="100" name="descuento" id="desc"> %</label></div>
    <div><label>Intereses: <input type="number" step="0.01" min="0" name="interes" id="interes"> %</label></div>
    <div><label id="total">Total: $ 0</label></div>

    <div><label for="formaPago">Forma de Pago: </label>
        <select class="selectUnico" name="formaPago" id="formaPago" data-placeholder="Seleccione">
            <option value="1">Efectivo</option>
            <option value="2">Tarjeta de Debito</option>
            <option value="3">Tarjeta de Credito</option>
            <option value="4">Cuenta Corriente</option>
        </select>
    </div>

    <input type="hidden" name="totalPost" id="totalPost" value="0">

    <input type="submit" value="Aceptar" action="">
    <input type="button" class="btn btn-success" id="btnModalPago"  data-toggle="modal" data-target="#pagoEfectivo" value="Aceptar" action="">



<div class="modal fade" id="pagoEfectivo" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> X </button>
                    <h4 class="modal-title" id="modalTitle">Pago Efectivo</h4>
                    <p hidden id="modalId" class="modal-body"> </p>
                    <div><strong>Nombre Cliente (opcional):</strong><input id="efectivoCliente" class="modal-body" name="cliente" type="text"></div>
                    <div><strong>Domicilio (opcional):</strong><input id="efectivoDomicilio" class="modal-body" name="domicilio" type="text"></div>
                    <div><label for="iva">IVA: </label>
                        <select class="selectUnico" name="ivaEfectivo" id="iva" data-placeholder="Seleccione">
                            <option value="Consumidor Final">Consumidor Final</option>
                            <option value="Exento">Exento</option>
                            <option value="Responsable Inscripto">Responsable Inscripto</option>
                            <option value="Responsable No Inscripto">Responsable No Inscripto</option>
                            <option value="Monotributo">Monotributo</option>
                        </select>
                    </div>
                    <div><strong>CUIT/CUIL:</strong><input id="cuit" class="modal-body" name="cuit" type="text"></div>
                    <div><strong>Tarjeta:</strong><input id="debitoTarjeta" class="modal-body" name="tarjeta" type="text"></div>
                    <div><strong>Cantidad de pagos:</strong><input id="pagos" class="modal-body" name="pagos" type="number" min="1" value="1"></div>
                    <div><strong>Nro de cupon de operacion:</strong><input id="creditoCupon" class="modal-body" name="cupon" type="text"></div>
                    <div class="modal-footer">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"  id="Cerrar" data-dismiss="modal">Cancelar</button>
                        <input type="button" value="Aceptar" action="" class="submitAll">
                    </div>
                </div>

        </div>

    </div>
</div>

<div class="modal fade" id="pagoDebito" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> X </button>
                    <h4 class="modal-title" id="modalTitle">Pago con Tarjeta de Debito</h4>
                    <p hidden id="modalId" class="modal-body"> </p>
                    <div><strong>Nombre Cliente (opcional):</strong><input id="debitoCliente" class="modal-body" name="cliente" type="text"></div>
                    <div><strong>Domicilio (opcional):</strong><input id="debitoDomicilio" class="modal-body" name="domicilio" type="text"></div>
                    <div><label for="iva">IVA: </label>
                        <select class="selectUnico" name="iva" id="debitoIva" data-placeholder="Seleccione">
                            <option value="Consumidor Final">Consumidor Final</option>
                            <option value="Exento">Exento</option>
                            <option value="Responsable Inscripto">Responsable Inscripto</option>
                            <option value="Responsable No Inscripto">Responsable No Inscripto</option>
                            <option value="Monotributo">Monotributo</option>
                        </select>
                    </div>
                    <div><strong>CUIT/CUIL:</strong><input id="debitoCUIT" class="modal-body" name="cuit" type="text"></div>
                    <div><strong>Tarjeta:</strong><input id="debitoTarjeta" class="modal-body" name="tarjeta" type="text"></div>
                    <div><strong>Nro de cupon de operacion:</strong><input id="debitoCupon" class="modal-body" name="cupon" type="text"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"  id="Cerrar" data-dismiss="modal">Cancelar</button>
                        <input type="button" value="Aceptar" action="" class="submitAll">
                    </div>
                </div>

        </div>

    </div>
</div>

<div class="modal fade" id="pagoCredito" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> X </button>
                    <h4 class="modal-title" id="modalTitle">Pago con Tarjeta de Credito</h4>
                    <p hidden id="modalId" class="modal-body"> </p>
                    <div><strong>Nombre Cliente (opcional):</strong><input id="creditoCliente" class="modal-body" name="cliente" type="text"></div>
                    <div><strong>Domicilio (opcional):</strong><input id="creditoDomicilio" class="modal-body" name="domicilio" type="text"></div>
                    <div><label for="iva">IVA: </label>
                        <select class="selectUnico" name="iva" id="creditoIva" data-placeholder="Seleccione">
                            <option value="Consumidor Final">Consumidor Final</option>
                            <option value="Exento">Exento</option>
                            <option value="Responsable Inscripto">Responsable Inscripto</option>
                            <option value="Responsable No Inscripto">Responsable No Inscripto</option>
                            <option value="Monotributo">Monotributo</option>
                        </select>
                    </div>
                    <div><strong>CUIT/CUIL:</strong><input id="creditoCUIT" class="modal-body" name="cuit" type="text"></div>
                    <div><strong>Tarjeta:</strong><input id="creditoTarjeta" class="modal-body" name="tarjeta" type="text"></div>
                    <div><strong>Cantidad de pagos:</strong><input id="creditoPagos" class="modal-body" name="pagos" type="number" min="1" value="1"></div>
                    <div><strong>Nro de cupon de operacion:</strong><input id="creditoCupon" class="modal-body" name="cupon" type="text"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"  id="Cerrar" data-dismiss="modal">Cancelar</button>
                        <input type="button" value="Aceptar" action="" class="submitAll">
                    </div>
                </div>

        </div>

    </div>
</div>

<div class="modal fade" id="pagoCuentaCorriente" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> X </button>
                    <h4 class="modal-title" id="modalTitle">Pago en Cuenta Corriente</h4>
                    <p hidden id="modalId" class="modal-body"> </p>
                    <select class="selectUnico" name="cliente" id="selectCliente" data-placeholder="Seleccione">
                        <option value="0">Seleccione cliente</option>
                    </select>
                    <div><label>Margen: $</label><label id="lblMargen">N/A</label></div>
                    <div><label>Balance: $</label><label id="lblBalance">N/A</label></div>
                    <div><label>Balance luego de compra: $</label><label id="lblBalancePost">N/A</label></div>
                    <div><label for="iva">IVA: </label>
                        <select class="selectUnico" name="iva" id="cuentaIva" data-placeholder="Seleccione">
                            <option value="Consumidor Final">Consumidor Final</option>
                            <option value="Exento">Exento</option>
                            <option value="Responsable Inscripto">Responsable Inscripto</option>
                            <option value="Responsable No Inscripto">Responsable No Inscripto</option>
                            <option value="Monotributo">Monotributo</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"  id="Cerrar" data-dismiss="modal">Cerrar</button>
                        <input type="button" value="Aceptar" action="" class="submitAll" id="btnCuenta">
                    </div>
                </div>

        </div>

    </div>
</div>


</form>



<script>
    $(document).ready(function(){

        var nroFilas=0;

        var table=$('#tablaProductos').DataTable({

            "info":false,
            "ordering":false,
            "paging":false,
            "searching":false,
            dom: 'Bfrtip',
            buttons: [
                'pdf'
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });

        $("#codBarras").focus();
        $("#producto").select2();
        $("#formaPago").select2();
        $("#iva").select2();
        $("#debitoIva").select2();
        $("#creditoIva").select2();
        $("#cuentaIva").select2();
        $("#selectCliente").select2();
        $("#btnCuenta").prop("disabled", true);

        $("#desc").change(function(){
            calcularTotal();
        });

        $("#interes").change(function(){
            calcularTotal();
        });

        //Postea el modal correspondiente y el formulario con el detalle de la venta
        $(".submitAll").click(function(){

            //var data=$(this).parents('form:first').serializeArray();
            //$("#formEfectivo").submit();
            //$('form').submit();
            //$("#formProductos").submit();
            //alert(data);
        });

        $("#pagoCuentaCorriente").on('shown.bs.modal', function (e) {
            $("#selectCliente").empty();
            $.ajax({
                type: 'POST',
                url: '/scripts/manejarDetallesVenta.php',
                data: {accion:3},                    //accion 3 es traer detalles de los clientes habilitados para comprar a cuenta corriente
                dataType: 'json',
                success: function (response) {
                    $("#selectCliente").append("<option value=0>Selecione cliente</option>");

                    $.each(response.data, function(key,value){

                        $("#selectCliente").append("<option value=" + value.idCuenta_corriente +">"+value.Nombre+"</option>");
                        //alert(value);
                    });
                }

            });
        });

        $("#selectCliente").change(function(){
            var idCliente=$("#selectCliente").val();

            if(idCliente==0)
            {
                $("#lblMargen").text("N/A");
                $("#lblBalance").text("N/A");
                $("#lblBalancePost").text("N/A");
            }
            else
            {
                $.ajax({
                    type: 'POST',
                    url: '/scripts/manejarDetallesVenta.php',
                    data: {id:idCliente,accion:4},                    //accion 4 es traer detalles de margen y balance del cliente seleccionado
                    dataType: 'json',
                    success: function (response) {
                        //alert(response[0].margen);
                        traerDetallesCuenta(response);
                    }

                });
            }

        });

        $("#producto").change(function(){
          var id=$("#producto").val();

            $.ajax({
                type: 'POST',
                url: '/scripts/manejarDetallesVenta.php',
                data: {id:id, accion:1},                    //accion 1 es traer detalles del producto seleccionado en el dropdown
                dataType: 'json',
                success: function (response) {
                    procesarRespuesta(response);
                }

            });

        });

        $("#codBarras").keyup(function(event){          //Dispara cuando tiene foco y se libera una tecla
            if(event.which==13)     //Verificar si la tecla presionada es enter
            {
                var id=$("#codBarras").val();

                $.ajax({
                    type: 'POST',
                    url: '/scripts/manejarDetallesVenta.php',
                    data: {id:id, accion:2},                    //accion 2 es traer detalles del producto mediante codigo de barras
                    dataType: 'json',
                    success: function (response) {
                        $("#codBarras").focus();        //restaurar foco en el input
                        $("#codBarras").val("");        //Dejar input en blanco
                        procesarRespuesta(response);

                    }

                });
            }
        });

        //Cuando un elemento se agrega dinamicamente, deben vincularse sus eventos mediante un elemento padre no dinamico
        $("#tablaProductos").on("change",'input[class=cantidad]',function(){
            var max=parseInt(this.max);

            if(parseInt(this.value)>max) this.value=max;        //Chequear que no se ingrese una cantidad mayor al stock disponible
            //var fila=$(this).parent().parent().index();
            var fila=$(this).parent().parent().children("td:nth-child(6)").children("input[id=quitarFila]").attr("name"); //Obterner el numero de fila, escondido como atributo en el boton de quitar fila
            //#tablaProductos > tbody > tr > td:nth-child(6)
            var precioUnitario=parseFloat($("#precio"+fila).text());
            $("#subtotal"+fila).text(this.value*precioUnitario);

            calcularTotal();

        });

        //Quita la fila al hacer click en boton correspondiente
        $("#tablaProductos").on( 'click', 'input[id=quitarFila]', function () {
            //alert("Hola");
            table
                .row( $(this).parents('tr') )
                .remove()
                .draw();

            calcularTotal();
        } );

        //Seleccionar el modal que se abrira de acuerdo a la forma de pago seleccionada
        $("#btnModalPago").click(function(){
            var formaPago=parseInt($("#formaPago").val());
            if(formaPago==1)
            {
                //$("#btnModalPago").attr("data-target", "#pagoEfectivo");

            }
            else if(formaPago==2) $("#btnModalPago").attr("data-target", "#pagoDebito");
            else if(formaPago==3) $("#btnModalPago").attr("data-target", "#pagoCredito");
            else if(formaPago==4) $("#btnModalPago").attr("data-target", "#pagoCuentaCorriente");
            else
            {
                alert("Debe elegir un medio de pago valido");
                $("#btnModalPago").attr("data-target", "");
            }

        });

        function calcularTotal()        //Calcula el total de la venta
        {
            var totalVenta=0;

            var descuento=$("#desc").val();     //Calcular el descuento
            if(descuento > 100) descuento=100;
            else if(descuento < 0 || descuento=="") descuento=0;
            var factorDesc=1-(descuento/100);

            var interes=$("#interes").val();       //Calcula el interes
            if(interes < 0 || interes=="") interes=0;
            var factorInteres=1+(interes/100);

            $('#tablaProductos > tbody  > tr > td > label[class=subtotal]').each(function() {           //Suma los totales de los productos vendidos
                totalVenta=totalVenta+parseFloat($(this).text());

            });
            totalVenta=parseFloat((totalVenta*factorDesc)*factorInteres).toFixed(2);
            $("#total").text("Total: $ "+totalVenta);
            $("#totalPost").val(totalVenta);
        }

        function procesarRespuesta(response)        //Funcion que añade una nueva fila a la tabla de productos comprados
        {
            var idProd=response[0].Id;              //Usar Jquery para armar componenetes de cada fila
            var filaId=$('<input>').attr({type: 'hidden', id: 'id'+nroFilas, name: 'id'+nroFilas, value:idProd}).prop('outerHTML');
            var filaCant=$('<input>').attr({type: 'number', step:'1', min:1, max:response[0].Stock, class:'cantidad', id: 'cantidad'+nroFilas, name: 'cantidad'+nroFilas, value:1}).prop('outerHTML');
            var filaPrecio=$('<label>').attr({class:"precio", id:'precio'+nroFilas}).text(response[0].Precio).prop('outerHTML');
            var filaSubtotal=$('<label>').attr({class:"subtotal", id:'subtotal'+nroFilas}).text(response[0].Precio).prop('outerHTML');
            var filaQuitar=$('<input>').attr({type:'button',action:"", id:'quitarFila',name:nroFilas, value:'Quitar'}).prop('outerHTML');
            //Agregar la fila dinamicamente a la tabla
            var fila=table.row.add([
                response[0].Producto+filaId,
                response[0].Stock,
                "$ "+filaPrecio,
                filaCant,
                "$ "+filaSubtotal,
                filaQuitar
            ]).draw(false);
            nroFilas++;
            calcularTotal();
        }
    });

    function traerDetallesCuenta(response)
    {
        var totalVenta=$("#totalPost").val();
        var color="Green";
        $("#btnCuenta").prop("disabled", false);
        $("#lblMargen").text(response[0].margen);
        $("#lblBalance").text(response[0].balance);
        var balanceLuego=response[0].balance-totalVenta;
        if(balanceLuego<0)
        {
            if(Math.abs(balanceLuego)>response[0].margen)
            {
                color="Red";
                $("#btnCuenta").prop("disabled", true);
            }
        }
        $("#lblBalancePost").css("color", color).text(balanceLuego);
    }



</script>


</body>
</html>
