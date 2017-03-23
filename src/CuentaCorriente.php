<?php
/**
 * Created by PhpStorm.
 * User: Nicolas-PC
 * Date: 23/02/2017
 * Time: 01:33 PM
 */

namespace Viva;


use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;
use datetime;

class CuentaCorriente
{

    protected  $viva;

    protected $cliente;
    protected $tipo;
    protected $estado="Activa";
    protected $fechaApertura;
    protected $margen;
    protected $balance=0;

    public function __construct(\medoo $medoo) //Llama a la BD para traer registros
    {
        $this->viva = $medoo;
    }

    function __get($atributo)
    {
        return $this->$atributo;
    }

    function __set($atributo, $valor)
    {
        $this->$atributo=$valor;
    }

    public function selectCuentas($viva)
    {
        $resultado=$viva->select('Cuenta_corriente',["[><]Cliente" => ["idCliente"=>"idCliente"]], [
            "Cliente.Nombre",
            "Cliente.idCliente",
            "Cuenta_corriente.idCuenta_corriente",
            "Cuenta_corriente.Tipo",
            "Cuenta_corriente.Estado",
            "Cuenta_corriente.Fecha_apertura",
            "Cuenta_corriente.Margen",
            "Cuenta_corriente.Fecha_ultimo_movimiento",
            "Cuenta_corriente.Balance"

        ]);
        return $resultado;
    }

    public function validarCuenta()
    {
        $v=new Validator();
        $v->required('idCliente')->alnum(true)->lengthBetween(1,100);
        $v->optional('Estado');
        $v->required('Tipo')->inArray(["Particular","Empresa"]);
        $v->optional('Fecha_apertura');
        $v->required('Margen')->numeric();
        $v->optional('Fecha_ultimo_movimiento');
        $v->optional('Balance');


        return $v->validate($this->valores());

    }
    protected function valores()
    {

        return[
            'idCliente'=>$this->cliente,
            'Estado'=>$this->estado,
            'Tipo'=>$this->tipo,
            'Fecha_apertura'=>$this->fechaApertura,
            'Margen'=>$this->margen,
            'Balance'=>$this->balance,
            'Fecha_ultimo_movimiento'=>$this->fechaApertura
        ];
    }

}