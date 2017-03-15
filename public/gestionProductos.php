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


$producto = new Viva\Producto($viva);
$db = new Viva\BaseDatos($viva);

$arrayProductos=$producto->selectProductos($viva);

$titulo="Nuevo Producto";

if (isset($_POST['action']) and $_POST['action'] == 'Habilitar/Deshabilitar')
{

    $habilitado=$viva->select("Producto",["Habilitado"],["idProducto"=>$_POST["id"]]);
    if($habilitado[0]["Habilitado"]=="Si"){
        $viva->update("Producto",["Habilitado"=>"No"],["idProducto"=>$_POST["id"]]);
    }
    else{
        $viva->update("Producto",["Habilitado"=>"Si"],["idProducto"=>$_POST["id"]]);
    }
    
    $arrayProductos=$producto->selectProductos($viva);//Volver a cargar el array teniendo en cuenta el cambio
}

if (isset($_POST['action']) and $_POST['action'] == 'Editar')
{
    $id=$_POST["id"];
    header("Location: nuevoProducto.php?titulo=Editar Producto&id=$id");
    exit();
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
    <h2>Producto</h2>
    <button><a href="nuevoProducto.php?titulo=Nuevo Producto">Nuevo Producto</a></button>
    <table id="tablaProductos" class="table table-striped">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripcion</th>
            <th>Categoria</th>
            <th>Cantidad</th>
            <th>Unidad</th>
            <th>Precio Unitario($)</th>
            <th>Perecedero</th>
            <th>Codigo de barras</th>
            <th>Stock Actual</th>
            <th>Stock Minimo</th>
            <th>Proveedor</th>
            <th>Habilitado</th>
            <th>Editar</th>
            <th>Habilitar/Deshabilitar</th>


        </tr>
        </thead>

        <tbody>
        <?php foreach($arrayProductos as $arrayProducto):?>
            <tr>
                <form action="" method="post">
                    <input type="hidden" name="id" value="<?php echo $arrayProducto["idProducto"]; ?>">
                    <td><?php echo $arrayProducto["Nombre_producto"];?></td>
                    <td><?php echo $arrayProducto["Descripcion_producto"];?></td>
                    <td><?php echo $arrayProducto["Categoria_nombre"];?></td>
                    <td><?php echo $arrayProducto["Cantidad_unitaria_producto"];?></td>
                    <td><?php echo $arrayProducto["Unidad_producto"];?></td>
                    <td><?php echo $arrayProducto["Precio_unitario_producto"];?></td>
                    <td><?php echo $arrayProducto["Perecedero"];?></td>
                    <td><?php echo $arrayProducto["Codigo_barras_producto"];?></td>
                    <td><?php echo $arrayProducto["Existencia_producto"];?></td>
                    <td><?php echo $arrayProducto["Stock_minimo_producto"];?></td>
                    <td><?php echo $arrayProducto["Proveedor_nombre"];?></td>
                    <td><?php echo $arrayProducto["Habilitado"];?></td>
                    <td><input type="submit" name="action" value="Editar"></td>
                    <td><input type="submit" name="action" value="Habilitar/Deshabilitar"></td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>


</div>



<script src="js/vendor/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js"></script>

<script type="text/javascript">
$(document).ready( function () {
    var table=$('#tablaProductos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });



} );
</script>

</body>

</html>

