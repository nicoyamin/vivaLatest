<?php

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';
require_once  'acceso.inc.php';
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

$usuario = new Viva\Usuario($viva);
$db = new Viva\BaseDatos($viva);

if(isset($_GET["titulo"])&& ($_GET["titulo"]==="Editar Usuario"))
{

    $editar=$viva->select("Usuario","*",["idPersona"=>$_GET["id"]]);
    $datos=[];
    foreach($editar as $dato)
    {
        $datos=$dato;
    }
}
else
{
    $datos=array(
        "Nombre_usuario"=>"",
        "Password"=>"",
        "Privilegio"=>""
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $usuario->setUsuario($_POST["usuario"])
        ->setPassword($_POST["password"])
        ->setPasswordConfirmar($_POST["confirmar"])
        ->setPrivilegio($_POST["privilegio"])
        ->setIdpersona($_GET["id"])
        ->setFechaCreacion();


    $resultadoValidacion = $usuario->validarUsuario($usuario->getPasswordConfirmar());

    if (!$resultadoValidacion->isValid()) {
        echo "<script>alert('Revisar la validez de los datos ingresados')</script>";
        //dump($resultadoValidacion->getMessages());
    }
    elseif($_GET["titulo"]==="Editar Usuario")
    {

        $id=$datos["idUsuario"];
        $db->actualizar('Usuario',$resultadoValidacion->getValues(), "idUsuario=$id");
        echo "<script>alert('El usuario ha sido actualizado')</script>";
        header("Location: gestionUsuarios.php");

    }
    else
    {
        $id = $db->insertar('Usuario', $resultadoValidacion->getValues());
        $viva->update("Usuario", ["Habilitado"=>"Si"],["idUsuario"=>$id]);
        echo "<script>alert('El usuario se ha creado con exito')</script>";
        header("Location: gestionUsuarios.php");
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
<h1><?php echo $_GET["titulo"]; ?></h1>
<form method="post">
    <div>
        <label>Nombre usuario: <input type="text" name="usuario" value="<?php echo $datos["Nombre_usuario"];?>"></label>
    </div>
    <div>
        <label>Contraseña: <input type="password" name="password"></label>
    </div>
    <div>
        <label>Confirmar contraseña: <input type="password" name="confirmar"></label>
    </div>
    <div>
        <label>Privilegio:
            <input type="radio" name="privilegio" value="1" checked>Operador
            <input type="radio" name="privilegio" value="2">Administrador
        </label>
    </div>
    <div>
        <input type="submit" value="Aceptar" action="">
    </div>
</form>

<a href="gestionUsuarios.php"><button>Cancelar</button></a>

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

