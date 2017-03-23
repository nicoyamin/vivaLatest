<?php
namespace Viva;

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;
use DateTime;


class Asistencia
{
    protected $viva;

    public function __construct(\medoo $medoo) //Llama a la BD para traer registros
    {
        $this->viva = $medoo;
    }

    public function selectAsistencias($viva, $fechaI, $fechaF)
    {
        if($fechaI != "" && $fechaF=="")
        {
        $resultado=$viva->select("Asistencia(A)",["[>]Turno(T)" => ["A.idTurno"=>"idTurno"],"[>]Usuario(U)"=>["A.idUsuario"=>"idUsuario"]], [
            "A.idAsistencia",
            "A.Fecha",
            "A.Hora_entrada",
            "A.Hora_salida",
            "A.Check_in",
            "A.Check_out",
            "A.Codigo",
            "A.Confirmado",
            "T.Descripcion_turno",
            "U.Nombre_usuario"
        ],
            ["Fecha[>=]"=>$fechaI, "ORDER"=>"Fecha"]);
        }

        elseif($fechaF != "" && $fechaI=="")
        {
        $resultado=$viva->select("Asistencia(A)",["[>]Turno(T)" => ["A.idTurno"=>"idTurno"],"[>]Usuario(U)"=>["A.idUsuario"=>"idUsuario"]], [
            "A.idAsistencia",
            "A.Fecha",
            "A.Hora_entrada",
            "A.Hora_salida",
            "A.Check_in",
            "A.Check_out",
            "A.Codigo",
            "A.Confirmado",
            "T.Descripcion_turno",
            "U.Nombre_usuario"
        ],
            ["Fecha[<=]"=>$fechaF,"ORDER"=>"Fecha"]);
        }

        else
        {
            $resultado=$viva->select("Asistencia(A)",["[>]Turno(T)" => ["A.idTurno"=>"idTurno"],"[>]Usuario(U)"=>["A.idUsuario"=>"idUsuario"]], [
            "A.idAsistencia",
            "A.Fecha",
            "A.Hora_entrada",
            "A.Hora_salida",
            "A.Check_in",
            "A.Check_out",
            "A.Codigo",
            "A.Confirmado",
            "T.Descripcion_turno",
            "U.Nombre_usuario"
        ],
            ["AND"=>["Fecha[<=]"=>$fechaF,"Fecha[>=]"=>$fechaI],"ORDER"=>"Fecha"]);
        }




        return $resultado;
    }

    public function selectAllAsistencias($viva)
    {
         $resultado=$viva->select("Asistencia(A)",["[>]Turno(T)" => ["A.idTurno"=>"idTurno"],"[>]Usuario(U)"=>["A.idUsuario"=>"idUsuario"]], [
            "A.idAsistencia",
            "A.Fecha",
            "A.Hora_entrada",
            "A.Hora_salida",
            "A.Check_in",
            "A.Check_out",
            "A.Codigo",
            "A.Confirmado",
            "T.Descripcion_turno",
            "U.Nombre_usuario"
        ]);
         return $resultado;
    }

    public function selectAllTurnos($viva)
    {
        $resultado=$viva->select("Asistencia(A)",["[>]Usuario(U)" => ["A.idUsuario"=>"idUsuario"],"[>]Persona(P)"=>["U.idPersona"=>"idPersona"]], [
            "A.idAsistencia",
            "A.Fecha",
            "A.Hora_entrada",
            "A.Hora_salida",
            "A.Check_in",
            "A.Check_out",
            "A.Codigo",
            "A.Confirmado",
            "U.Nombre_usuario",
            "P.Nombre",
            "P.Apellido"
        ]);
        return $resultado;
    }

}