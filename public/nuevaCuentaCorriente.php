<?php
use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';

include 'scripts/db.inc.php';

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

$nombre=$viva->select("Cliente",["Nombre","idCliente"],["idPersona"=>$_GET["id"]]);
//dump($nombre);


if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    //dump($_POST);

    date_default_timezone_set('America/Argentina/Buenos_Aires');

    $cuenta->cliente=$nombre[0]["idCliente"];
    $cuenta->tipo=$_POST["tipo"];
    $cuenta->fechaApertura=date("Y-m-d");
    $cuenta->margen=$_POST["margen"];

    $resultadoValidacion = $cuenta->validarCuenta();

    if (!$resultadoValidacion->isValid())
    {
        dump($resultadoValidacion->getMessages());
    }
    else
    {
        //dump($resultadoValidacion->getValues());
        $id=$db->insertar('Cuenta_corriente', $resultadoValidacion->getValues());
        $viva->update("Cliente",["Cuenta_corriente"=>"Si"],["idCliente"=>$nombre[0]["idCliente"]]);
        header("Location: gestionClientes.php");
    }
}

?>

<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <!-- Place favicon.ico in the root directory -->

    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Add your site or application content here -->
<h1>Abrir Cuenta Corriente</h1>
<form method="post">
    <div>
        <label>Nombre:<?php echo $nombre[0]["Nombre"];?> </label>
    </div>
    <div>
        <label>Tipo de cuenta:</label>
        <div class="radio">
            <label><input type="radio" name="tipo" value="Particular" checked>Particular</label>
        </div>
        <div class="radio">
            <label><input type="radio" name="tipo" value="Empresa">Empresa</label>
        </div>
    </div>

    <div>
        <label>Margen($): <input type="text" name="margen"></label>
    </div>

    <div>
        <input type="submit" value="Aceptar" action="">
    </div>
</form>

<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.12.0.min.js"><\/script>')</script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<script>
    (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
        function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
        e=o.createElement(i);r=o.getElementsByTagName(i)[0];
        e.src='https://www.google-analytics.com/analytics.js';
        r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
    ga('create','UA-XXXXX-X','auto');ga('send','pageview');
</script>
</body>
</html>

