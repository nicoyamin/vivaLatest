<?php

$filepath=$_GET["ruta"];
$filename=$_GET["nombre"];

for($i=0; $i<10;$i++)
{
    if(file_exists($filepath)) break;
    else sleep(1);
}

downloadFile($filename,$filepath);


function downloadFile($filename,$filepath)
{


    /*header("Content-disposition: attachment; filename=".$filename);
    header("Content-Length: " . filesize($filepath));
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile($filepath);*/
    header('Content-Description: File Transfer');
    header('Content-Type: application/msword');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);


    return;
}
?>


