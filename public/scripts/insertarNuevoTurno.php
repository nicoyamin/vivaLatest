<?php

require_once '../../vendor/autoload.php';
include 'db.inc.php';


$db = new Viva\BaseDatos($viva);

/*$usuario=$_POST['usuario'];
$turno=$_POST['turno'];
$fecha=$_POST['fecha'];
$confirmado="No";*/

$turnoAsignado=$viva->select("Asistencia", ["idAsistencia"], ["AND" => ["idUsuario" => $_POST["usuario"], "idTurno"=>$_POST["turno"], "Fecha"=>$_POST["fecha"]]]);

//echo "<script>alert('No puede ser');</script>";

if(empty($turnoAsignado)) {
    $codigo = md5rand(8);
    if ($_POST['turno'] == 1) {
        $entrada = "06:00:00";
        $salida = "14:00:00";
    } elseif ($_POST['turno'] == 2) {
        $entrada = "14:00:00";
        $salida = "22:00:00";
    } elseif ($_POST['turno'] == 3) {
        $entrada = "22:00:00";
        $salida = "06:00:00";
    } else {
        $entrada = "05:00:00";
        $salida = "05:00:00";
    }

    $asistencia = [
        'idUsuario' => $_POST['usuario'],
        'idTurno' => $_POST['turno'],
        'Fecha' => $_POST['fecha'],
        'Hora_entrada' => $entrada,
        'Hora_salida' => $salida,
        'Codigo' => $codigo,
        'Check_in' => "Pendiente",
        'Check_out' => "Pendiente",
        'Confirmado' => "Pendiente"
    ];

    $db->insertar('Asistencia', $asistencia);
}

function md5rand ($length) {

    $pass1 = strtoupper(md5(rand(0, 1000000)));

    $rand_start = rand(5,strlen($pass1));

    if($rand_start == strlen($pass1)) {

        $rand_start = 1;

    }



	return $pass1;

}
