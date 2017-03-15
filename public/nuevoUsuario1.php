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
if (!userHasRole('2') && !userHasRole('3'))
{
    $error = 'Solo administradores pueden acceder a esta pagina';
    include 'accesoDenegado.php';
    exit();
}

$persona = new Viva\Persona($viva);
$db = new Viva\BaseDatos($viva);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $persona->setNombre($_POST["nombre"])
        ->setApellido($_POST["apellido"])
        ->setDni($_POST["dni"])
        ->setNacimiento($_POST["nacimiento"])
        ->setDireccion($_POST["direccion"])
        ->setCiudad($_POST["ciudad"])
        ->setProvincia($_POST["provincia"])
        ->setCodigoPostal($_POST["codigo_postal"])
        ->setEmail($_POST["email"])
        ->setTelefono($_POST["telefono"])
        ->setCelular($_POST["celular"]);

    $resultadoValidacion = $persona->validarPersona();

    if (!$resultadoValidacion->isValid())
    {
        dump($resultadoValidacion->getMessages());
    }
    else
    {
        $db->insertar('Persona', $resultadoValidacion->getValues());
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
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Add your site or application content here -->
<form method="post">
    <div>
        <label>Nombre: <input type="text" name="nombre"></label>
    </div>
    <div>
        <label>Apellido: <input type="text" name="apellido"></label>
    </div>
    <div>
        <label>Documento: <input type="number" name="dni" placeholder="Sin puntos ni guiones"></label>
    </div>
    <div>
        <label>Fecha nacimiento: <input type="date" name="nacimiento"></label>
    </div>
    <div>
        <label>Direccion: <input type="text" name="direccion"></label>
    </div>
    <div>
        <label>Ciudad: <input type="text" name="ciudad"></label>
    </div>
    <div>
        <label>Provincia: <input type="text" name="provincia"></label>
    </div>
    <div>
        <label>Codigo postal: <input type="number" name="codigo_postal"></label>
    </div>
    <div>
        <label>Email: <input type="text" name="email" placeholder="email@hotmail/yahoo/gmail.com"></label>
    </div>
    <div>
        <label>Telefono: <input type="number" name="telefono"></label>
    </div>
    <div>
        <label>Celular: <input type="number" name="celular"></label>
    </div>
    <div>
        <label>Con estos datos, crear:
            <input type="radio" name="crear" value=1>Usuario
            <input type="radio" name="crear" value=2>Cliente
            <input type="radio" name="crear" value=3>Proveedor
        </label>
    </div>
    <div>
        <input type="submit" value="Aceptar" action="">
    </div>
</form>

<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.12.0.min.js"><\/script>')</script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>

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
