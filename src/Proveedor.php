<?php

namespace Viva;

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;
use datetime;
class Proveedor
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

    public function __construct(\medoo $medoo) //Llama a la BD para traer registros
    {
        $this->viva = $medoo;
    }

    public function selectProveedores($viva)
    {
        $resultado=$viva->select('Persona',["[><]Proveedor" => ["idPersona"=>"Representante"]], [
            "Persona.idPersona",
            "Persona.Apellido",
            "Persona.Nombre",
            "Persona.Documento",
            "Persona.Fecha_nacimiento",
            "Persona.Direccion",
            "Persona.Ciudad",
            "Persona.Provincia",
            "Persona.Email",
            "Persona.Telefono",
            "Persona.Celular",
            "Proveedor.Proveedor_nombre",
            "Proveedor.Sitio_web",
            "Proveedor.Email",
            "Proveedor.Telefono",
            "Proveedor.Fecha_alta",
            "Proveedor.Representante",
            "Proveedor.idProveedor",
            "Proveedor.Habilitado",
            "Proveedor.Cuit_cuil"

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

    /*public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        return $this;
    }


    public function setWeb($web)
    {
        $this->web = $web;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
        return $this;
    }

    public function setHabilitado($habilitado)
    {
        $this->habilitado = $habilitado;
        return $this;
    }

   public function getHabilitado()
    {
        return $this->habilitado;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getWeb()
    {
        return $this->web;
    }


    public function getEmail()
    {
        return $this->email;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }


    public function getIdPersona()
    {
        return $this->idPersona;
    }


    public function setIdPersona($idPersona)
    {
        $this->idPersona = $idPersona;
        return $this;
    }


    public function getFechaAlta()
    {
        return $this->fecha_alta;
    }


    public function setFechaAlta()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $this->fecha_alta = date("Y-m-d");
        return $this;
    }*/


    public function validarProveedor()
    {
        $v=new Validator();
        $v->required('Proveedor_nombre')->alnum(true)->lengthBetween(1,100);
        $v->required('Telefono')->digits()->lengthBetween(6,20);
        $v->optional('Email')->email()->lengthBetween(5,100);
        $v->optional('Sitio_web')->lengthBetween(5,100);
        $v->optional('Fecha_alta');
        $v->required('Representante')->digits();
        $v->optional('Habilitado');
        $v->optional('Cuit_cuil')->digits()->lengthBetween(6,20);

        return $v->validate($this->valores());

    }

    protected function valores()
    {

        return[
            'Proveedor_nombre'=>$this->nombre,
            'Sitio_web'=>$this->web,
            'Email'=>$this->email,
            'Telefono'=>$this->telefono,
            'Fecha_alta'=>$this->fecha_alta,
            'Representante'=>$this->idPersona,
            'Habilitado'=>$this->habilitado,
            'Cuit_cuil'=>$this->cuit

        ];
    }
}