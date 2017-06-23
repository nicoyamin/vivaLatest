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

$persona = new Viva\Persona($viva);
$db = new Viva\BaseDatos($viva);

if(isset($_GET["titulo"])&& (($_GET["titulo"]==="Editar Usuario")||($_GET["titulo"]==="Editar Proveedor")||($_GET["titulo"]==="Editar Cliente")) )
{

    $editar=$viva->select("Persona","*",["idPersona"=>$_GET["id"]]);
    $datos=[];
    foreach($editar as $dato)
    {
        $datos=$dato;
    }
}
else
{
    $datos=array(
        "Nombre"=>"",
        "Apellido"=>"",
        "Documento"=>"",
        "Fecha_nacimiento"=>"",
        "Direccion"=>"",
        "Ciudad"=>"",
        "Provincia"=>"",
        "Codigo_postal"=>"",
        "Email"=>"",
        "Telefono"=>"",
        "Celular"=>""
     );
}


if ($_SERVER['REQUEST_METHOD'] === 'POST')
{


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
        echo "<script>alert('Revisar la validez de los datos ingresados')</script>";
    }
    elseif($_GET["titulo"]==="Editar Usuario" || $_GET["titulo"]==="Editar Proveedor" || $_GET["titulo"]==="Editar Cliente" )
    {

        $id=$datos["idPersona"];
        $db->actualizar('Persona',$resultadoValidacion->getValues(), "idPersona=$id");

        if($_GET["titulo"]==="Editar Usuario")header("Location: nuevoUsuario_2.php?titulo=Editar Usuario&id=$id");
        else if($_GET["titulo"]==="Editar Proveedor")header("Location: nuevoProveedor.php?titulo=Editar Proveedor&id=$id");
        else if($_GET["titulo"]==="Editar Cliente")header("Location: nuevoCliente.php?titulo=Editar Cliente&id=$id");
    }
    else
    {
        $id=$db->insertar('Persona', $resultadoValidacion->getValues());
        if($_GET["titulo"]==="Nuevo Usuario") header("Location: nuevoUsuario_2.php?titulo=Nuevo Usuario&id=$id");
        elseif($_GET["titulo"]==="Nuevo Proveedor")header("Location: nuevoProveedor.php?titulo=Nuevo Proveedor&id=$id");
        elseif($_GET["titulo"]==="Nuevo Cliente")header("Location: nuevoCliente.php?titulo=Nuevo Cliente&id=$id");
    }
}

?>

<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Nueva persona</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <!-- Place favicon.ico in the root directory -->

    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/vivaStyle.css" media="screen" />

    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Add your site or application content here -->
<h1><?php echo $_GET["titulo"]; ?></h1>
<form method="post" class="form-style-1" >
<div>
   <div class="div-nombre">
    <label>Nombre: </label>
    <input type="text" name="nombre" value="<?php echo $datos["Nombre"];?>">
   </div>

    <div class="div-nombre">
        <label>Apellido: </label>
        <input type="text" name="apellido" value="<?php echo $datos["Apellido"];?>">
    </div>
</div>

    <div>
        <label>Documento: </label>
        <input type="number" name="dni" placeholder="Sin puntos ni guiones" value="<?php echo $datos["Documento"];?>">
    </div>

    <div>
        <label>Fecha nacimiento: </label>
        <input type="date" name="nacimiento" value="<?php echo $datos["Fecha_nacimiento"];?>">

    </div>
<div>
    <div class="div-dom">
        <label>Direccion: </label>
        <input type="text" name="direccion" value="<?php echo $datos["Direccion"];?>">
    </div>

    <div class="div-dom">
        <label>Ciudad: </label>
        <input type="text" name="ciudad" value="<?php echo $datos["Ciudad"];?>">
    </div>

    <div class="div-dom">

        <label>Provincia:</label>
        <input type="text" name="provincia" value="<?php echo $datos["Provincia"];?>">
    </div>

    <div class="div-dom">
        <label>Codigo postal:</label>
        <input type="number" name="codigo_postal" value="<?php echo $datos["Codigo_postal"];?>">
    </div>
</div>

    <div>
        <div class="div-contacto">
            <label>Email:</label>
            <input type="text" name="email" value="<?php echo $datos["Email"];?>">
        </div>

        <div class="div-contacto">
            <label>Telefono:</label>
            <input type="number" name="telefono" value="<?php echo $datos["Telefono"];?>">
        </div>

        <div class="div-contacto">
            <label>Celular: </label>
            <input type="number" name="celular" value="<?php echo $datos["Celular"];?>">
        </div>
    </div>


    <div>
        <input type="submit" value="Aceptar" action="">
        <a href="index.php" ><button type="button" id="btnUsuarioCancelar">Cancelar</button></a>
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

