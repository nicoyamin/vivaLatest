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
if (!userHasRole('2') && !userHasRole('3'))
{
    $error = 'Solo administradores pueden acceder a esta pagina';
    include 'accesoDenegado.php';
    exit();
}


$producto = new Viva\Producto($viva);



$listaProvs=$viva->select("Proveedor",["idProveedor","Proveedor_nombre"],["Habilitado"=>"Si"]);

//$listaProds=$viva->select("Producto",["idProducto","Nombre_producto"]);

$listaProds=$producto->selectPorStock($viva);

dump($listaProds);




if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $nombreProv=$viva->select("Proveedor",["Proveedor_nombre"],["idProveedor"=>$_POST['proveedor']]);

    $nombreProv=$nombreProv[0]["Proveedor_nombre"];


    $last_insert_id=$viva->insert("Compra",[
        "Fecha"=>date("Y-m-d"),
        "Condiciones_pago"=>$_POST['pago'],
        "Lugar_entrega"=>$_POST['lugarE'],
        "Fecha_entrega"=>$_POST['fechaE'],
        "Enviar_por"=>$_POST['envio'],
        "Estado"=>$_POST['optradio'],
        "idProveedor"=>$_POST['proveedor']
    ]);

    $cantArray=(count($_POST)-6)/2;
    for($x=0;$x<$cantArray;$x++)
    {
        $arrayCompra=array_slice($_POST, 1,2);
        $_POST = array_diff_assoc($_POST, $arrayCompra);
        $arrayCompra["idCompra"]=$last_insert_id;
        $keys=array_keys($arrayCompra);
        $viva->insert("Compra_Producto",[
            "idCompra"=>$arrayCompra["idCompra"],
            "idProducto"=>$arrayCompra[$keys[0]],
            "Cantidad"=>$arrayCompra[$keys[1]],
            "Cantidad_pendiente"=>$arrayCompra[$keys[1]]
        ]);
    }

    if($_POST["fechaE"]=="")
    {
        $fechaE="";
    }
    else
    {
        $fechaE=date("d/m/Y", strtotime($_POST["fechaE"]));
    }

    $jasper = new JasperPHP;

    $database = [
        'driver' => 'mysql',
        'username' => 'homestead',
        'password'=> 'secret',
        'database'=> 'VIVA',
        'host' => '127.0.0.1',
        'port' => '3306'
    ];


    if($_POST["optradio"]==3)
    {
        $nroOrden=$viva->select("Orden_de_compra",["numero"],["numero[>=]"=>1, "ORDER"=>["numero"=>"DESC"],"LIMIT"=>1]);
        $nroOrden=$nroOrden[0]["numero"]+1;
        $viva->insert("Orden_de_compra",[
            "idCompra"=>$last_insert_id,
            "numero"=>$nroOrden,
            "Fecha"=>date("Y-m-d"),
            "Estado"=>"Activa"
        ]);

        $nombre="Orden_de_compra_Numero_".$nroOrden;

        $jasper->process(
        // Ruta y nombre de archivo de entrada del reporte
            'reports/ordenCompra.jasper',
            'reports/'.$nombre, // Ruta y nombre de archivo de salida del reporte (sin extensión)
            array('pdf'), // Formatos de salida del reporte
            array('proveedor' => $nombreProv,
                'pago'=>$_POST["pago"],
                'lugar'=>$_POST["lugarE"],
                'fechaEntrega'=>$fechaE,
                'envio'=>$_POST["envio"],
                'fecha'=>date("d/m/Y"),
                'idCompra'=>$last_insert_id,
                'nroOrden'=>$nroOrden
            ) // Parámetros del reporte
            ,$database
        )->execute();
    }

    if($_POST["optradio"]==1)
    {

        $nombre="Cotizacion";

        $jasper->process(
        // Ruta y nombre de archivo de entrada del reporte
            'reports/Cotizacion.jasper',
            'reports/'.$nombre, // Ruta y nombre de archivo de salida del reporte (sin extensión)
            array('pdf'), // Formatos de salida del reporte
            array('proveedor' => $nombreProv,
                'pago'=>$_POST["pago"],
                'lugar'=>$_POST["lugarE"],
                'fechaEntrega'=>$fechaE,
                'envio'=>$_POST["envio"],
                'fecha'=>date("d/m/Y"),
                'idCompra'=>$last_insert_id
            ) // Parámetros del reporte
            ,$database
        )->execute();
    }




    //echo $output;

    //exec($jasper->output().' 2>&1', $output);
    //dump($output);
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
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>


