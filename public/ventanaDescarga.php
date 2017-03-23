<?php
use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';

include 'scripts/db.inc.php';

include("acceso.inc.php");

//dump($_SERVER["PHP_SELF"]);
$atras=$_GET["atras"];

?>

<html>
<head>
    <script type="text/javascript">
        function startDownload() {
            window.location = "download.php?ruta=<?=$_GET['ruta']?>&nombre=<?=$_GET['nombre']?>";

        }
    </script>
</head>
<body onload="startDownload();">
<h1>Descarga de documentos</h1>
<p>Su descarga comenzara en un momento. Si no inicia, use este <a href="download.php?ruta=<?=$_GET['ruta']?>&nombre=<?=$_GET['nombre']?>">link directo.</a></p>
<a href="<?php echo $atras ?>">Pagina Anterior.</a>
</body>
