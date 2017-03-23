<?php

require_once '../../vendor/autoload.php';
include 'db.inc.php';


$db = new Viva\BaseDatos($viva);

$viva->delete('Asistencia',["idAsistencia"=>$_POST['idAsistencia']]);