<?php
require_once '../vendor/autoload.php';

include 'scripts/db.inc.php';

include("acceso.inc.php");

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

$cliente = new Viva\Cliente($viva);
$db = new Viva\BaseDatos($viva);

$arrayClientes=$cliente->selectClientes($viva);

$titulo="Nuevo Cliente";

if (isset($_POST['action']) and $_POST['action'] == 'Habilitar/Deshabilitar')
{
    $habilitado=$viva->select("Cliente",["Habilitado"],["idCliente"=>$_POST["idCliente"]]);
    //dump($habilitado);
    if($habilitado[0]["Habilitado"]=="Si"){
        $viva->update("Cliente",["Habilitado"=>"No"],["idCliente"=>$_POST["idCliente"]]);
    }
    else{
        $viva->update("Cliente",["Habilitado"=>"Si"],["idCliente"=>$_POST["idCliente"]]);
    }

    $arrayClientes=$cliente->selectClientes($viva);


    //$viva->delete("Persona",["idPersona"=>$_POST["id"]]);
    //exit();
    //echo "hola";
}

if (isset($_POST['action']) and $_POST['action'] == 'Edit')
{
    $id=$_POST["id"];
    header("Location: nuevoUsuario.php?titulo=Editar Cliente&id=$id");
    exit();
}

if(isset($_POST['action']) and $_POST['action']=="Abrir Cuenta Corriente")
{
    $id=$_POST["id"];
    header("Location: nuevaCuentaCorriente.php?id=$id");
    exit();
}

if(isset($_POST['action']) and $_POST['action']=="Administrar Cuenta Corriente")
{
    $id=$viva->select("Cuenta_corriente","idCuenta_corriente",["idCliente"=>$_POST["idCliente"]]);
    $id=$id[0]["idCuentaCorriente"];
    $cliente=$_POST["nombre"];
    header("Location: detallesCuentaCorriente.php?id=$id&cliente=$cliente");
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
    <h2>Clientes</h2>
    <button><a href="nuevoUsuario.php?titulo=Nuevo Cliente">Nuevo Cliente</a></button>
    <table id="tablaClientes" class="table table-striped">
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
            <th>Cuenta Corriente</th>
            <th>Editar</th>
            <th>Habilitar/Deshabilitar</th>


        </tr>
        </thead>

        <tbody>
        <?php foreach($arrayClientes as $arrayCliente):?>
            <tr>
                <form action="" method="post">
                    <input type="hidden" name="id" value="<?php echo $arrayCliente["idPersona"]; ?>">
                    <input type="hidden" name="idCliente" value="<?php echo $arrayCliente["idCliente"]; ?>">
                    <input type="hidden" name="nombre" value="<?php echo $arrayCliente["Nombre"]; ?>">
                    <td><?php echo $arrayCliente["Nombre"];?></td>
                    <td><?php echo $arrayCliente["Cuit_cuil"];?></td>
                    <td><?php echo $arrayCliente["Telefono"];?></td>
                    <td><?php echo $arrayCliente["Sitio_web"];?></td>
                    <td><?php echo $arrayCliente["Email"];?></td>
                    <td><?php echo $arrayCliente["Direccion"];?></td>
                    <td><?php echo $arrayCliente["nombreRep"];?></td>
                    <td><?php echo $arrayCliente["Apellido"];?></td>
                    <td><?php echo $arrayCliente["Fecha_alta"];?></td>
                    <td><?php echo $arrayCliente["Habilitado"];?></td>
                    <?php if($arrayCliente["Cuenta_corriente"]=="No"):  ?>
                        <td><input type="submit" name="action" value="Abrir Cuenta Corriente"></td>
                    <?php else: ?>
                        <td><input type="submit" name="action" value="Administrar Cuenta Corriente"></td>
                    <?php endif?>
                    <td><input type="submit" name="action" value="Edit"></td>
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
        var table=$('#tablaClientes').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });



    } );
</script>

</body>

</html>
