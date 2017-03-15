<?php

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';
include("acceso.inc.php");
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

$proveedor = new Viva\Proveedor($viva);
$db = new Viva\BaseDatos($viva);

if(isset($_GET["titulo"])&& ($_GET["titulo"]==="Editar Proveedor") )
{

    $editar=$viva->select("Proveedor","*",["Representante"=>$_GET["id"]]);
    $datos=[];
    foreach($editar as $dato)
    {
        $datos=$dato;
    }
    //dump($datos);

}
else
{
    $datos=array(
        "Proveedor_nombre"=>"",
        "Cuit_cuil"=>"",
        "Sitio_web"=>"",
        "Email"=>"",
        "Telefono"=>""

    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    date_default_timezone_set('America/Argentina/Buenos_Aires');

    $proveedor->nombre=$_POST["nombre"];
    $proveedor->email=$_POST["email"];
    $proveedor->telefono=$_POST["telefono"];
    $proveedor->web=$_POST["web"];
    $proveedor->idPersona=$_GET["id"];
    $proveedor->fecha_alta=date("Y-m-d");
    $proveedor->cuit=$_POST["cuit"];


    $resultadoValidacion = $proveedor->validarProveedor();

    if (!$resultadoValidacion->isValid())
    {
        echo "<script>alert('Revisar la validez de los datos ingresados')</script>";

        //dump($resultadoValidacion->getMessages());
    }
    elseif(isset($_GET["titulo"]) && $_GET["titulo"]==="Editar Proveedor")
    {
        $id=$datos["idProveedor"];
        $db->actualizar('Proveedor',$resultadoValidacion->getValues(), "idProveedor=$id");
        header("Location: gestionProveedores.php");
    }
    else
    {
        $id=$db->insertar('Proveedor', $resultadoValidacion->getValues());
        header("Location: gestionProveedores.php");
    }
}

?>

<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Alta/Edicion de Proveedores</title>
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
<h1><?php echo $_GET["titulo"]; ?></h1>
<form method="post">
    <div>
        <label>Nombre proveedor: <input type="text" name="nombre" value="<?php echo $datos["Proveedor_nombre"];?>"></label>
    </div>
    <div>
        <label>CUIT/CUIL: <input type="text" name="cuit" value="<?php echo $datos["Cuit_cuil"];?>"></label>
    </div>
    <div>
        <label>Sitio web: <input type="text" name="web" value="<?php echo $datos["Sitio_web"];?>"></label>
    </div>
    <div>
        <label>E-Mail: <input type="text" name="email" value="<?php echo $datos["Email"];?>"></label>
    </div>
    <div>
        <label>Telefono: <input type="text" name="telefono" value="<?php echo $datos["Telefono"];?>"></label>
    </div>
    <div>
        <input type="submit" value="Aceptar">
    </div>
</form>

<a href="gestionProveedores.php"><button>Cancelar</button></a>


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
