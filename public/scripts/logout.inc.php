

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="../index.php">VIVA</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">RRHH <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="../gestionUsuarios.php">Gestion Usuarios</a></li>
                        <li><a href="../asistencia.php">Asistencia</a></li>
                        <li><a href="../gestionTurnos.php">Gestion Turnos</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Compras <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="../gestionProductos.php">Gestion Productos</a></li>
                        <li><a href="../gestionProveedores.php">Gestion Proveedores</a></li>
                        <li><a href="../nuevaCompraBackup.php">Nueva Compra</a></li>
                         <li><a href="../gestionCompras.php">Gestion Compras</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Ventas <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="../nuevaVenta.php">Nueva Venta</a></li>
                        <li><a href="../gestionVentas.php">Gestion Ventas</a></li>
                        <li><a href="../gestionClientes.php">Gestion Clientes</a></li>
                        <li><a href="../gestionCuentaCorriente.php">Gestion Cuentas Corrientes</a></li>
                        <li><a href="../gestionPrecios.php">Gestion Precios de Venta al Publico</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Stock <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="../gestionStock.php">Gestion Stock</a></li>
                        <li><a href="../historialStock.php">Historial de movimientos</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Caja <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="../historialCaja.php">Historial de Movimientos de Caja</a></li>
                        <li><a href="../historialCierreCaja.php">Historial de Cierre de Caja</a></li>
                        <li><a href="../entradaCaja.php">Registro Entradas</a></li>
                        <li><a href="../salidaCaja.php">Registro Salidas</a></li>
                    </ul>
                </li>

                <label><?php echo "Sesion de ".$_SESSION["usuario"]?></label>
                <form action="" method="post" class="pull-right">
                    <div>
                        <input type="hidden" name="action" value="logout">
                        <input type="hidden" name="goto" value="../index.php">
                        <input type="submit" value="Cerrar Sesion">
                    </div>
                </form>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