</head>

<body>
    <?php include 'scripts/logout.inc.php'; ?>

    <h1>Nueva Compra Generada por Stock</h1>

    <form action="" method="post">

        <table  id="tablaProductos" class="table table-striped">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Stock Minimo</th>
                <th>Stock Actual</th>
                <th>Cantidad</th>
                <th>Quitar Fila</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach($listaProds as $prod):?>
            <tr id="filaP">
                <td><?php echo $prod["Nombre_producto"]." ".$prod["Cantidad_unitaria_producto"]." ".$prod["Unidad_producto"]?></td>
                <td><?php echo $prod["Stock_minimo_producto"];?></td>
                <td><?php echo $prod["Existencia_producto"];?></td>


                <td>
                    <input id="cantidad" class="input add-new-data" type="number" name="cantidad">
                </td>

                <td>
                    <button id="quitarFila" type="button">Quitar fila</button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </form>

<script>

    $(document).ready(function(){


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



        $('#proveedor').select2({
            dropdownAutoWidth: 'true'
        });

        //$("#filaP").children("select").select2();
        $("#producto").select2();

        $('#proveedor').change(function(){

            var sel_id=$('#proveedor').val();
            $('#producto').empty();
            $.get('scripts/dobleDropdown.php', {'sel_id':sel_id},function(return_data){
                $.each(return_data.data, function(key,value){

                    //$("#producto").append("<option value=" + value.idProducto +">"+value.Nombre_producto+"</option>");
                    $("#producto").append("<option value=" + value.idProducto +">"+value.Nombre_producto+" "+value.Cantidad_unitaria_producto+" "+value.Unidad_producto+"</option>");
                });
            },"json");

            //var nroFilas=table.column(0).data().length;

            for(nroFilas=table.column(0).data().length; nroFilas=nroFilas-1; nroFilas==2)
            {
                table
                    .row( nroFilas)
                    .remove()
                    .draw();

            }


            //$("#lista").children("select").select2();
        });


        $("#btnNuevaFila").click(function(){


            var flag=0;

            $('input[id=cantidad]').each(function(){

                if($(this).val()=="" || $(this).val()<=0)//Chequea que cantidad no este vacio
                {

                    flag=flag+1;
                }
            });



            if(flag>0)
            {
                alert("Debe introducir una cantidad valida antes de crear una nueva fila");
            }
            else
            {
                $("#producto").select2("destroy").end();
                var ddl = $("#filaP").clone(true);

                //ddl.attr("id", "producto_"+table.column(0).data().length);
                //ddl.attr("name", "producto_"+table.column(0).data().length);
                ddl.children().eq(0).children().eq(0).attr("name","producto_"+table.column(0).data().length);
                //$('input[name=cantidad]').last().val(""); //Valor Vacio para cantidad
                ddl.children().eq(1).children().eq(0).attr("name","cantidad_"+table.column(0).data().length);
                ddl.children().eq(1).children().eq(0).attr("value","");
                table.row.add(ddl).draw();
                $(".selectUnico").select2();

                $('input[id=cantidad]').last().val(""); //Valor Vacio para cantidad

            }
        });

        $("#quitarFila").click(function(){

            if(table.column(0).data().length==1)
            {
                alert("La tabla no puede quedar vacia");
            }
            else{
                table.row( $(this).parents('tr') ).remove().draw();
            }
        });

    });
</script>

</body>

</html>
