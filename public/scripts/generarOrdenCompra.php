<?php

require_once '../../vendor/autoload.php';

include 'db.inc.php';
use JasperPHP\JasperPHP;

$db = new Viva\BaseDatos($viva);
$jasper = new JasperPHP;
$prueba="Gasnor";

//$myPublicFolder = public_path();
//dump($myPublicFolder);

    /*$output = $jasper->list_parameters(
        'reports/hello_world.jasper'
    )->execute();

foreach($output as $parameter_description)
    echo $parameter_description;*/


    // Crear el objeto JasperPHP
    
    // Generar el Reporte
    /*$jasper->process(
        // Ruta y nombre de archivo de entrada del reporte
        '../reports/ordenCompra.jasper', 
        false, // Ruta y nombre de archivo de salida del reporte (sin extensión)
        array('pdf'), // Formatos de salida del reporte
        array('proveedor' => $_POST["proveedor"],
              'pago'=>$_POST["pago"],
              'lugar'=>$_POST["lugar"],
              'fechaEntrega'=>$_POST["fechaEntrega"],
              'envio'=>$_POST["envio"],
              'fecha'=>date("m/d/y")
              ) // Parámetros del reporte
    )->execute();*/
   
    //exec($jasper->output().' 2>&1', $output);
    //print_r($output);
?>