<?php

//header("content-type: application/json");

require_once '../../vendor/autoload.php';



include 'db.inc.php';

$asistencia = new Viva\Asistencia($viva);

    $arrayAsistencias = $asistencia->selectAllTurnos($viva);

    $calendario=array();

    foreach ($arrayAsistencias as $arrayAsistencia)
    {
        $e=array();
        $e['id'] = $arrayAsistencia["idAsistencia"];
        $e['title'] = $arrayAsistencia["Nombre"]." ".$arrayAsistencia["Apellido"];
        $e['start'] = $arrayAsistencia["Fecha"]."T".$arrayAsistencia["Hora_entrada"];
        $e['end'] = $arrayAsistencia["Fecha"]."T".$arrayAsistencia["Hora_salida"];
        $e['allDay'] = false;
        $e['codigo'] = $arrayAsistencia["Codigo"];
        $e['confirmado'] = $arrayAsistencia["Confirmado"];
        $e['entrada'] = $arrayAsistencia["Hora_entrada"];
        $e['salida'] = $arrayAsistencia["Hora_salida"];
        $e['checkin']=$arrayAsistencia["Check_in"];
        $e['checkout']=$arrayAsistencia["Check_out"];

        if($arrayAsistencia["Confirmado"]=="Pendiente") //El turno esta Asignado
        {
            $e['color']="#428bca"; //Azul
        }

        if($arrayAsistencia["Confirmado"]=="Iniciado") //El turno esta iniciado
        {
            $e['color']="#ffbb33"; //Amarillo
        }

        if($arrayAsistencia["Confirmado"]=="Confirmado") //El turno esta Finalizado
        {
            $e['color']="#378006"; //Verde
        }

        if($arrayAsistencia["Confirmado"]=="Ausente") //El turno esta Ausente
        {
            $e['color']="#CC0000"; //Rojo
        }

        if( $arrayAsistencia["Confirmado"]=="Vencido") //El turno esta Sin CheckOut
        {
            $e['color']="#4B515D"; //Gris
        }

        //$e['color']="#378006"; //Verde

        array_push($calendario,$e);
    }

//dump(json_encode($calendario));
    echo json_encode($calendario);

