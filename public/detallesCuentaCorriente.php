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

$arrayMovimientos=$viva->select("Cuenta_corriente_movimientos","*",["idCuenta_corriente"=>$_GET["id"]]);
$detallesCuenta=$viva->select("Cuenta_corriente","*",["idCuenta_corriente"=>$_GET["id"]]);


if(isset($_POST['action']) and $_POST['action']==="Realizar Cambios")
{
    $viva->update("Cuenta_corriente",["Margen"=>$_POST["margen"],"Estado"=>$_POST["estado"]],["idCuenta_corriente"=>$_GET["id"]]);
    header("Location: gestionCuentaCorriente.php");
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
    <h2>Cuenta Corriente de <?php echo $_GET["cliente"]; ?></h2>

    <div>
        <label>ID cuenta Corriente: <?php echo $detallesCuenta[0]["idCuenta_corriente"]; ?></label>
    </div>
    <div>
        <label>Tipo de cuenta: <?php echo $detallesCuenta[0]["Tipo"]; ?></label>
    </div>
    <div>
        <label>Fecha de Apertura: <?php echo $detallesCuenta[0]["Fecha_apertura"]; ?></label>
    </div>
    <div>
        <label>Fecha Ultimo Movimiento: <?php echo $detallesCuenta[0]["Fecha_ultimo_movimiento"]; ?></label>
    </div>

    <div>
        <label>Balance: $<?php echo $detallesCuenta[0]["Balance"]; ?></label>
    </div>

<form action="" method="post">
    <div>
        <label>Estado de cuenta: </label>
        <select class="selectUnico" name="estado">
            <option selected disabled><?php echo $detallesCuenta[0]["Estado"]; ?></option>
            <option value="Activa">Activa</option>
            <option value="Inactiva">Inactiva</option>
            <option value="Bloqueada">Boqueada</option>
        </select>
    </div>
    <div>
        <label> Margen de la cuenta($): <input name="margen" value="<?php echo $detallesCuenta[0]["Margen"]; ?>"></label>
    </div>

    <input type="submit" name="action" value="Realizar Cambios" onclick="return confirm('Seguro que quiere aplicar estos cambios?');">
</form>

    <h3>Historial de movimientos</h3>
    <table id="tablaMovimientos" class="table table-striped">
        <thead>
        <tr>
            <th>Movimiento Nro.</th>
            <th>Concepto</th>
            <th>Medio de pago</th>
            <th>Fecha</th>
            <th>Debe</th>
            <th>Haber</th>
            <th>Saldo</th>
            <th>Nro Documento Referencia</th>

        </tr>
        </thead>

        <tbody>
        <?php foreach($arrayMovimientos as $mov):?>
            <tr>
                    <td><?php echo $mov["idCuenta_corriente_movimientos"];?></td>
                    <td><?php echo $mov["Concepto"];?></td>
                    <td><?php echo $mov["Medio_pago"];?></td>
                    <td><?php echo $mov["Fecha"];?></td>
                    <td><?php echo $mov["Debe"];?></td>
                    <td><?php echo $mov["Haber"];?></td>
                    <td><?php echo $mov["Saldo"];?></td>
                    <td><?php echo $mov["Referencia"];?></td>
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
        var table=$('#tablaMovimientos').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });



    } );
</script>

</body>

</html>
