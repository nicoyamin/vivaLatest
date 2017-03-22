<?php

use Particle\Validator\Validator;
use Particle\Validator\ValidationResult;

require_once '../vendor/autoload.php';
require_once  'acceso.inc.php';
include 'scripts/db.inc.php';

use JasperPHP\JasperPHP;

if (!userIsLoggedIn())
{
    include 'login.php';
    exit();
}
if (!userHasRole('1') && !userHasRole('2') && !userHasRole('3'))
{
    $error = 'Solo administradores pueden acceder a esta pagina';
    include 'accesoDenegado.php';
    exit();
}

//$turno=$viva->select("Asistencia(A)",["[>]Usuario(U)" => ["A.idUsuario"=>"idUsuario"]],"idAsistencia",["AND"=>["U.Nombre_usuario"=>$_SESSION["usuario"], "U.Password"=>$_SESSION["password"], "A.Confirmado"=>"Iniciado"]]);

//dump($_SESSION);

$usuario = $viva->select("Usuario", ["idUsuario", "privilegio"], ["AND" => ["Nombre_usuario" => $_SESSION["usuario"], "Password" => $_SESSION["password"]]]);
$turnoIniciado = $viva->select("Asistencia", "*", ["AND" => ["idUsuario" => $usuario[0]["idUsuario"],"Check_in[<>]"=>"Pendiente","Check_out"=>"Pendiente","Confirmado"=>"Iniciado"]]);

if(isset($_SESSION["idTurno"]))
{
    $haber=$viva->sum("Caja","Haber",["idTurno"=>$_SESSION["idTurno"]]);
    $debe=$viva->sum("Caja","Debe",["idTurno"=>$_SESSION["idTurno"]]);
}


//dump($debe,$haber);

$flagAlerta=0;//No mostrar ningun mensaje de alerta

//dump($proxTurno);

if(empty($turnoIniciado)) {
    $proxTurno = $viva->select("Asistencia", ["idAsistencia","Codigo", "Hora_entrada", "Hora_salida", "Confirmado"], ["AND" => ["Fecha" => date('Y-m-d'), "idUsuario" => $usuario[0]["idUsuario"], "Check_in" => "Pendiente", "Check_out" => "Pendiente", "Confirmado" => "Pendiente"]]);

//dump($proxTurno);

    if (empty($proxTurno)) {
        $mensajeTurno = "Hoy no cuenta con un turno asignado, fue confirmado o expiro";
        $flagTurno = 0; //Sin turno
    } elseif (date("H:i:s") < $proxTurno[0]["Hora_entrada"]) {
        $mensajeTurno = "Su turno comienza a Hs. " . $proxTurno[0]["Hora_entrada"] . ". A partir de ese horario podra iniciarlo";
        $flagTurno = 0;
    } elseif (date("H:i:s") > $proxTurno[0]["Hora_salida"] && $proxTurno[0]["Hora_salida"] != "06:00:00") {
        $mensajeTurno = "Su turno finalizo a Hs. " . $proxTurno[0]["Hora_salida"] . ". Su estado es " . $proxTurno[0]["Confirmado"];
        $flagTurno = 0;
    } else {
        $mensajeTurno = "Su turno comienza a Hs. " . $proxTurno[0]["Hora_entrada"] . " y finaliza a Hs. " . $proxTurno[0]["Hora_salida"];
        $flagTurno = 1; //Con turno asignado y si el usuario ingresa en una horario entre los estipulados por el turno que le corresponde, puede mostrar boton para iniciar
    }
}

else
{
    $mensajeTurno = "Usted inicio su turno a Hs. ".$turnoIniciado[0]["Check_in"].". Su turno finaliza a Hs. ".$turnoIniciado[0]["Hora_salida"];
    //$_SESSION["codigo"]=$turnoIniciado[0]['Codigo'];//Guardar Codigo de confirmacion de turno en variable de sesion
    $flagTurno = 2; //Turno iniciado. Puede mostrar boton de finalizar turno
}

