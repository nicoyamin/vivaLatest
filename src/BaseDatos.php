<?php

namespace Viva;


class BaseDatos
{
    protected $viva;

    public function __construct(\medoo $medoo) //Llama a la BD para traer registros
    {
        $this->viva = $medoo;
    }

    public function insertar($tabla, $datos)
    {
        // recuperar las claves del array (nombre de las columnas en DB)
        $campos = array_keys($datos);

        // Armar la consulta
        $sql = "INSERT INTO ".$tabla." (`".implode('`,`', $campos)."`) VALUES('".implode("','", $datos)."')";

        // Ejecutar y devolver la consulta sql
        //dump($sql);

        $this->viva->query($sql);

        return $this->viva->pdo->lastInsertId();




        throw new \Exception("Error al insertar");
    }



    public function actualizar($table_name, $form_data, $where_clause='')
    {
        // check for optional where clause
        $whereSQL = '';
        if(!empty($where_clause))
        {
            // check to see if the 'where' keyword exists
            if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE')
            {
                // not found, add key word
                $whereSQL = " WHERE ".$where_clause;
            } else
            {
                $whereSQL = " ".trim($where_clause);
            }
        }
        // start the actual SQL statement
        $sql = "UPDATE ".$table_name." SET ";

        // loop and build the column /
        $sets = array();
        foreach($form_data as $column => $value)
        {
            $sets[] = "`".$column."` = '".$value."'";
        }
        $sql .= implode(', ', $sets);

        // append the where statement
        $sql .= $whereSQL;
        //dump($sql);
        // run and return the query result
        $this->viva->query($sql);

    }
}