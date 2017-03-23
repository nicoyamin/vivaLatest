<?php

namespace Viva;

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;
use DateTime;

class Persona
{
    protected $viva;

    protected $nombre;
    protected $apellido;
    protected $dni;
    protected $nacimiento;
    protected $direccion;
    protected $ciudad;
    protected $provincia;
    protected $codigo_postal;
    protected $email;
    protected $telefono;
    protected $celular;



    public function __construct(\medoo $medoo) //Llama a la BD para traer registros
    {
        $this->viva = $medoo;
    }

    public function encontrarTodo()  //Funcion para traer todos las personas registradas
    {

    }



    //INICIO DE FUNCIONES SETTERS
    public function setNombre($nombre)
    {
        $this->nombre= (string)$nombre;
        return $this;
    }

    public function setApellido($apellido)
    {
        $this->apellido= (string)$apellido;
        return $this;
    }

    public function setDni($dni)  //Esta funcion realiza validacion de numero entero y revisa que la longitud del dni sea entre 1000000 y 100000000
    {
        $this->dni=$dni;

        return $this;
    }

    public function setNacimiento($nacimiento) //Esta funcion revisa que la fecha de nacimiento sea anterior al dia de la fecha. Mas validaciones de fecha se realizaran mediante javascript
    {
        $hoy = new DateTime();

        if($nacimiento < $hoy)
        {
            $this->nacimiento=$nacimiento;
        }
        else
        {
         throw new \InvalidArgumentException('Fecha no valida. Por favor, revise la fecha ingresada');
        }

        return $this;
    }

    public function setDireccion($direccion)
    {
        $this->direccion= (string)$direccion;
        return $this;
    }

    public function setCiudad($ciudad)
    {
        $this->ciudad=(string)$ciudad;
        return $this;
    }

    public function setProvincia($provincia)
    {
        $this->provincia=(string)$provincia;
        return $this;
    }

    public function setCodigoPostal($codigo_postal)
    {
        $this->codigo_postal=$codigo_postal;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email=$email;
        return $this;
    }

    public function setTelefono($telefono)
    {
        $this->telefono=$telefono;
        return $this;
    }

    public function setCelular($celular)
    {
        $this->celular=$celular;
         return $this;
    }
    //FIN DE FUNCIONES SETTERS


    //INICIO DE FUNCIONES GETTERS

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getDni()
    {
        return $this->dni;
    }

    public function getNacimiento()
    {
        return $this->nacimiento;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getCiudad()
    {
        return $this->ciudad;
    }

    public function getProvincia()
    {
        return $this->provincia;
    }

    public function getCodigoPostal()
    {
        return $this->codigo_postal;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    //FIN DE FUNCIONES GETTERS

    public function validarPersona() //Reglas de validacion para la clase persona. Usa Clases validator y validationResult
    {
        $v=new Validator();
        $v->required('Nombre')->alnum(true)->lengthBetween(1,100);
        $v->required('Apellido')->alnum(true)->lengthBetween(1,100);
        $v->required('Documento')->digits()->lengthBetween(7,10);
        $v->optional('Fecha_nacimiento')->datetime();
        $v->optional('Direccion')->alnum(true)->lengthBetween(1,255);
        $v->optional('Ciudad')->alnum(true)->lengthBetween(1,100);
        $v->optional('Provincia')->alnum(true)->lengthBetween(1,100);
        $v->optional('Codigo_postal')->alnum()->lengthBetween(1,10);
        $v->optional('Email')->email()->lengthBetween(5,100);
        $v->optional('Telefono')->digits()->lengthBetween(5,10);
        $v->optional('Celular')->digits()->lengthBetween(10,null);


        return $v->validate($this->valores());
    }

    protected function valores()   //Arma un array con los valores para su validacion
    {
        return[
            'Nombre' => $this->getNombre(),
            'Apellido' => $this->getApellido(),
            'Documento' => $this->getDni(),
            'Fecha_nacimiento' => $this->getNacimiento(),
            'Direccion' => $this->getDireccion(),
            'Ciudad' => $this->getCiudad(),
            'Provincia' => $this->getProvincia(),
            'Codigo_postal' => $this->getCodigoPostal(),
            'Email' => $this->getEmail(),
            'Telefono' => $this->getTelefono(),
            'Celular' => $this->getCelular()
        ];
    }

}