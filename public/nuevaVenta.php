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
$listaProds=$viva->select("Producto",["idProducto","Nombre_producto","Unidad_producto","Cantidad_unitaria_producto","Codigo_barras_producto"],["AND"=>["Habilitado"=>"Si","Existencia_producto[>]"=>0]]);

//dump($clientes);
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{

    //dump($_POST);

    //crear un array que contiene todos los posibles nombres de los datos concernientes a una venta
    $info=array("descuento","interes","formaPago","totalPost","cliente","domicilio","iva","cuit","tarjeta","pagos","cupon");
    $infoVenta=array();
    $productoCant=array();
    $hoy=date("Y-m-d H:i:s");
    //dump($hoy);
    //usar el array info para filtrar los datos presentes a al array infoVenta, dejando en $_post solo los ids y cantidades de productos vendidos
    foreach($info as $i)
    {
        if(isset($_POST[$i]))
        {
            $infoVenta[$i]=$_POST[$i];
            unset($_POST[$i]);
        }
    }

    reset($_POST);
    for($i = 0; $i < count($_POST); $i=$i+2) {
        $productoCant[current($_POST)]=next($_POST);
        next($_POST);
    }

    unset($_POST);

    //Si la forma de pago es efectivo, debito o credito, establecer las variables correspondientes para insertar en tabla Ventas:
    if($infoVenta["formaPago"]!=4)
    {
        if($infoVenta["formaPago"]==1)$forma_pago="Contado efectivo";
        elseif($infoVenta["formaPago"]==2)$forma_pago="Debito ".$infoVenta["tarjeta"];
        elseif($infoVenta["formaPago"]==3)$forma_pago="Credito ".$infoVenta["tarjeta"]." ".$infoVenta["pagos"]." pago/s";
        if(isset($infoVenta["cupon"])) $referencia=$infoVenta["cupon"];
        else $referencia="-";
        $iva=$infoVenta["iva"];
        $estado="Cerrada";
        $saldo=0;
        $cliente=0;
    }

    else
    {
        //dump($infoVenta);
        $idCuenta=$infoVenta["cliente"];
        $datosCuenta=$viva->select("Cuenta_corriente",["idCliente","Margen","Balance"],["idCuenta_corriente"=>$idCuenta]);
        $cliente=$datosCuenta[0]["idCliente"];
        $balance=$datosCuenta[0]["Balance"];
        $margen=$datosCuenta[0]["Margen"];
        $totalVenta=$infoVenta["totalPost"];

        $forma_pago="Cuenta Corriente";
        $referencia="-";
        $iva=$infoVenta["iva"];

        if($balance-$totalVenta>0)
        {
            $saldo=0;
            $estado="Cerrada";
        }
        else
        {
            $saldo=abs($balance-$totalVenta);
            $estado="Saldo Pendiente";
        }


        $viva->insert("Cuenta_corriente_movimientos", ["idCuenta_corriente"=>$idCuenta,"Concepto"=>"Compra", "Medio_pago"=>"-", "Fecha"=>$hoy, "Debe"=>$totalVenta, "Haber"=>"0", "Saldo"=>($balance-$totalVenta), "Referencia"=>"0"]);
        $viva->update("Cuenta_corriente",["Fecha_ultimo_movimiento"=>date("Y-m-d"), "Balance"=>$balance-$totalVenta],["idCuenta_corriente"=>$idCuenta]);

        $datosCliente=$viva->select("Cliente",["Nombre", "Cuit_cuil"], ["idCliente"=>$cliente]);
        $infoVenta["cliente"]=$datosCliente[0]["Nombre"];
        $infoVenta["cuit"]=$datosCliente[0]["Cuit_cuil"];

        //dump($datosCuenta);

    }



    $idVenta=$viva->insert("Venta",["Fecha"=>$hoy,"Importe"=>$infoVenta["totalPost"],"saldoImporte"=>$saldo, "Forma_pago"=>$forma_pago,"Referencia_pago"=>$referencia, "Estado"=>$estado, "Iva_cliente"=>$iva ,"idCliente"=>$cliente]);

    foreach($productoCant as $key => $value)
    {
        $idProducto=$key;
        $cantidad=$value;
        //Usar una declaracion PDO para insertar uno por unos los registros en la tabla Venta_producto
        $sql=$viva->pdo->prepare("INSERT INTO Venta_Producto (idVenta, idProducto, Cantidad, Precio) VALUES (:idVenta, :idProducto, :cantidad, (SELECT Precio_venta_producto FROM Producto WHERE idProducto=:idProducto))");
        $sql->bindParam(':idVenta', $idVenta, PDO::PARAM_INT);
        $sql->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
        $sql->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $sql->execute();

        //Usar una declaracion PDO para insertar uno por unos los registros en la tabla Stock_movimietnos
        $sql=$viva->pdo->prepare("INSERT INTO Stock_movimientos (idProducto, Cantidad, Fecha,idStock_tipo_movimientos,idStock_concepto ,Stock_antes, Stock_luego) VALUES (:idProducto,:cantidad,:hoy,2,10, (SELECT Existencia_producto as Stock FROM Producto WHERE idProducto=:idProducto), Stock_antes-:cantidad )");
        $sql->bindParam(':hoy', $hoy, PDO::PARAM_STR);
        $sql->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
        $sql->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $sql->execute();

        //ActualizaR el stock de los productos
        $viva->update("Producto",["Existencia_producto[-]"=>$cantidad],["idProducto"=>$idProducto]);

    }

    //Crear Remito/Factura
    $jasper = new JasperPHP;

    $database = [
        'driver' => 'mysql',
        'username' => 'homestead',
        'password'=> 'secret',
        'database'=> 'VIVA',
        'host' => '127.0.0.1',
        'port' => '3306'
    ];

    $nroComprobante = $viva->select("Factura", ["numero"], ["numero[>=]" => 1, "ORDER" => ["numero" => "DESC"], "LIMIT" => 1]);
    if ($nroComprobante != null) $nroComprobante = $nroComprobante[0]["numero"] + 1;
    else $nroComprobante = 1;

    $nombre="Remito_Nro_".$nroComprobante;

    if($infoVenta["cliente"]=="") $infoVenta["cliente"]="-";
    if(!isset($infoVenta["domicilio"]) || $infoVenta["domicilio"]=="" ) $infoVenta["domicilio"]="-";
    if(!isset($infoVenta["cuit"]) || $infoVenta["cuit"]=="") $infoVenta["cuit"]="-";
    if($infoVenta["descuento"]=="") $infoVenta["descuento"]=0;
    if($infoVenta["interes"]=="") $infoVenta["interes"]=0;

    $jasper->process(
    // Ruta y nombre de archivo de entrada del reporte
        'reports/ventas/remito.jasper',
        'reports/ventas/'.$nombre, // Ruta y nombre de archivo de salida del reporte (sin extensión)
        array('pdf'), // Formatos de salida del reporte
        array('numero' => $nroComprobante,
            'fecha'=>date("d-m-Y"),
            'cliente'=>$infoVenta["cliente"],
            'domicilio'=>$infoVenta["domicilio"],
            'iva'=>$infoVenta["iva"],
            'cuit'=>$infoVenta["cuit"],
            'pago'=>$forma_pago,
            'idVenta'=>$idVenta,
            'descuento'=>$infoVenta["descuento"],
            'interes'=>$infoVenta["interes"],
            'totalPost'=>$infoVenta["totalPost"]
        ) // Parámetros del reporte
        ,$database
    )->execute();

    $nombre=$nombre.".pdf";

    $ruta='reports/ventas/'.$nombre;

    $atras=$_SERVER["PHP_SELF"];
    header("Location: ventanaDescarga.php?nombre=$nombre&ruta=$ruta&atras=$atras");

    //Insertar registro en tabla factura
    $viva->insert("Factura",["idVenta"=>$idVenta, "numero"=>$nroComprobante, "Fecha"=>$hoy]);

    //Obtener datos para insertar en tabla CAJA. Solo se inserta en caso de pago en efectivo
    $datosUsuario = $usuario->selectNombreUsuarioPassword($viva, $_SESSION["usuario"], $_SESSION["password"]);
    if(isset($_SESSION["idTurno"])) $idTurno=$_SESSION["idTurno"];
    else $idTurno=0;


    $viva->insert("Caja",[
        "idUsuario"=>$datosUsuario[0]["idUsuario"],
        "Fecha"=>$hoy,
        "Tipo"=>"Entrada",
        "Concepto"=>1,
        "Debe"=>$infoVenta["totalPost"],
        "Haber"=>"0.00",
        "Observaciones"=>"",
        "Referencia"=>1,
        "idReferencia"=>$nroComprobante,
        "idTurno"=>$idTurno
    ]);
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

    <div><label>Descuento: <input type="number" step="0.01" min="0" max="100" name="descuento" id="desc" value="0"> %</label></div>
    <div><label>Intereses: <input type="number" step="0.01" min="0" name="interes" id="interes" value="0"> %</label></div>
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
    <input type="button" class="btn btn-success" id="btnModalPago"  data-toggle="modal" data-target="#pago" value="Aceptar" action="">



<div class="modal fade" id="pago" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> X </button>
                    <h1 class="modal-title" id="modalPagoTitle">Pago Efectivo</h1>
                    <p hidden id="modalId" class="modal-body"> </p>

                    <div id="selectClienteDiv" hidden>
                        <select class="selectUnico" name="cliente" id="selectCliente" data-placeholder="Seleccione" disabled>
                            <option value="0">Seleccione cliente</option>
                        </select>
                    </div>
                    <div id="margenDiv" hidden><label>Margen: $</label><label id="lblMargen">N/A</label></div>
                    <div id="balanceDiv" hidden><label>Balance: $</label><label id="lblBalance">N/A</label></div>
                    <div id="balancePostDiv" hidden><label>Balance luego de compra: $</label><label id="lblBalancePost">N/A</label></div>

                    <div id="clienteDiv"><strong>Nombre Cliente (opcional):</strong><input id="cliente" class="modal-body" name="cliente" type="text"></div>
                    <div id="domicilioDiv"><strong>Domicilio (opcional):</strong><input id="domicilio" class="modal-body" name="domicilio" type="text"></div>
                    <div><label for="iva">IVA: </label>
                        <select class="selectUnico" name="iva" id="iva" data-placeholder="Seleccione">
                            <option value="Consumidor Final">Consumidor Final</option>
                            <option value="Exento">Exento</option>
                            <option value="Responsable Inscripto">Responsable Inscripto</option>
                            <option value="Responsable No Inscripto">Responsable No Inscripto</option>
                            <option value="Monotributo">Monotributo</option>
                        </select>
                    </div>
                    <div id="cuitDiv"><strong>CUIT/CUIL:</strong><input id="cuit" class="modal-body" name="cuit" type="text"></div>
                    <div id="tarjetaDiv"><strong>Tarjeta:</strong><input id="tarjeta" class="modal-body" name="tarjeta" type="text"></div>
                    <div id="pagosDiv"><strong>Cantidad de pagos:</strong><input id="pagos" class="modal-body" name="pagos" type="number" min="1" value="1"></div>
                    <div id="cuponDiv"><strong>Nro de cupon de operacion:</strong><input id="cupon" class="modal-body" name="cupon" type="text"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"  id="Cerrar" data-dismiss="modal">Cancelar</button>
                        <input type="submit" value="Aceptar" action="" class="submitAll" id="btnVenta">
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

        $("#pago").on('shown.bs.modal', function (e) {
            var formaPago=parseInt($("#formaPago").val());
            if(formaPago==4)
            {
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
            }

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
            $("#btnVenta").prop("disabled", false)
            $("#selectClienteDiv").attr("hidden", true);
            $("#selectCliente").attr("disabled", true);
            $("#margenDiv").attr("hidden", true);
            $("#balanceDiv").attr("hidden", true);
            $("#balancePostDiv").attr("hidden", true);
            $("#cliente").attr("disabled", false);
            $("#clienteDiv").attr("hidden", false);
            $("#domicilio").attr("disabled", false);
            $("#domicilioDiv").attr("hidden", false);
            $("#cuit").attr("disabled", false);
            $("#cuitDiv").attr("hidden", false);

            if(formaPago==1)
            {
                //$("#btnModalPago").attr("data-target", "#pagoEfectivo");
                $("#modalPagoTitle").text("Pago en Efectivo");
                $("#pagos").attr("disabled", true);
                $("#pagosDiv").attr("hidden", true);
                $("#tarjeta").attr("disabled", true);
                $("#tarjetaDiv").attr("hidden", true);
                $("#cupon").attr("disabled", true);
                $("#cuponDiv").attr("hidden", true);
            }
            else if(formaPago==2)
            {
                $("#modalPagoTitle").text("Pago con Tarjeta de Debito");
                $("#pagos").attr("disabled", true);
                $("#pagosDiv").attr("hidden", true);
                $("#tarjeta").attr("disabled", false);
                $("#tarjetaDiv").attr("hidden", false);
                $("#cupon").attr("disabled", false);
                $("#cuponDiv").attr("hidden", false);

            }
            else if(formaPago==3)
            {
                $("#modalPagoTitle").text("Pago con Tarjeta de Credito");
                $("#pagos").attr("disabled", false);
                $("#pagosDiv").attr("hidden", false);
                $("#tarjeta").attr("disabled", false);
                $("#tarjetaDiv").attr("hidden", false);
                $("#cupon").attr("disabled", false);
                $("#cuponDiv").attr("hidden", false);
            }
            else if(formaPago==4)
            {
                $("#modalPagoTitle").text("Pago con Tarjeta de Credito");
                $("#selectClienteDiv").attr("hidden", false);
                $("#selectCliente").attr("disabled", false);
                $("#margenDiv").attr("hidden", false);
                $("#balanceDiv").attr("hidden", false);
                $("#balancePostDiv").attr("hidden", false);
                $("#pagos").attr("disabled", true);
                $("#pagosDiv").attr("hidden", true);
                $("#tarjeta").attr("disabled", true);
                $("#tarjetaDiv").attr("hidden", true);
                $("#cupon").attr("disabled", true);
                $("#cuponDiv").attr("hidden", true);
                $("#cliente").attr("disabled", true);
                $("#clienteDiv").attr("hidden", true);
                $("#domicilio").attr("disabled", true);
                $("#domicilioDiv").attr("hidden", true);
                $("#cuit").attr("disabled", true);
                $("#cuitDiv").attr("hidden", true);
            }
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
        $("#btnVenta").prop("disabled", false);
        $("#lblMargen").text(response[0].margen);
        $("#lblBalance").text(response[0].balance);
        var balanceLuego=response[0].balance-totalVenta;
        if(balanceLuego<0)
        {
            if(Math.abs(balanceLuego)>response[0].margen)
            {
                color="Red";
                $("#btnVenta").prop("disabled", true);
            }
        }
        $("#lblBalancePost").css("color", color).text(balanceLuego);
    }



</script>


</body>
</html>
