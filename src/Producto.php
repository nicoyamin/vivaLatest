<?php

namespace Viva;

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

class Producto
{
    protected $viva;

    protected $nombre;
    protected $categoria;
    protected $descripcion;
    protected $cantidad;
    protected $unidad;
    protected $precio;
    protected $perecedero;
    protected $codigo_barras;
    protected $proveedor;
    protected $habilitado="Si";
    protected $existencias=0;
    protected $stockMinimo=0;

    public function __construct(\medoo $medoo) //Llama a la BD para traer registros
    {
        $this->viva = $medoo;
    }

    public function selectProductos($viva)
    {
        $resultado=$viva->select("Producto(P)",["[><]Producto_categoria(PC)" => ["P.idCategoria"=>"idCategoria"],"[><]Proveedor(PR)"=>["P.idProveedor"=>"idProveedor"]], [
            "P.idProducto",
            "P.Nombre_producto",
            "P.Descripcion_producto",
            "PC.Categoria_nombre",
            "P.Cantidad_unitaria_producto",
            "P.Unidad_producto",
            "P.Precio_unitario_producto",
            "P.Perecedero",
            "P.Habilitado",
            "P.Codigo_barras_producto",
            "P.Existencia_producto",
            "P.Stock_minimo_producto",
            "PR.Proveedor_nombre"
        ],["ORDER"=>"P.Nombre_producto"]);
        return $resultado;
    }

    public function selectPorStock($viva)
    {

       /* $resultado=$viva->debug()->select("Producto(P)", ["[>]Proveedor(PR)"=>["P.idProveedor"=>"idProveedor"]],
            ["P.Nombre_producto",
            "P.Cantidad_unitaria_producto",
            "P.Unidad_producto",
            "PR.Proveedor_nombre",
            "P.Existencia_producto",
            "P.Stock_minimo_producto"],
            ["AND"=>[
                "P.Existencia_producto[<=]" => $viva->quote($tabla),
                "P.Stock_minimo_producto[>]"=>0,
                "P.Habilitado"=>"Si",

            ]
            ]
        );*/
        $resultado=$viva->query("SELECT P.Nombre_producto, P.Cantidad_unitaria_producto, P.Unidad_producto, PR.Proveedor_nombre, P.Existencia_producto, P.Stock_minimo_producto FROM Producto as P left join Proveedor as PR ON P.idProveedor=PR.idProveedor WHERE P.Habilitado='Si' AND P.Existencia_producto <= P.stock_minimo_producto*1.20 AND P.Stock_minimo_producto>0")->fetchAll();

        return $resultado;
	}



    //INICIO DE FUNCIONES SETTERS

    public function setNombre($nombre)
    {
        $this->nombre=(string)$nombre;
        return $this;
    }

    public function setCategoria($categoria)
    {
        $this->categoria=$categoria;
        return $this;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion=(string)$descripcion;
        return $this;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad=$cantidad;
        return $this;
    }

    public function setUnidad($unidad)
    {
        $this->unidad=(string)$unidad;
        return $this;
    }

    public function setPrecio($precio)
    {
        $this->precio=(float)$precio;
        return $this;
    }

    public function setPerecedero($perecedero)
    {
        $this->perecedero=$perecedero;
        return $this;
    }

    public function setCodigo_barras($codigo_barras)
    {
        $this->codigo_barras=$codigo_barras;
        return $this;
    }

    public function setExistencias($existencias)
    {
        $this->existencias=$existencias;
        return $this;
    }

    public function setStockMinimo($stockMinimo)
    {
        $this->stockMinimo=$stockMinimo;
        return $this;
    }

    public function setProveedor($proveedor)
    {
        $this->proveedor=$proveedor;
        return $this;
    }

    public function setHabilitado($habilitado)
    {
        $this->habilitado=$habilitado;
        return $this;
    }
    //FIN DE FUNCIONES SETTERS

    //INICIO DE FUNCIONES GETTERS

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function getUnidad()
    {
        return $this->unidad;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function getPerecedero()
    {
        return $this->perecedero;
    }

    public function getCodigo_barras()
    {
        return $this->codigo_barras;
    }

    public function getExistencias()
    {
        return $this->existencias;
    }

    public function getStockMinimo()
    {
        return $this->stockMinimo;
    }

    public function getProveedor()
    {
        return $this->proveedor;
    }

    public function getHabilitado()
    {
        return $this->habilitado;
    }

    //FIN DE FUNCIONES SETTERS


    public function validarProducto()  //Reglas de validacion para la clase persona. Usa Clases validator y validationResult
    {
        $v=new Validator();
        $v->required('Nombre_producto')->alnum(true)->lengthBetween(1,100);
        $v->required('idCategoria')->digits()->lengthBetween(1,3);
        $v->required('idProveedor')->digits()->lengthBetween(1,10);
        $v->required('Precio_unitario_producto')->float()->lengthBetween(1,5);
        $v->optional('Descripcion_producto')->alnum(true)->lengthBetween(1,255);
        $v->optional('Cantidad_unitaria_producto')->numeric()->lengthBetween(1,10);
        $v->optional('Unidad_producto')->alnum(true)->lengthBetween(1,10);
        $v->optional('Perecedero');
        $v->optional('Codigo_barras_producto')->alnum(true)->lengthBetween(1,20);
        $v->optional('Existencia_producto')->numeric()->greaterThan(-1);
        $v->optional('Stock_minimo_producto')->numeric()->greaterThan(-1);
        $v->optional('Habilitado');

        return $v->validate($this->valores());

    }

    protected function valores()   //Arma un array con los valores para su validacion
    {
        return[
            'Nombre_producto' => $this->getNombre(),
            'idCategoria' => $this->getCategoria(),
            'idProveedor' => $this->getProveedor(),
            'Precio_unitario_producto' => $this->getPrecio(),
            'Descripcion_producto' => $this->getDescripcion(),
            'Cantidad_unitaria_producto' => $this->getCantidad(),
            'Unidad_producto' => $this->getUnidad(),
            'Perecedero' => $this->getPerecedero(),
            'Codigo_barras_producto' => $this->getCodigo_barras(),
            'Existencia_producto' => $this->getExistencias(),
            'Stock_minimo_producto' => $this->getStockMinimo(),
            'Habilitado' => $this->getHabilitado()

        ];
    }
}