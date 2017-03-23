<?php

namespace Viva;

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;
use datetime;

class Usuario
{
    protected $viva;

    protected $usuario;
    protected $password;
    protected $password_confirmar;
    protected $privilegio;
    protected $idpersona;
    protected $fecha_creacion;


    public function __construct(\medoo $medoo) //Llama a la BD para traer registros
    {
        $this->viva = $medoo;
    }

    public function selectUsuarios($viva)
    {
        $resultado=$viva->select('Persona',["[><]Usuario" => ["idPersona"=>"idPersona"]], [
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
            "Usuario.idUsuario",
            "Usuario.Nombre_usuario",
            "Usuario.Privilegio",
            "Usuario.Fecha_creacion",
            "Usuario.Habilitado"
        ]);
        return $resultado;
    }

    public function selectEmpleados($viva)
    {
        $resultado=$viva->select('Usuario',["[>]Persona" => ["idPersona"=>"idPersona"]], [
            "Persona.Apellido",
            "Persona.Nombre",
            "Usuario.idUsuario"
        ],
        ["Privilegio"=>"1"]
    );
        return $resultado;
    }

    public function selectNombreUsuarioPassword($viva, $usuario, $password)
    {
        $resultado=$viva->select('Usuario',["[>]Persona" => ["idPersona"=>"idPersona"]], [
            "Persona.Apellido",
            "Persona.Nombre",
            "Usuario.idUsuario",
            "Usuario.Privilegio"
        ],
            ["AND"=>["Nombre_usuario"=>$usuario,"Password"=>$password]]
        );
        return $resultado;
    }



    public function getUsuario()
    {
        return $this->usuario;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
        return $this;
    }


    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = md5($password.'ijdb');
        return $this;
    }


    public function getPrivilegio()
    {
        return $this->privilegio;
    }

    public function setPrivilegio($privilegio)
    {
        $this->privilegio = $privilegio;
        return $this;
    }

    public function getPasswordConfirmar()
    {

        return $this->password_confirmar;
    }

    public function setPasswordConfirmar($password_confirmar)
    {
        $this->password_confirmar = md5($password_confirmar.'ijdb');
        return $this;
    }


    public function getIdpersona()
    {
        return $this->idpersona;
    }


    public function setIdpersona($idpersona)
    {
        $this->idpersona = $idpersona;
        return $this;
    }

    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
    }


    public function setFechaCreacion()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $this->fecha_creacion = date("Y-m-d");
        return $this;
    }




    public function validarUsuario($confirmar) //Reglas de validacion para la clase usuario. Usa Clases validator y validationResult
    {
        $v=new Validator();
        $v->required('Nombre_usuario')->alnum()->lengthBetween(4,100);
        $v->required('Password')->alnum()->equals($confirmar);
        $v->required('Privilegio')->between(1,2);
        $v->required('idPersona');
        $v->required('Fecha_creacion');

        return $v->validate($this->valores());
    }

    protected function valores()
    {

        return[
            'Nombre_usuario'=>$this->getUsuario(),
            'Password'=>$this->getPassword(),
            'Privilegio'=>$this->getPrivilegio(),
            'idPersona'=>$this->getIdpersona(),
            'Fecha_creacion'=>$this->getFechaCreacion()

        ];
    }

}