<?php
use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';
require_once 'acceso.inc.php';
include 'scripts/db.inc.php';

use JasperPHP\JasperPHP;

if (!userIsLoggedIn()) {
    include 'login.php';
    exit();
}
if (!userHasRole('2') && !userHasRole('3')) {
    $error = 'Solo administradores pueden acceder a esta pagina';
    include 'accesoDenegado.php';
    exit();
}
$usuario = new Viva\Usuario($viva);

//Obtener operaciones de entrada y comprobantes que las respaldan para llenar dropdown lists
$entradaCaja = $viva->select("Caja_operacion", "*", ["Tipo" => "Salida"]);
$comprobantes = $viva->select("Caja_comprobante", ["[><]Caja_operacion" => ["Respalda_operacion" => "idCaja_operacion"]],
    [
        "Caja_comprobante.idComprobante",
        "Caja_comprobante.Descripcion"
    ],
    ["Caja_operacion.Tipo" => "Salida"]);

$datosUsuario = $usuario->selectNombreUsuarioPassword($viva, $_SESSION["usuario"], $_SESSION["password"]);

//dump($_SESSION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    if(!isset($_POST["comprobante"]))
    {
        if($_POST["concepto"]==9) $_POST["comprobante"]="12";
        if($_POST["concepto"]==10) $_POST["comprobante"]="13";

        $_POST["referencia"]="Sin comprobante";
    }

    if(isset($_SESSION["idTurno"])) $idTurno=$_SESSION["idTurno"];
    else $idTurno=0;

   //dump($_POST);
    $viva->insert("Caja",[
            "idUsuario"=>$datosUsuario[0]["idUsuario"],
            "Fecha"=>date("Y-m-d H:i:s"),
            "Tipo"=>"Salida",
            "Concepto"=>$_POST["concepto"],
            "Debe"=>"0.00",
            "Haber"=>$_POST["importe"],
            "Observaciones"=>$_POST["observaciones"],
            "Referencia"=>$_POST["comprobante"],
            "idReferencia"=>$_POST["referencia"],
        "idTurno"=>$idTurno]
    );



}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salida de Caja</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
    <script src="js/vendor/jquery-1.12.0.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</head>

<body>

<?php include 'scripts/logout.inc.php'; ?>

<div class="container">
    <h2>Registrar salida de caja</h2>


    <form action="" method="post" id="formSalida">


        <div><label>Concepto:</label>
            <select class="selectUnico" name="concepto" id="concepto" data-placeholder="Seleccione" required>
                <option value="0">Seleccione Concepto</option>
                <?php foreach ($entradaCaja as $entrada): ?>
                    <option
                        value="<?php echo $entrada["idCaja_operacion"]; ?>"><?php echo $entrada["Descripcion"]; ?></option>
                <?php endforeach; ?>
            </select></div>

        <div>
            <label>Importe: $ <input type="number" step="0.01" name="importe" id="importe" min="0" required></label>
        </div>

        <div><label>Comprobante:</label>
            <select class="selectUnico" name="comprobante" id="comprobante" data-placeholder="Seleccione" required>
                <option value="0">Comprobante de operacion:</option>
                <?php foreach ($comprobantes as $comp): ?>
                    <option
                        value="<?php echo $comp["idComprobante"]; ?>"><?php echo $comp["Descripcion"]; ?></option>
                <?php endforeach; ?>
            </select></div>

        <div>
            <label>Numero de referencia del comprobante: <input type="number" name="referencia" id="referencia"
                                                                min="1" required></label>
        </div>

        <div>
            <label>Observaciones: </label>

            <div><textarea name="observaciones" id="observaciones"></textarea></div>

        </div>

        <input type="submit" value="Aceptar">
    </form>


<script type="text/javascript">
    $(document).ready(function () {


        $('#concepto').select2();
        $('#comprobante').select2();

        $("#formSalida").on("submit", function(){

            var concepto = $('#concepto').val();
            var comprobante = $('#comprobante').val();

            var priv = <?php echo json_encode($datosUsuario[0]["Privilegio"]) ?>;

            if(concepto==0)
            {
                alert("Debe seleccionar un concepto");
                return false;
            }

            if(comprobante=="0" && concepto!="9" && concepto != "10")
            {
                alert("Debe seleccionar un comprobante que respalde la operacion");
                return false;
            }

            if(concepto==9 && priv !=3 )
            {
                alert("Solo usuarios nivel 3 pueden realizar esta operacion");
                return false;
            }

        });

        //Funcion que permite generar comprobante de acuerdo al concepto elegido
        $('#concepto').change(function () {
            var concepto = $('#concepto').val();

            //Si el concepto es alivio de caja o cobranza de cuenta, se puede generar comprobante. Se abrira el modal correspondiente para su generacion
            if (concepto == 9 || concepto==10)
            {
                $('#comprobante').prop('disabled',true);
                $('#referencia').prop('disabled',true);

            }
            else
            {
                $('#comprobante').prop('disabled',false);
                $('#referencia').prop('disabled',false);
            }



        });



    })

</script>

</body>

</html>
