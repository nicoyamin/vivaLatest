<?php

namespace Viva;


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
        if (filter_var($dni, FILTER_VALIDATE_INT, array("options"=> array("min_range"=>1000000, "max_range"=>100000000)))===false)
        {
            throw new \InvalidArgumentException('DNI no valido. Por favor, revise el DNI ingresado');
        }
        else
        {
            $this->dni=$dni;
        }

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
        if (filter_var($codigo_postal, FILTER_VALIDATE_INT)===false)
        {
            throw new \InvalidArgumentException('Codigo postal no valido. Por favor, revise el valor ingresado');
        }
        else
        {
            $this->codigo_postal=$codigo_postal;
        }

        return $this;
    }

    public function setEmail($email)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $this->email=$email;
        }
        else
        {
            throw new \InvalidArgumentException('Email no valido. Por favor, revise lo ingresado');
        }

        return $this;
    }

    public function setTelefono($telefono)
    {
        if (filter_var($telefono, FILTER_VALIDATE_INT)===false)
        {
            throw new \InvalidArgumentException('Por favor, ingrese solo el numero, sin guiones mi espacios');
        }
        else
        {
            $this->telefono=$telefono;
        }

        return $this;
    }

    public function setCelular($celular)
    {
        if (filter_var($celular, FILTER_VALIDATE_INT)===false)
        {
            throw new \InvalidArgumentException('Por favor, ingrese solo el numero, sin guiones mi espacios');
        }
        else
        {
            $this->celular=$celular;
        }

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

    public function insertar()
    {
        if($this->getNombre() && $this->getApellido() && $this->getDni())
        {
            return $this->viva->insert('Persona', [
                'Nombre'=>$this->getNombre(),
                'Apellido'=>$this->getApellido(),
                'Documento'=>$this->getDni(),
                'Fecha_nacimiento'=>$this->getNacimiento(),
                'Direccion'=>$this->getDireccion(),
                'Ciudad'=>$this->getCiudad(),
                'Provincia'=>$this->getProvincia(),
                'Codigo_postal'=>$this->getCodigoPostal(),
                'Email'=>$this->getEmail(),
                'Telefono'=>$this->getTelefono(),
                'Celular'=>$this->getCelular()
            ]);
        }

        throw new \Exception("Error al insertar");
    }
}