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

$cuenta = new Viva\CuentaCorriente($viva);
$db = new Viva\BaseDatos($viva);

$arrayCuentas=$cuenta->selectCuentas($viva);



if(isset($_POST['action']) and $_POST['action']=="Administrar Cuenta")
{
    $id=$_POST["idCuenta"];
    $cliente=$_POST["cliente"];
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
    <h2>Cuentas Corrientes</h2>
    <table id="tablaCuentas" class="table table-striped">
        <thead>
        <tr>
            <th>Cliente</th>
            <th>Id Cuenta</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Fecha Apertura</th>
            <th>Margen</th>
            <th>Fecha Ultimo Movimiento</th>
            <th>Balance</th>
            <th>Administrar</th>

        </tr>
        </thead>

        <tbody>
        <?php foreach($arrayCuentas as $arrayCuenta):?>
            <tr>
                <form action="" method="post">
                    <input type="hidden" name="idCuenta" value="<?php echo $arrayCuenta["idCuenta_corriente"]; ?>">
                    <input type="hidden" name="cliente" value="<?php echo $arrayCuenta["Nombre"]; ?>">
                    <td><?php echo $arrayCuenta["Nombre"];?></td>
                    <td><?php echo $arrayCuenta["idCuenta_corriente"];?></td>
                    <td><?php echo $arrayCuenta["Tipo"];?></td>
                    <td><?php echo $arrayCuenta["Estado"];?></td>
                    <td><?php echo $arrayCuenta["Fecha_apertura"];?></td>
                    <td><?php echo $arrayCuenta["Margen"];?></td>
                    <td><?php echo $arrayCuenta["Fecha_ultimo_movimiento"];?></td>
                    <td><?php echo $arrayCuenta["Balance"];?></td>
                    <td><input type="submit" name="action" value="Administrar Cuenta"></td>
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
        var table=$('#tablaCuentas').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });



    } );
</script>

</body>

</html>

