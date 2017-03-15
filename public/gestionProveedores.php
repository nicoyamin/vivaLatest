<?php
require_once '../vendor/autoload.php';

include 'scripts/db.inc.php';

include("acceso.inc.php");

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


$proveedor = new Viva\Proveedor($viva);
$db = new Viva\BaseDatos($viva);

$arrayProveedores=$proveedor->selectProveedores($viva);

$titulo="Nuevo Proveedor";

if (isset($_POST['action']) and $_POST['action'] == 'Habilitar/Deshabilitar')
{
    $habilitado=$viva->select("Proveedor",["Habilitado"],["idProveedor"=>$_POST["idProveedor"]]);
    //dump($habilitado);
    if($habilitado[0]["Habilitado"]=="Si"){
        $viva->update("Proveedor",["Habilitado"=>"No"],["idProveedor"=>$_POST["idProveedor"]]);
    }
    else{
        $viva->update("Proveedor",["Habilitado"=>"Si"],["idProveedor"=>$_POST["idProveedor"]]);
    }
    
    $arrayProveedores=$proveedor->selectProveedores($viva);


    //$viva->delete("Persona",["idPersona"=>$_POST["id"]]);
    //exit();
    //echo "hola";
}

if (isset($_POST['action']) and $_POST['action'] == 'Editar')
{
    $id=$_POST["id"];
    header("Location: nuevoUsuario.php?titulo=Editar Proveedor&id=$id");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <title>Gestion de Proveedores</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
</head>
<body>

<?php include 'scripts/logout.inc.php'; ?>
<div class="container">
    <h2>Proveedores</h2>
    <button><a href="nuevoUsuario.php?titulo=Nuevo Proveedor">Nuevo Proveedor</a></button>
    <table id="tablaProveedores" class="table table-striped">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>CUIT/CUIL</th>
            <th>Telefono</th>
            <th>Sitio web</th>
            <th>Email</th>
            <th>Direccion</th>
            <th>Nombre Representante</th>
            <th>Apellido Representante</th>
            <th>Fecha de Creacion</th>
            <th>Habilitado</th>
            <th>Editar</th>
            <th>Habilitar/Deshabilitar</th>


        </tr>
        </thead>

        <tbody>
        <?php foreach($arrayProveedores as $arrayProveedor):?>
            <tr>
                <form action="" method="post">
                    <input type="hidden" name="id" value="<?php echo $arrayProveedor["Representante"]; ?>">
                    <input type="hidden" name="idProveedor" value="<?php echo $arrayProveedor["idProveedor"]; ?>">
                    <td><?php echo $arrayProveedor["Proveedor_nombre"];?></td>
                    <td><?php echo $arrayProveedor["Cuit_cuil"];?></td>
                    <td><?php echo $arrayProveedor["Telefono"];?></td>
                    <td><?php echo $arrayProveedor["Sitio_web"];?></td>
                    <td><?php echo $arrayProveedor["Email"];?></td>
                    <td><?php echo $arrayProveedor["Direccion"];?></td>
                    <td><?php echo $arrayProveedor["Nombre"];?></td>
                    <td><?php echo $arrayProveedor["Apellido"];?></td>
                    <td><?php echo $arrayProveedor["Fecha_alta"];?></td>
                    <td><?php echo $arrayProveedor["Habilitado"];?></td>
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
    var table=$('#tablaProveedores').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });



} );
</script>

</body>

</html>
