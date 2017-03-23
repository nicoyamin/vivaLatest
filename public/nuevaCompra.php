<?php

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';

include 'scripts/db.inc.php';

$listaProvs=$viva->select("Proveedor",["idProveedor","Proveedor_nombre"],["Habilitado"=>"Si"]);

$listaProds=$viva->select("Producto",["idProducto","Nombre_producto"]);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nueva Compra</title>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.min.css">
</head>
<body>

<h1>Nueva Compra</h1>

<form action="" method="post">
    <div>
        <select class="selectUnico" name="proveedor" id="proveedor" data-placeholder="Seleccione">
            <option value="0"></option>
        <?php foreach($listaProvs as $listaProv):?>
            <option value="<?php echo $listaProv["idProveedor"];?>"><?php echo $listaProv["Proveedor_nombre"];?></option>
        <?php endforeach; ?>
        </select>
    </div>

    <div id="lista">
        <select class="selectUnico form-control required chosen-select-width producto" name="producto" id="producto" data-placeholder="Seleccione">
            <option value="0"></option>
        </select>

        <label>Cantidad:<input class="input add-new-data" type="number" name="cantidad""></label>

    </div>

    <div id="container">

    </div>

</form>





<script src="js/vendor/jquery-1.12.0.min.js"></script>
<script src="js/chosen.jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $("#proveedor").chosen({
            width: "30%"
        });



        $('#proveedor').change(function(){
            var sel_id=$('#proveedor').val();
            $('#producto').empty();
            $.get('scripts/dobleDropdown.php', {'sel_id':sel_id},function(return_data){
                $.each(return_data.data, function(key,value){

                    //$("#producto").append("<option value=" + value.idProducto +">"+value.Nombre_producto+"</option>");
                    $("#producto").append("<option value=" + value.idProducto +">"+value.Nombre_producto+"</option>").trigger('chosen:updated');
                });
            },"json");

        });


        /*jQuery(function($){
            var clone = $("table tr.data-wrapper:first").clone(true,true);
            var index=1;
            $('select.producto').chosen({width: "100%"});
            $('body').on('click', '.add-new-data', function() {
                var ParentRow = $("table tr.data-wrapper").last();

                clone.clone(true,true).insertAfter(ParentRow);
                $('tr.data-wrapper:last select').chosen();
                clone.attr("id", "producto_"+index);
                clone.attr("name", "producto_"+index);
            });
        });*/
        $(function (){


            $("#nuevaLista").bind("click", function(){



                var index=$("#container select").length+1;

                var ddl = $("#lista").clone(true,true);




                ddl.attr("id", "producto_"+index);
                ddl.attr("name", "producto_"+index);

                ddl.chosen('destroy');
                $("#container").append(ddl);
                $("#container:last select").chosen();
                $("#container").append("<br/><br/>");
            })
        });


    });


</script>


</body>
</html>