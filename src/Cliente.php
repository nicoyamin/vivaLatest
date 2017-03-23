<?php
/**
 * Created by PhpStorm.
 * User: Nicolas-PC
 * Date: 22/02/2017
 * Time: 01:32 PM
 */

namespace Viva;


use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;
use datetime;


class Cliente
{

    protected $viva;

    protected $nombre;
    protected $web;
    protected $email;
    protected $telefono;
    protected $idPersona;
    protected $fecha_alta;
    protected $habilitado="Si";
    protected $cuit;
    protected $cuenta="No";

    public function __construct(\medoo $medoo) //Llama a la BD para traer registros
    {
        $this->viva = $medoo;
    }

    public function selectClientes($viva)
    {
        $resultado=$viva->select('Persona',["[><]Cliente" => ["idPersona"=>"idPersona"]], [
            "Persona.idPersona",
            "Persona.Apellido",
            "Persona.Nombre(nombreRep)",
            "Persona.Documento",
            "Persona.Fecha_nacimiento",
            "Persona.Direccion",
            "Persona.Ciudad",
            "Persona.Provincia",
            "Persona.Email",
            "Persona.Telefono",
            "Persona.Celular",
            "Cliente.idCliente",
            "Cliente.Nombre",
            "Cliente.Sitio_web",
            "Cliente.Email",
            "Cliente.Telefono",
            "Cliente.Fecha_alta",
            "Cliente.idPersona",
            "Cliente.Cuit_cuil",
            "Cliente.Habilitado",
            "Cliente.Cuenta_corriente"

        ]);
        return $resultado;
    }

    function __get($atributo)
    {
        return $this->$atributo;
    }

    function __set($atributo, $valor)
    {
        $this->$atributo=$valor;
    }

    public function validarCliente()
    {
        $v=new Validator();
        $v->required('Nombre')->alnum(true)->lengthBetween(1,100);
        $v->required('Telefono')->digits()->lengthBetween(6,20);
        $v->optional('Email')->email()->lengthBetween(5,100);
        $v->optional('Sitio_web')->lengthBetween(5,100);
        $v->optional('Fecha_alta');
        $v->required('idPersona')->digits();
        $v->optional('Habilitado');
        $v->optional('Cuit_cuil')->digits()->lengthBetween(6,20);
        $v->optional('Cuenta_corriente');

        return $v->validate($this->valores());

    }
    protected function valores()
    {

        return[
            'Nombre'=>$this->nombre,
            'Sitio_web'=>$this->web,
            'Email'=>$this->email,
            'Telefono'=>$this->telefono,
            'Fecha_alta'=>$this->fecha_alta,
            'idPersona'=>$this->idPersona,
            'Habilitado'=>$this->habilitado,
            'Cuit_cuil'=>$this->cuit,
            'Cuenta_corriente'=>$this->cuenta
        ];
    }

}