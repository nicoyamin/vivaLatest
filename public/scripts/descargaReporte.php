<?php

//$fileName = basename($_GET["nombre"]);
//$filePath = $_GET["ruta"];
$fileName=basename($nombre);
$filePath=$ruta;
//$intentos=0;
for($i=0; $i<5;$i++)
{
    if(file_exists($filePath)) break;
    else sleep(1);
}
if (file_exists($filePath)) {
    header('Content-Description: File Transfer');
    //header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$fileName.'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);

    exit;
}
else echo "Arhcivo nio encontrado";