if (isset($_POST['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    //dump($_POST);
    if ($flagTurno == 1) {
        $idTurno = $proxTurno[0]['idAsistencia'];

        $db = new Viva\BaseDatos($viva);
        date_default_timezone_set("America/Argentina/Buenos_Aires");

        $turno = array("Check_in" => date('H:i:s'), "Confirmado" => "Iniciado");

        $db->actualizar('Asistencia', $turno, "idAsistencia=$idTurno");

        $_SESSION["turno"] = "Iniciado";
        $_SESSION["idTurno"] = $idTurno;
        //$mensajeTurno = "Usted inicio su turno a Hs. " . date("H:i:s") . ". Su turno finaliza a Hs. " . $proxTurno[0]["Hora_salida"];
        //$_SESSION["codigo"]=$proxTurno[0]['Codigo'];//Guardar Codigo de confirmacion de turno en variable de sesion
        //$flagTurno = 2;
        header("Refresh:0");
    }
}

if(isset($_POST['btnCierre']) && $_SERVER['REQUEST_METHOD'] == 'POST')
{
    //dump($debe,$haber,$_SESSION,$_POST);
        if($flagTurno==2 and $_POST["codigo"]==$turnoIniciado[0]["Codigo"])
        {
            $db = new Viva\BaseDatos($viva);
            date_default_timezone_set("America/Argentina/Buenos_Aires");

            $jasper = new JasperPHP;

            $database = [
                'driver' => 'mysql',
                'username' => 'homestead',
                'password'=> 'secret',
                'database'=> 'VIVA',
                'host' => '127.0.0.1',
                'port' => '3306'
            ];

            //inicializar y asignar variables que seran usadas en el reporte como parametros
            $idTurno = $turnoIniciado[0]['idAsistencia'];

            if(!isset($_POST["observaciones"])) $observaciones="";
            else $observaciones=$_POST["observaciones"];

            $nroComprobante = $viva->select("Caja_comprobante_diario", ["Numero"], ["Numero[>=]" => 1, "ORDER" => ["Numero" => "DESC"], "LIMIT" => 1]);
            if ($nroComprobante != null) $nroComprobante = $nroComprobante[0]["Numero"] + 1;
            else $nroComprobante = 1;

            if($turnoIniciado[0]["idTurno"]==1) $turno="MAÑANA";
            elseif($turnoIniciado[0]["idTurno"]==2) $turno="TARDE";
            else $turno="NOCHE";

            $usuario = new Viva\Usuario($viva);
            $nombre=$usuario->selectNombreUsuarioPassword($viva,$_SESSION["usuario"],$_SESSION["password"]);
            $nombre=$nombre[0]["Nombre"]." ".$nombre[0]["Apellido"];

            $nombreReporte="Cierre_de_caja_numero_".$nroComprobante;

            $jasper->process(
            // Ruta y nombre de archivo de entrada del reporte
                'reports/cierres/cierreCaja.jasper',
                'reports/cierres/'.$nombreReporte, // Ruta y nombre de archivo de salida del reporte (sin extensi�n)
                array('pdf'), // Formatos de salida del reporte
                array('nombre' => $nombre,
                    'turno'=>$turno,
                    'numero'=>$nroComprobante,
                    'fecha'=>date("d/m/Y"),
                    'observaciones'=>$observaciones,
                    'idTurno'=>$idTurno,
                    'importeEfectivo'=>$_POST["efectivo"],
                    'importeValores'=>$_POST["valores"]
                ) // Par�metros del reporte
                ,$database
            )->execute();


            //Las siguientes tablas se ven afectadas: Asistencia(actualizar), caja_arqueo(insertar), caja_comprobante_diario(insertar)
            $turno = array("Check_out" => date('H:i:s'), "Confirmado" => "Confirmado");

            //$rendicion=$haber-$debe;
            $rendicion=$debe-$haber;
            $diferencia=($_POST["efectivo"]+$_POST["valores"])-$rendicion;
            $db->actualizar('Asistencia', $turno, "idAsistencia=$idTurno");

            $idCajaArqueo=$viva->insert("Caja_Arqueo",["idTurno"=>$idTurno,
                "Fecha"=>date("Y-m-d H:i:s"),
                "Arqueo_sistema"=>$rendicion,
                "Arqueo_empleado_efectivo"=>$_POST["efectivo"],
                "Arqueo_empleado_valores"=>$_POST["valores"],
                "Diferencia"=>$diferencia
            ]);

            $viva->insert("Caja_comprobante_diario",[
                "idCaja_arqueo"=>$idCajaArqueo,
                "Numero"=>$nroComprobante,
                "Fecha"=>date("Y-m-d H:i:s"),
                "Observaciones"=>$observaciones
            ]);

            echo "<script type='text/javascript'>alert('Usted ha confirmado su turno con exito');</script>";

            $_SESSION["turno"]="Sin iniciar";

            $nombre=$nombreReporte.".pdf";

            $ruta='reports/cierres/'.$nombre;

            $atras="index.php";
            header("Location: ventanaDescarga.php?nombre=$nombre&ruta=$ruta&atras=$atras");
        }

        elseif($flagTurno==2 and $_POST["codigo"]!=$turnoIniciado[0]["Codigo"])
        {
            echo "<script type='text/javascript'>alert('El codigo es incorrecto');</script>";
        }

}



?>

<!doctype html>
<html class="" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Pagina Principal/CMS</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/vivaStyle.css" media="screen" />
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->


        <?php include 'scripts/logout.inc.php'; ?>

        <div class="jumbotron">
            <h1>Bienvenido, <?php echo($_SESSION["usuario"]);?>!</h1>
            <p><?php echo $mensajeTurno;?></p>

            <?php if($flagTurno==1) : ?>
                <form action="" method="post"> <input type="submit" name="submit" class="btn btn-primary" value="Iniciar Turno"></form>
            <?php elseif($flagTurno==2) : ?>
                <button data-toggle="modal" data-target="#modalFinalizar" class="btn btn-primary" value="Finalizar Turno" action="">Finalizar Turno </button>
            <?php endif; ?>

        </div>




        <div class="modal fade" id="modalFinalizar" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                        <h4 class="modal-title" id="myModalLabel">Cierre de Caja</h4>
                    </div>
                    <form action="" method="post" id="formCierre">
                        <div>
                            <label>Recaudado Efectivo: $<input type="number" step="0.01" name="efectivo" id="efectivo" value="0.00" min="0"> </label>
                            <label>Recaudado Comprobantes: $<input type="number" step="0.01" name="valores" id="valores" value="0.00" min="0"> </label>
                        </div>
                        <div><label name="total" id="total">Total: $0.00</label></div>
                        <div>
                            <label>Observaciones: </label>

                            <div><textarea name="observaciones" id="observaciones" disabled></textarea></div>

                        </div>
                        <div>
                            <label>Codigo de Confirmacion: <input type="text" name="codigo"></label>
                            <p>Una vez finalizado su turno, pida el codigo de confirmacion a su supervisor</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            <input type="submit" value="Cierre de caja" name="btnCierre">
                    </form>
                    </div>
                </div>
            </div>
        </div>


        <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.12.0.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
        <script src="js/bootstrap.min.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>

        <script>
            $(document).ready( function () {

                function sumaTotal()
                {
                    var total=(parseFloat($('#efectivo').val())+parseFloat($('#valores').val())).toFixed(2);
                    //var valores=$('#valores').val();

                    $('#total').text("Total: $"+total);
                }


                $('#efectivo').change(function(){

                    sumaTotal();

                });

                $('#valores').change(function(){

                    sumaTotal();

                });

                $('#formCierre').on('submit',function(){

                    var debe=<?php if (isset($debe)) echo $debe; else echo 0; ?>;
                    var haber=<?php if(isset($haber)) echo $haber; else echo 0; ?>;
                    //var totalSistema=Math.abs(haber-debe);
                    var totalSistema=debe-haber;

                    var totalEmpleado=(parseFloat($('#efectivo').val())+parseFloat($('#valores').val())).toFixed(2);

                    //En caso de faltante o sobrante de caja, se tiene una tolerancia de $10
                    if(totalSistema-10>totalEmpleado && $('#observaciones').val()=="")
                    {
                        alert("Faltante de Caja. Por favor corrija el importe o complete el campo observaciones con la razon del faltante");
                        $('#observaciones').prop('disabled', false);
                        return false;

                    }
                    else if(totalSistema+10<totalEmpleado && $('#observaciones').val()=="")
                    {
                        alert("Sobrante de caja. Por favor corrija el importe o complete el campo observaciones con la razon del sobrante");
                        $('#observaciones').prop('disabled', false);
                        return false;
                    }
                    else
                    {
                        if(!confirm("Esto cerrara su turno y creara el comprobante de cierre de caja. Desea continuar?")) return false;
                        else return true;
                    }

                });

            } );

        </script>
    </body>
</html>
