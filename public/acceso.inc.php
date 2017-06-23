<?php

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';



date_default_timezone_set("America/Argentina/Salta");

function userIsLoggedIn()
{
    if (isset($_POST['action']) and $_POST['action'] == 'Ingresar')
    {
        if (!isset($_POST['usuario']) or $_POST['usuario'] == '' or !isset($_POST['password']) or $_POST['password'] == '')
        {
            $GLOBALS['loginError'] = 'Por favor complete ambos campos';
            return FALSE;
        }

        $password = md5($_POST['password'] . 'ijdb');

        if (databaseContainsAuthor($_POST['usuario'], $password))
        {
            session_start();
            $_SESSION['loggedIn'] = TRUE;
            $_SESSION['usuario'] = $_POST['usuario'];
            $_SESSION['password'] = $password;

            $turno=tieneTurnoIniciado($_POST["usuario"],$password);
            if(isset ($turno[0]))
            {
                $_SESSION["turno"]="Iniciado";
                $_SESSION["idTurno"]=$turno[0];
            }
            else $_SESSION['turno']="Sin iniciar";

            $_SESSION["surtidores"]="No";


            return TRUE;
        }

        else
        {
            session_start();
            unset($_SESSION['loggedIn']);
            unset($_SESSION['usuario']);
            unset($_SESSION['password']);
            unset($_SESSION['turno']);
            unset($_SESSION['idTurno']);
            unset($_SESSION['surtidores']);
            $GLOBALS['loginError'] = 'Clave o nombre de usuario incorrectos';

            return FALSE;
        }
    }

    if (isset($_POST['action']) and $_POST['action'] == 'logout')
    {
        session_start();
        unset($_SESSION['loggedIn']);
        unset($_SESSION['email']);
        unset($_SESSION['password']);
        unset($_SESSION['turno']);
        unset($_SESSION['idTurno']);
        unset($_SESSION['surtidores']);

        session_destroy();
        header('Location: ' . $_POST['goto']);
        exit();
    }

    session_start();
    if (isset($_SESSION['loggedIn']))
    {
        return databaseContainsAuthor($_SESSION['usuario'],$_SESSION['password']);
    }

}

function tieneTurnoIniciado($usuario, $password)
{
    $viva = new medoo([
// required
        'database_type' => 'mysql',
        'database_name' => 'VIVA',
        'server' => 'localhost',
        'username' => 'homestead',
        'password' => 'secret',
        'charset' => 'utf8',
        'port' => 3306,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);

    $turno=$viva->select("Asistencia(A)",["[>]Usuario(U)" => ["A.idUsuario"=>"idUsuario"]],"idAsistencia",["AND"=>["U.Nombre_usuario"=>$_SESSION["usuario"], "U.Password"=>$_SESSION["password"], "A.Confirmado"=>"Iniciado"]]);


    //$login=$viva->select("Usuario","idUsuario",["AND"=>["Nombre_usuario"=>$usuario,"Password"=>$password]]);

    //$idUsuario=$login[0]["idUsuario"];

    //$turno=$viva->count("Asistencia",["AND"=>["Confirmado"=>"Iniciado","idUsuario"=>$idUsuario]]);

    return $turno;


}

function databaseContainsAuthor($usuario, $password)
{

    $viva = new medoo([
// required
        'database_type' => 'mysql',
        'database_name' => 'VIVA',
        'server' => 'localhost',
        'username' => 'homestead',
        'password' => 'secret',
        'charset' => 'utf8',
        'port' => 3306,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);

    try
    {
        $login=$viva->count("Usuario",["AND"=>["Nombre_usuario"=>$usuario,"Password"=>$password, "Habilitado"=>"Si"]]);

    }
    catch (PDOException $e)
    {
        $error = 'Error al buscar usuario';
        //include 'error.html.php';
        exit();
    }
    if ($login > 0)
    {

        return TRUE;
    }
    else
    {
        return FALSE;
    }
}

function userHasRole($role)
{
    $viva = new medoo([
// required
        'database_type' => 'mysql',
        'database_name' => 'VIVA',
        'server' => 'localhost',
        'username' => 'homestead',
        'password' => 'secret',
        'charset' => 'utf8',
        'port' => 3306,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);

    try
    {
        $privilegio=$viva->count("Usuario",["AND"=>["Nombre_usuario"=>$_SESSION['usuario'],"Privilegio"=>$role]]);
    }
    catch (PDOException $e)
    {
        $error = 'Error al buscar privilegio';
        exit();
    }
    if ($privilegio > 0)
    {
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}