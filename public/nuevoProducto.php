<?php

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';
require_once 'acceso.inc.php';
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

$producto = new Viva\Producto($viva);
$db = new Viva\BaseDatos($viva);

$listaProvs= $viva->select("Proveedor",["idProveedor","Proveedor_nombre"],["Habilitado"=>"Si"]);
$listaCats= $viva->select("Producto_categoria",["idCategoria","Categoria_nombre"]);
//dump($listaProvs);

if(isset($_GET["titulo"])&& ($_GET["titulo"]==="Editar Producto") )
{

    $editar=$viva->select("Producto","*",["idProducto"=>$_GET["id"]]);
    $datos=[];
    foreach($editar as $dato)
    {
        $datos=$dato;
    }

}
else
{
    $datos=array(
        "idCategoria"=>'',
        "Nombre_producto"=>"",
        "Descripcion_producto"=>"",
        "Cantidad_unitaria_producto"=>"",
        "Unidad_producto"=>"",
        "Precio_unitario_producto"=>"",
        "Perecedero"=>"Si",
        "Codigo_barras_producto"=>"",
        "idProveedor"=>"",
        "Existencia_producto"=>"",
        "Stock_minimo_producto"=>""
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //dump($_POST);

    $producto->setNombre($_POST["nombre"])
        ->setCategoria($_POST["categoria"])
        ->setProveedor($_POST["proveedor"])
        ->setDescripcion($_POST["descripcion"])
        ->setCantidad($_POST["cantidad"])
        ->setUnidad($_POST["unidad"])
        ->setPrecio($_POST["precio"])
        ->setPerecedero($_POST["perecedero"])
        ->setCodigo_barras($_POST["codigo_barras"])
        ->setExistencias($_POST["existencias"])
        ->setStockMinimo($_POST["stockMinimo"]);

    $resultadoValidacion = $producto->validarProducto();

    if (!$resultadoValidacion->isValid())
    {
        dump($resultadoValidacion->getMessages());
    }

    elseif($_GET["titulo"]==="Editar Producto")
    {
        $id=$datos["idProducto"];
        $db->actualizar('Producto',$resultadoValidacion->getValues(), "idProducto=$id");
        header("Location: gestionProductos.php");

    }
    else
    {
        $db->insertar('Producto', $resultadoValidacion->getValues());
        header("Location: gestionProductos.php");

    }
}

?>

<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Alta de Productos</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.min.css">
    <link rel="stylesheet" type="text/css" href="css/vivaStyle.css" media="screen" />
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="js/vendor/jquery-1.12.0.min.js"></script>
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Add your site or application content here -->

<?php include 'scripts/logout.inc.php'; ?>
<h1><?php echo $_GET["titulo"]; ?></h1>
<form method="post" class="form-style-1">
    <div>
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $datos["Nombre_producto"];?>">
    </div>
    <div>
        <label>Categoria:</label>
        <select class="selectUnico" name="categoria" data-placeholder="Categoria">
            <option value="0"></option>
            <?php foreach($listaCats as $listaCat):?>
                <option
                    <?php if($listaCat['idCategoria'] == $datos["idCategoria"]) { ?> selected="selected" <?php } ?>
                    value="<?php echo $listaCat["idCategoria"];?>"><?php echo $listaCat["Categoria_nombre"];?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" id="btnNuevaCategoria" data-toggle="modal" data-target="#modalNuevaCategoria">Nueva Categoria</button>
    </div>
    <div>
        <label>Proveedor:</label>
        <select class="selectUnico" name="proveedor" data-placeholder="Proveedor">
            <option value="0"></option>
            <?php foreach($listaProvs as $listaProv):?>
                <option
                    <?php if($listaProv['idProveedor'] == $datos["idProveedor"]) { ?>
                        selected
                    <?php }; ?>
                    value="<?php echo $listaProv["idProveedor"];?>"><?php echo $listaProv["Proveedor_nombre"];?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>Descripcion: </label>
        <input type="text" name="descripcion" value="<?php echo $datos["Descripcion_producto"];?>">
    </div>
    <div>
        <label>Cantidad unitaria:</label>
        <input type="text" name="cantidad" value="<?php echo $datos["Cantidad_unitaria_producto"];?>">
    </div>
    <div>
        <label>Unidad: </label>
        <input type="text" name="unidad" value="<?php echo $datos["Unidad_producto"];?>">
    </div>
    <div>
        <label>Precio unitario: </label>
        <input type="text" name="precio" value="<?php echo $datos["Precio_unitario_producto"];?>">
    </div>
    <div>
        <label>Es perecedero?:</label>
        <input type="radio" name="perecedero" value="Si" checked>Si
        <input type="radio" name="perecedero" value="No">No
    </div>
    <div>
        <label>Codigo de Barras:</label>
        <input type="text" name="codigo_barras" value="<?php echo $datos["Codigo_barras_producto"];?>">
    </div>

    <div>
        <label>Existencias (Cantidad Actual):</label>
        <input type="text" name="existencias" value="<?php echo $datos["Existencia_producto"];?>">
    </div>
    <div>
        <label>Stock Minimo:</label>
        <input type="text" name="stockMinimo" value="<?php echo $datos["Stock_minimo_producto"];?>">
    </div>

    <div>
        <input type="submit" value="Aceptar">
    </div>
</form>

    <div class="modal fade" id="modalNuevaCategoria" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> X </button>
                    <h4 class="modal-title" id="modalTitle">Agregar Nueva Categoria</h4>
                    <p hidden id="modalId" class="modal-body"> </p>
                    <h4><strong>Nombre Categoria:</strong><input id="modalNombre" class="modal-body"></h4>
                    <h4><strong>Descripcion:</strong><input id="modalDescripcion" class="modal-body"></h4>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"  id="Cerrar" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="aceptar">Aceptar</button>
                </div>
            </div>
        </div>

    </div>



<script src="js/plugins.js"></script>
<script src="js/main.js"></script>
<script src="js/bootstrap.min.js"></script>

<script src="js/chosen.jquery.min.js"></script>

<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<script>

    $(document).ready(function(){
        //Chosen
        
        $(".selectUnico").chosen({
            width: "100%"
        });
    $("#aceptar").click(function(){
                var nombre = $('input[id=modalNombre]').val();
                var descripcion = $('input[id=modalDescripcion]').val();
                //alert(nombre,descripcion);

                $.ajax
                ({
                    type: 'POST',
                    url: '/scripts/nuevaCategoria.php',
                    data: {nombre: nombre, descripcion: descripcion},
                    success: function (html) 
                    {
                        alert("La categoria fue agregada");
                        $("#modalNuevaCategoria .close").click();
                        location.reload();
                    }
                });
        });
    });
</script>
</body>
</html>
