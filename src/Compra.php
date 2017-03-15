<?php

namespace Viva;

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;
use DateTime;

class compra
{
	protected $viva;

	protected $fecha;
	protected $Condiciones_pago;
	protected $Lugar_entrega;
	protected $Fecha_entrega;
	protected $Enviar_por;
	protected $Estado;

	public function __construct(\medoo $medoo) //Llama a la BD para traer registros
    {
        $this->viva = $medoo;
    }

    public function selectCompras($viva)
    {
    	$resultado=$viva->select("Compra(C)",["[>]Compra_estado(CE)"=>["C.Estado"=>"idCompra_estado"],"[>]Proveedor(P)"=>["C.idProveedor"=>"idProveedor"]], [
    		"C.idCompra",
    		"C.Fecha",
    		"C.Condiciones_pago",
    		"C.Lugar_entrega",
    		"C.Fecha_entrega",
    		"C.Enviar_por",
    		"CE.Descripcion",
    		"C.idProveedor",
    		"P.Proveedor_nombre"
    	]);

    	return $resultado;

    }

	public function selectEstados($viva)
	{
		$resultado=$viva->select("Compra_estado",["idCompra_estado","Descripcion"]);
		return $resultado;
	}


}