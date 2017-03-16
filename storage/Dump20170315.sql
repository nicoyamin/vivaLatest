-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: VIVA
-- ------------------------------------------------------
-- Server version	5.7.17-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Asistencia`
--

DROP TABLE IF EXISTS `Asistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Asistencia` (
  `idAsistencia` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) DEFAULT NULL,
  `idTurno` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `Hora_entrada` time NOT NULL,
  `Hora_salida` time NOT NULL,
  `Check_in` varchar(50) DEFAULT NULL,
  `Check_out` varchar(50) DEFAULT NULL,
  `Codigo` varchar(8) NOT NULL,
  `Confirmado` varchar(50) NOT NULL,
  PRIMARY KEY (`idAsistencia`),
  KEY `fk_Asistencia_Usuario1_idx` (`idUsuario`),
  KEY `fk_Asistencia_Turno1_idx` (`idTurno`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Asistencia`
--

LOCK TABLES `Asistencia` WRITE;
/*!40000 ALTER TABLE `Asistencia` DISABLE KEYS */;
INSERT INTO `Asistencia` VALUES (2,8,2,'2017-03-14','14:00:00','22:00:00','Pendiente','Pendiente','1D467EA5','Pendiente'),(3,6,3,'2017-03-14','22:00:00','06:00:00','Pendiente','Pendiente','5853B136','Pendiente');
/*!40000 ALTER TABLE `Asistencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Caja`
--

DROP TABLE IF EXISTS `Caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Caja` (
  `idCaja` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) DEFAULT NULL,
  `Fecha` datetime NOT NULL,
  `Tipo` varchar(45) DEFAULT NULL,
  `Concepto` int(11) DEFAULT NULL,
  `Debe` float DEFAULT NULL,
  `Haber` float DEFAULT NULL,
  `Observaciones` varchar(100) DEFAULT NULL,
  `Referencia` int(11) DEFAULT NULL,
  `idReferencia` varchar(30) DEFAULT NULL,
  `idTurno` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCaja`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Caja`
--

LOCK TABLES `Caja` WRITE;
/*!40000 ALTER TABLE `Caja` DISABLE KEYS */;
INSERT INTO `Caja` VALUES (1,1,'2017-03-14 16:17:58','Entrada',1,NULL,0,'',1,'1',0);
/*!40000 ALTER TABLE `Caja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Caja_Arqueo`
--

DROP TABLE IF EXISTS `Caja_Arqueo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Caja_Arqueo` (
  `idCaja_arqueo` int(11) NOT NULL AUTO_INCREMENT,
  `idTurno` int(11) DEFAULT NULL,
  `Fecha` datetime DEFAULT NULL,
  `Arqueo_sistema` float DEFAULT NULL,
  `Arqueo_empleado_efectivo` float DEFAULT NULL,
  `Arqueo_empleado_valores` float DEFAULT NULL,
  `Diferencia` float DEFAULT NULL,
  PRIMARY KEY (`idCaja_arqueo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Caja_Arqueo`
--

LOCK TABLES `Caja_Arqueo` WRITE;
/*!40000 ALTER TABLE `Caja_Arqueo` DISABLE KEYS */;
/*!40000 ALTER TABLE `Caja_Arqueo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Caja_comprobante`
--

DROP TABLE IF EXISTS `Caja_comprobante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Caja_comprobante` (
  `idComprobante` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) DEFAULT NULL,
  `Respalda_operacion` int(11) DEFAULT NULL,
  PRIMARY KEY (`idComprobante`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Caja_comprobante`
--

LOCK TABLES `Caja_comprobante` WRITE;
/*!40000 ALTER TABLE `Caja_comprobante` DISABLE KEYS */;
INSERT INTO `Caja_comprobante` VALUES (1,'Factura',1),(2,'Ticket Factura',1),(3,'Recibo de cobranza',2),(4,'Resumen de tarjeta',4),(5,'Resumen de Arqueo',5),(6,'Recibo de proveedor',6),(7,'Orden de pago de proveedor',6),(8,'Liquidacion de haberes',7),(9,'Recibo de sueldo',7),(10,'Boleta de deposito',8),(11,'Comprobante alivio de caja',3),(12,'Sin comprobante',9),(13,'Retiro para uso habitual',10);
/*!40000 ALTER TABLE `Caja_comprobante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Caja_comprobante_alivio`
--

DROP TABLE IF EXISTS `Caja_comprobante_alivio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Caja_comprobante_alivio` (
  `idCaja_comprobante_alivio` int(11) NOT NULL AUTO_INCREMENT,
  `Numero` int(11) DEFAULT NULL,
  `Observaciones` varchar(200) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  PRIMARY KEY (`idCaja_comprobante_alivio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Caja_comprobante_alivio`
--

LOCK TABLES `Caja_comprobante_alivio` WRITE;
/*!40000 ALTER TABLE `Caja_comprobante_alivio` DISABLE KEYS */;
/*!40000 ALTER TABLE `Caja_comprobante_alivio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Caja_comprobante_diario`
--

DROP TABLE IF EXISTS `Caja_comprobante_diario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Caja_comprobante_diario` (
  `idCaja_comprobante_diario` int(11) NOT NULL AUTO_INCREMENT,
  `idCaja_arqueo` int(11) DEFAULT NULL,
  `Numero` int(11) DEFAULT NULL,
  `Fecha` datetime DEFAULT NULL,
  `Observaciones` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idCaja_comprobante_diario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Caja_comprobante_diario`
--

LOCK TABLES `Caja_comprobante_diario` WRITE;
/*!40000 ALTER TABLE `Caja_comprobante_diario` DISABLE KEYS */;
/*!40000 ALTER TABLE `Caja_comprobante_diario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Caja_operacion`
--

DROP TABLE IF EXISTS `Caja_operacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Caja_operacion` (
  `idCaja_operacion` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) DEFAULT NULL,
  `Tipo` varchar(45) DEFAULT NULL,
  `Privilegio_necesario` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCaja_operacion`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Caja_operacion`
--

LOCK TABLES `Caja_operacion` WRITE;
/*!40000 ALTER TABLE `Caja_operacion` DISABLE KEYS */;
INSERT INTO `Caja_operacion` VALUES (1,'Venta al contado','Entrada Especial',1),(2,'Cobranza de ventas a cuenta','Entrada',1),(3,'Alivios de caja','Entrada',1),(4,'Cobranza debito/credito','Entrada',1),(5,'Arqueo de caja','Cierre',1),(6,'Pago a proveedores','Salida',2),(7,'Pago a empleados','Salida',2),(8,'Extraccion para depositos bancarios','Salida',2),(9,'Retiro para uso personal','Salida',3),(10,'Retiro para usos habituales','Salida',2);
/*!40000 ALTER TABLE `Caja_operacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Cliente`
--

DROP TABLE IF EXISTS `Cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cliente` (
  `idCliente` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(60) DEFAULT NULL,
  `Sitio_web` varchar(60) DEFAULT NULL,
  `Email` varchar(60) DEFAULT NULL,
  `Fecha_alta` date DEFAULT NULL,
  `idPersona` int(11) NOT NULL,
  `Habilitado` varchar(2) DEFAULT NULL,
  `Cuit_cuil` varchar(13) DEFAULT NULL,
  `Telefono` int(11) DEFAULT NULL,
  `Cuenta_corriente` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`idCliente`),
  KEY `fk_Cliente_Persona1_idx` (`idPersona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Cliente`
--

LOCK TABLES `Cliente` WRITE;
/*!40000 ALTER TABLE `Cliente` DISABLE KEYS */;
/*!40000 ALTER TABLE `Cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Compra`
--

DROP TABLE IF EXISTS `Compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Compra` (
  `idCompra` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha` date DEFAULT NULL,
  `Condiciones_pago` varchar(45) DEFAULT NULL,
  `Lugar_entrega` varchar(45) DEFAULT NULL,
  `Fecha_entrega` date DEFAULT NULL,
  `Enviar_por` varchar(45) DEFAULT NULL,
  `Estado` varchar(45) DEFAULT NULL,
  `idProveedor` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idCompra`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Compra`
--

LOCK TABLES `Compra` WRITE;
/*!40000 ALTER TABLE `Compra` DISABLE KEYS */;
INSERT INTO `Compra` VALUES (1,'2017-03-14','Efectivo','GNC','2017-03-31','A cargo del comprador','8','3'),(2,'2017-03-14','','','0000-00-00','','8','5'),(3,'2017-03-14','','','0000-00-00','','8','9'),(4,'2017-03-14','','','0000-00-00','','7','7'),(5,'2017-03-14','','','0000-00-00','','1','4'),(6,'2017-03-14','Cheque','GNC','2017-03-31','A cargo del comprador','3','6'),(7,'2017-03-14','','','0000-00-00','','1','3'),(8,'2017-03-14','','','0000-00-00','','1','4'),(9,'2017-03-14','','','0000-00-00','','1','6'),(10,'2017-03-14','','','0000-00-00','','1','7'),(11,'2017-03-14','','','0000-00-00','','1','5');
/*!40000 ALTER TABLE `Compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Compra_Producto`
--

DROP TABLE IF EXISTS `Compra_Producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Compra_Producto` (
  `idCompra` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `Cantidad` varchar(45) DEFAULT NULL,
  `Cantidad_pendiente` int(11) DEFAULT '0',
  PRIMARY KEY (`idCompra`,`idProducto`),
  KEY `fk_Compra_has_Producto_Producto1_idx` (`idProducto`),
  KEY `fk_Compra_has_Producto_Compra1_idx` (`idCompra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Compra_Producto`
--

LOCK TABLES `Compra_Producto` WRITE;
/*!40000 ALTER TABLE `Compra_Producto` DISABLE KEYS */;
INSERT INTO `Compra_Producto` VALUES (1,2,'50',50),(1,3,'50',50),(1,7,'50',50),(1,8,'50',50),(2,1,'100',100),(2,10,'100',100),(2,15,'100',100),(3,18,'200',200),(4,17,'150',0),(5,4,'200',200),(6,11,'30',30),(7,8,'500',500),(8,4,'25',25),(9,11,'75',75),(10,17,'750',750),(11,9,'50',50);
/*!40000 ALTER TABLE `Compra_Producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Compra_estado`
--

DROP TABLE IF EXISTS `Compra_estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Compra_estado` (
  `idCompra_estado` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) DEFAULT NULL,
  `Accion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idCompra_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Compra_estado`
--

LOCK TABLES `Compra_estado` WRITE;
/*!40000 ALTER TABLE `Compra_estado` DISABLE KEYS */;
INSERT INTO `Compra_estado` VALUES (1,'Cotizacion Generada','La cotizacion fue generada, pero no enviada. El stock de productos entrantes no se modifica.'),(2,'Cotizacion enviada','La cotizacion ha sido enviada al proveedor para ser completada. El stock entrante no se modifica.'),(3,'Orden de compra generada','La orden de compra se ha generado, y esta pendiente de ser enviada al proveedor. El stock entrante se modifica con las cantidades detalladas en la orden.'),(4,'Orden de compra enviada','La orden de compra se ha enviado para ser procesada por el proveedor.'),(5,'Compra en transito','La orden de compra ha sido procesada por el proveedor y se encuentra en camino a la direccion indicada.'),(6,'Compra recibida','La orden de compra se ha recibido, pero aun no se dieron de alta al stock los productos que la componen. El stock entrante permanece sin cambios.'),(7,'Compra cerrada','La orden de compra fue recibida y aprobada, y los productos en ella detallados fueron dados de alta. Se trasladan las cantidades especificadas en stock entrante a stock actual'),(8,'Cancelada','La orden de compra/cotizacion fue cancelada. ESTE PROCESO ES IRREVERSIBLE. Las cantidades detalladas en stock entrante correspondientes a esta orden de compra se quitan.'),(9,'Devuelta','La orden de compra fue devuelta por no cumplir alguna de las condiciones en ella especificada. ESTE PROCESO ES IRREVERSIBLE.Las cantidades detalladas se procesan como si fuera una orden cancelada. ');
/*!40000 ALTER TABLE `Compra_estado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Cuenta_corriente`
--

DROP TABLE IF EXISTS `Cuenta_corriente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cuenta_corriente` (
  `idCuenta_corriente` int(11) NOT NULL AUTO_INCREMENT,
  `idCliente` int(11) NOT NULL,
  `Tipo` varchar(45) NOT NULL,
  `Estado` varchar(45) DEFAULT NULL,
  `Fecha_apertura` date DEFAULT NULL,
  `Margen` float DEFAULT NULL,
  `Fecha_ultimo_movimiento` date DEFAULT NULL,
  `Balance` float DEFAULT NULL,
  PRIMARY KEY (`idCuenta_corriente`),
  KEY `fk_Cuenta_corriente_Proveedor1_idx` (`idCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Cuenta_corriente`
--

LOCK TABLES `Cuenta_corriente` WRITE;
/*!40000 ALTER TABLE `Cuenta_corriente` DISABLE KEYS */;
/*!40000 ALTER TABLE `Cuenta_corriente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Cuenta_corriente_movimientos`
--

DROP TABLE IF EXISTS `Cuenta_corriente_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cuenta_corriente_movimientos` (
  `idCuenta_corriente_movimientos` int(11) NOT NULL AUTO_INCREMENT,
  `idCuenta_corriente` int(11) NOT NULL,
  `Concepto` varchar(100) DEFAULT NULL,
  `Medio_pago` varchar(50) DEFAULT NULL,
  `Fecha` datetime DEFAULT NULL,
  `Debe` float DEFAULT NULL,
  `Haber` float DEFAULT NULL,
  `Saldo` float DEFAULT NULL,
  `Referencia` int(11) DEFAULT NULL,
  PRIMARY KEY (`idCuenta_corriente_movimientos`),
  KEY `fk_Cuenta_corriente_movimientos_Cuenta_corriente1_idx` (`idCuenta_corriente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Cuenta_corriente_movimientos`
--

LOCK TABLES `Cuenta_corriente_movimientos` WRITE;
/*!40000 ALTER TABLE `Cuenta_corriente_movimientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `Cuenta_corriente_movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Factura`
--

DROP TABLE IF EXISTS `Factura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Factura` (
  `idFactura` int(11) NOT NULL AUTO_INCREMENT,
  `idVenta` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `Fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Tipo` varchar(1) NOT NULL,
  PRIMARY KEY (`idFactura`),
  KEY `fk_Factura_Venta1_idx` (`idVenta`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Factura`
--

LOCK TABLES `Factura` WRITE;
/*!40000 ALTER TABLE `Factura` DISABLE KEYS */;
INSERT INTO `Factura` VALUES (1,1,1,'2017-03-14 16:17:58','');
/*!40000 ALTER TABLE `Factura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MovimientoBancario_Compra`
--

DROP TABLE IF EXISTS `MovimientoBancario_Compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MovimientoBancario_Compra` (
  `Movimiento_bancario_idMovimiento_bancario` int(11) NOT NULL,
  `Compra_idCompra` int(11) NOT NULL,
  `Importe` float DEFAULT NULL,
  PRIMARY KEY (`Movimiento_bancario_idMovimiento_bancario`,`Compra_idCompra`),
  KEY `fk_Movimiento_bancario_has_Compra_Compra1_idx` (`Compra_idCompra`),
  KEY `fk_Movimiento_bancario_has_Compra_Movimiento_bancario1_idx` (`Movimiento_bancario_idMovimiento_bancario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MovimientoBancario_Compra`
--

LOCK TABLES `MovimientoBancario_Compra` WRITE;
/*!40000 ALTER TABLE `MovimientoBancario_Compra` DISABLE KEYS */;
/*!40000 ALTER TABLE `MovimientoBancario_Compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MovimientoBancario_Venta`
--

DROP TABLE IF EXISTS `MovimientoBancario_Venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MovimientoBancario_Venta` (
  `Movimiento_bancario_idMovimiento_bancario` int(11) NOT NULL,
  `Venta_idVenta` int(11) NOT NULL,
  `Importe` float DEFAULT NULL,
  PRIMARY KEY (`Movimiento_bancario_idMovimiento_bancario`,`Venta_idVenta`),
  KEY `fk_Movimiento_bancario_has_Venta_Venta1_idx` (`Venta_idVenta`),
  KEY `fk_Movimiento_bancario_has_Venta_Movimiento_bancario1_idx` (`Movimiento_bancario_idMovimiento_bancario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MovimientoBancario_Venta`
--

LOCK TABLES `MovimientoBancario_Venta` WRITE;
/*!40000 ALTER TABLE `MovimientoBancario_Venta` DISABLE KEYS */;
/*!40000 ALTER TABLE `MovimientoBancario_Venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Movimiento_bancario`
--

DROP TABLE IF EXISTS `Movimiento_bancario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Movimiento_bancario` (
  `idMovimiento_bancario` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha` datetime DEFAULT NULL,
  `idMovimiento_bancario_tipo` int(11) NOT NULL,
  `Nro_cuenta` varchar(45) DEFAULT NULL,
  `Descripcion_cuenta` varchar(45) DEFAULT NULL,
  `Saldo` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idMovimiento_bancario`),
  KEY `fk_Movimiento_bancario_Movimiento_bancario_tipo1_idx` (`idMovimiento_bancario_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Movimiento_bancario`
--

LOCK TABLES `Movimiento_bancario` WRITE;
/*!40000 ALTER TABLE `Movimiento_bancario` DISABLE KEYS */;
/*!40000 ALTER TABLE `Movimiento_bancario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Movimiento_bancario_tipo`
--

DROP TABLE IF EXISTS `Movimiento_bancario_tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Movimiento_bancario_tipo` (
  `idMovimiento_bancario_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idMovimiento_bancario_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Movimiento_bancario_tipo`
--

LOCK TABLES `Movimiento_bancario_tipo` WRITE;
/*!40000 ALTER TABLE `Movimiento_bancario_tipo` DISABLE KEYS */;
/*!40000 ALTER TABLE `Movimiento_bancario_tipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Orden_de_compra`
--

DROP TABLE IF EXISTS `Orden_de_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Orden_de_compra` (
  `idOrden_de_compra` int(11) NOT NULL AUTO_INCREMENT,
  `idCompra` int(11) NOT NULL,
  `numero` int(11) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Estado` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idOrden_de_compra`),
  KEY `fk_Orden_de_compra_Compra1_idx` (`idCompra`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Orden_de_compra`
--

LOCK TABLES `Orden_de_compra` WRITE;
/*!40000 ALTER TABLE `Orden_de_compra` DISABLE KEYS */;
INSERT INTO `Orden_de_compra` VALUES (1,6,1,'2017-03-14','Activa'),(2,1,2,'2017-03-14','Inactiva'),(3,2,3,'2017-03-14','Inactiva'),(4,3,4,'2017-03-14','Inactiva'),(5,4,5,'2017-03-14','Cerrada');
/*!40000 ALTER TABLE `Orden_de_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Persona`
--

DROP TABLE IF EXISTS `Persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Persona` (
  `idPersona` int(11) NOT NULL AUTO_INCREMENT,
  `Apellido` varchar(45) NOT NULL,
  `Nombre` varchar(45) NOT NULL,
  `Documento` int(11) DEFAULT NULL,
  `Fecha_nacimiento` date DEFAULT NULL,
  `Direccion` varchar(100) DEFAULT NULL,
  `Ciudad` varchar(60) DEFAULT NULL,
  `Provincia` varchar(60) DEFAULT NULL,
  `Codigo_postal` int(11) DEFAULT NULL,
  `Email` varchar(60) DEFAULT NULL,
  `Telefono` varchar(45) DEFAULT NULL,
  `Celular` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idPersona`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Persona`
--

LOCK TABLES `Persona` WRITE;
/*!40000 ALTER TABLE `Persona` DISABLE KEYS */;
INSERT INTO `Persona` VALUES (30,'Lopez','Juan',11111111,'0000-00-00','La Rioja 146','Tucuman','Tucuman',4000,'','3885764523',''),(32,'Gutierrez','Jorge',11111111,'0000-00-00','','','',0,'jgutierrez@hotmail.com','',''),(33,'Perez','Pedro',12456369,'0000-00-00','','','',0,'','',''),(34,'Fernandez','Jose',10236587,'0000-00-00','','','',0,'','',''),(38,'Infante','Mercedes',12456987,'0000-00-00','','','',0,'','',''),(39,'Andrada','Maximiliano',12369855,'0000-00-00','','','',0,'','',''),(40,'Mora','Daniela',45123698,'1940-06-12','Catamarca 52 5A','San Juan','San Juan',1454,'','',''),(41,'Tonetti','Juan',32555698,'1990-05-12','Talitas 122','Salta','Salta',4120,'jtonetti@gmail.com','4221236','3812587414'),(42,'Juarez','Fernando',12369854,'0000-00-00','','','',0,'','',''),(43,'Gomez','Pedro',12456999,'1965-05-26','San Martin 122','Salta','Salta',3455,'pgomez@hotmail.com','',''),(44,'Morales','Gaston',12455788,'1990-02-12','San Martin 122','Salta','Salta',1234,'gmorales@yahoo.com','',''),(46,'Albornoz','Andres Mateo',12456788,'1985-05-12','San Martin 122','Salta','Salta',1000,'aalbornoz@hotmail.com','34523532',''),(47,'Aguirre','Juan',20555888,'1966-04-12','Florida 666','CABA','Buenos Aires',1234,'jaguirre@hotmail.com','4916607','3888764121'),(48,'Sanchez','Marta',12555698,'1945-12-02','','','',0,'','',''),(49,'Vegas','Juliana',30556874,'1988-04-12','','','',0,'','',''),(50,'Dominguez','Martin',35654125,'0000-00-00','','','',0,'','',''),(52,'Torres','Pedro',25879664,'1968-01-12','San Martin 122','Salta','Salta',3455,'ptorres@hotmail.com','',''),(53,'Tatu','Gaston',12457893,'1958-05-12','','','',0,'','42257892',''),(54,'Perez','Pedro',12456789,'1985-05-12','Las Heras 500','Rosario de la Frontera','Salta',4801,'pperez@gmail.com','4220284','3885478965'),(55,'Streep','Meryl',25789654,'1950-02-10','Mulholland Dr 123','LA','California',4512,'mstreep@hotmail.com','4578965','5478964556'),(56,'Lopez','Javier',20125478,'1990-03-15','','','',0,'','',''),(57,'Galvez','Federico',20123698,'1911-11-11','11 de Septiembre 345','Usuhaia','Tierra del Fuego',1234,'','',''),(58,'Jimenez','Ramon',23555874,'0000-00-00','','','',0,'','','');
/*!40000 ALTER TABLE `Persona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Producto`
--

DROP TABLE IF EXISTS `Producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Producto` (
  `idProducto` int(11) NOT NULL AUTO_INCREMENT,
  `idCategoria` int(11) NOT NULL,
  `Nombre_producto` varchar(45) NOT NULL,
  `Descripcion_producto` varchar(140) DEFAULT NULL,
  `Cantidad_unitaria_producto` float DEFAULT NULL,
  `Unidad_producto` varchar(45) DEFAULT NULL,
  `Precio_unitario_producto` float NOT NULL,
  `Perecedero` varchar(2) DEFAULT NULL,
  `Codigo_barras_producto` varchar(20) DEFAULT NULL,
  `idProveedor` int(11) NOT NULL,
  `Habilitado` varchar(2) NOT NULL,
  `Existencia_producto` int(11) DEFAULT NULL,
  `Stock_minimo_producto` int(11) DEFAULT NULL,
  `Stock_entrante_producto` int(11) DEFAULT '0',
  `Precio_venta_producto` float DEFAULT '0',
  PRIMARY KEY (`idProducto`),
  KEY `fk_Producto_Producto_categoria1_idx` (`idCategoria`),
  KEY `fk_Producto_Proveedor1_idx` (`idProveedor`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Producto`
--

LOCK TABLES `Producto` WRITE;
/*!40000 ALTER TABLE `Producto` DISABLE KEYS */;
INSERT INTO `Producto` VALUES (1,1,'Pepitos','Galletas dulces',200,'gr',15,'Si','7790580105966',5,'Si',10,50,270,100),(2,1,'Papas Lays','Papas fritas clasicas',39,'gr',22.5,'Si','7790580102354',3,'Si',290,20,250,15.56),(3,1,'Coca Cola','Gaseosa',500,'ml',12.75,'No','7790580107822',3,'Si',87,100,50,22.5),(4,4,'Aceite YPF','Aceite para motor',3,'litros',125.8,'Si','7790580104569',4,'Si',120,10,345,120),(7,1,'Alfajor Aguila','de Chocolate',80,'gr',25,'Si','7790580107785',3,'Si',280,50,65,50),(8,2,'Cafe Instantaneo','Cafe en granos',500,'gr',45.5,'Si','7790580104455',3,'Si',240,10,50,15),(9,7,'Revista Gente','82 Paginas',1,'Unidad',35,'No','7790580100021',5,'Si',20,0,0,0),(10,9,'Torpedo','Helado de agua',60,'gr',15,'Si','7790580101004',5,'Si',85,0,15,0),(11,9,'Gol','Helado en cono',150,'gr',25,'Si','7790580106089',6,'Si',70,0,350,0),(12,1,'Jugo En Polvo','Sabor Naranja',20,'gr',6.5,'Si','7790580105938',5,'Si',57,40,50,0),(13,1,'Jugo en Polvo','Sabor Limon',20,'gr',5.5,'Si','7790580106096',5,'No',300,50,0,0),(14,1,'Jugo en polvo','Sabor Anana',20,'gr',4,'Si','7790580104099',5,'Si',500,50,0,0),(15,1,'Chicle Beldent Menta','',15,'gr',10,'Si','7790580111111',5,'Si',50,10,0,0),(16,1,'Chicle Beldent Uva','',15,'gr',10,'Si','7790580105421',5,'No',100,10,0,0),(17,2,'Cerveza Quilmes','Rubia',1,'litro',15,'Si','7790580105432',7,'Si',300,20,0,0),(18,1,'Pan Lactal','',600,'gr',10,'Si','7790580100445',9,'Si',200,50,0,0);
/*!40000 ALTER TABLE `Producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Producto_categoria`
--

DROP TABLE IF EXISTS `Producto_categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Producto_categoria` (
  `idCategoria` int(11) NOT NULL AUTO_INCREMENT,
  `Categoria_nombre` varchar(45) NOT NULL,
  `Categoria_descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idCategoria`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Producto_categoria`
--

LOCK TABLES `Producto_categoria` WRITE;
/*!40000 ALTER TABLE `Producto_categoria` DISABLE KEYS */;
INSERT INTO `Producto_categoria` VALUES (1,'Comestibles','Golosinas, helados, sandwiches'),(2,'Bebidas','Gaseosas, Agua, Cafe, hielo, etc.'),(3,'Limpieza','Limpiador de piso, escobas, haraganes, detergente,etc.'),(4,'Automovil','Aceite, perfumes, etc.'),(5,'Combustibles','GNC y similares'),(6,'Higiene','Desodorantes, jabon, shampoo'),(7,'Libros y Revistas',NULL),(9,'Helados','Arcor y frigor'),(10,'Panaderia','Bizcochos y facturas'),(11,'Fiambre',''),(12,'Cigarrillos','');
/*!40000 ALTER TABLE `Producto_categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Proveedor`
--

DROP TABLE IF EXISTS `Proveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Proveedor` (
  `idProveedor` int(11) NOT NULL AUTO_INCREMENT,
  `Proveedor_nombre` varchar(60) DEFAULT NULL,
  `Nombre_representante` varchar(45) DEFAULT NULL,
  `Sitio_web` varchar(60) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Telefono` varchar(45) DEFAULT NULL,
  `Fecha_alta` date DEFAULT NULL,
  `Representante` int(11) NOT NULL,
  `Habilitado` varchar(2) DEFAULT NULL,
  `Cuit_cuil` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idProveedor`),
  KEY `fk_Proveedor_Persona1_idx` (`Representante`),
  CONSTRAINT `ProveedorPersona` FOREIGN KEY (`Representante`) REFERENCES `Persona` (`idPersona`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Proveedor`
--

LOCK TABLES `Proveedor` WRITE;
/*!40000 ALTER TABLE `Proveedor` DISABLE KEYS */;
INSERT INTO `Proveedor` VALUES (3,'Cabrales',NULL,'www.cafecabrales.com','ccabrales@hotmail.com','4220280','2016-10-12',38,'Si','20375086094'),(4,'Gasnor',NULL,'www.gasnor.com.ar','gasnor@gmail.com','4220280','2016-10-12',39,'Si','44646545646'),(5,'Arcor',NULL,'www.arcor.com.ar','arcor@yahoo.com','4000001','2017-03-14',40,'Si','45646546'),(6,'Frigor',NULL,'www.frigor.com.ar','frigorargentina@frigor.com','388425147','2016-11-03',41,'Si','123974536'),(7,'Quilmes',NULL,'www.quilmes.com.ar','quilmes@quilmes.com','01145247894','2016-11-03',42,'Si','95142316'),(8,'Noganet',NULL,'noganet.com.ar','noganet@gmail.com','111111111','2017-02-22',44,'Si','12025896544'),(9,'Panaderia napoleon',NULL,'','pnapoleon@gmail.com','4225879','2017-03-10',52,'Si','2365478984'),(10,'Epuyen',NULL,'www.epuyen.com.ar','epuyen@yahoo.com','42258964','2017-03-14',57,'Si','12569874545'),(11,'Limpimax',NULL,'','','458795445','2017-03-14',58,'Si','12365477899');
/*!40000 ALTER TABLE `Proveedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Recibo`
--

DROP TABLE IF EXISTS `Recibo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recibo` (
  `idRecibo` int(11) NOT NULL AUTO_INCREMENT,
  `Numero` int(11) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Observaciones` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idRecibo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Recibo`
--

LOCK TABLES `Recibo` WRITE;
/*!40000 ALTER TABLE `Recibo` DISABLE KEYS */;
/*!40000 ALTER TABLE `Recibo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Stock_concepto`
--

DROP TABLE IF EXISTS `Stock_concepto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Stock_concepto` (
  `idStock_concepto` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idStock_concepto`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Stock_concepto`
--

LOCK TABLES `Stock_concepto` WRITE;
/*!40000 ALTER TABLE `Stock_concepto` DISABLE KEYS */;
INSERT INTO `Stock_concepto` VALUES (1,'Devolucion del cliente'),(2,'Cancelacion del cliente'),(3,'Ajuste de Entrada'),(4,'Compra'),(5,'Stock Inicial'),(6,'Cancelacion al proveedor'),(7,'Ajuste de Salida'),(8,'Mermas'),(9,'Perdidas'),(10,'Ventas'),(11,'Devolucion al proveedor'),(12,'Cambio de ubicacion');
/*!40000 ALTER TABLE `Stock_concepto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Stock_movimientos`
--

DROP TABLE IF EXISTS `Stock_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Stock_movimientos` (
  `idStock_movimientos` int(11) NOT NULL AUTO_INCREMENT,
  `idProducto` int(11) NOT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Fecha` datetime NOT NULL,
  `idStock_tipo_movimientos` int(11) NOT NULL,
  `idStock_concepto` int(11) NOT NULL,
  `Observaciones` varchar(250) DEFAULT NULL,
  `Stock_luego` int(11) DEFAULT NULL,
  `Stock_antes` int(11) DEFAULT NULL,
  PRIMARY KEY (`idStock_movimientos`),
  KEY `fk_Stock_movimientos_Stock_productos1_idx` (`idProducto`),
  KEY `fk_Stock_movimientos_Stock_tipo_movimientos1_idx` (`idStock_tipo_movimientos`),
  KEY `fk_Stock_movimientos_Stock_concepto1_idx` (`idStock_concepto`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Stock_movimientos`
--

LOCK TABLES `Stock_movimientos` WRITE;
/*!40000 ALTER TABLE `Stock_movimientos` DISABLE KEYS */;
INSERT INTO `Stock_movimientos` VALUES (1,0,0,'2017-03-14 16:17:58',2,10,NULL,NULL,NULL),(2,0,0,'2017-03-14 16:17:58',2,10,NULL,NULL,NULL),(3,17,50,'2017-03-14 18:37:46',1,4,NULL,200,150),(4,17,50,'2017-03-14 18:38:03',1,4,NULL,250,200),(5,17,50,'2017-03-14 18:38:11',1,4,NULL,300,250);
/*!40000 ALTER TABLE `Stock_movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Stock_tipo_movimientos`
--

DROP TABLE IF EXISTS `Stock_tipo_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Stock_tipo_movimientos` (
  `idStock_tipo_movimientos` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idStock_tipo_movimientos`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Stock_tipo_movimientos`
--

LOCK TABLES `Stock_tipo_movimientos` WRITE;
/*!40000 ALTER TABLE `Stock_tipo_movimientos` DISABLE KEYS */;
INSERT INTO `Stock_tipo_movimientos` VALUES (1,'Entrada'),(2,'Salida'),(3,'Traslado');
/*!40000 ALTER TABLE `Stock_tipo_movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Turno`
--

DROP TABLE IF EXISTS `Turno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Turno` (
  `idTurno` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion_turno` varchar(45) DEFAULT NULL,
  `Cant_horas_turno` int(11) DEFAULT NULL,
  `Entrada_turno` time NOT NULL,
  `Salida_turno` time NOT NULL,
  PRIMARY KEY (`idTurno`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Turno`
--

LOCK TABLES `Turno` WRITE;
/*!40000 ALTER TABLE `Turno` DISABLE KEYS */;
INSERT INTO `Turno` VALUES (1,'MANIANA',8,'06:00:00','14:00:00'),(2,'TARDE',8,'14:00:00','22:00:00'),(3,'NOCHE',8,'22:00:00','06:00:00');
/*!40000 ALTER TABLE `Turno` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Usuario`
--

DROP TABLE IF EXISTS `Usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Usuario` (
  `idUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `idPersona` int(11) NOT NULL,
  `Nombre_usuario` varchar(45) NOT NULL,
  `Password` varchar(45) NOT NULL,
  `Privilegio` int(11) NOT NULL,
  `Fecha_creacion` date DEFAULT NULL,
  `Habilitado` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  KEY `UsuarioPersona_idx` (`idPersona`),
  CONSTRAINT `UsuarioPersona` FOREIGN KEY (`idPersona`) REFERENCES `Persona` (`idPersona`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Usuario`
--

LOCK TABLES `Usuario` WRITE;
/*!40000 ALTER TABLE `Usuario` DISABLE KEYS */;
INSERT INTO `Usuario` VALUES (4,47,'admin','db43b86da58631629adada27f1db5841',3,'2017-03-14','Si'),(6,54,'pperez','d02cad1698609467b28cf3b954ea785f',1,'2017-03-14','Si'),(7,55,'mstreep','524d00b0106182b6baf088364820a7b9',2,'2017-03-14','Si'),(8,56,'jlopez','d3324024c5bf79858cee2fecd1120d86',1,'2017-03-14','Si');
/*!40000 ALTER TABLE `Usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Venta`
--

DROP TABLE IF EXISTS `Venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Venta` (
  `idVenta` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha` datetime DEFAULT NULL,
  `Importe` float DEFAULT NULL,
  `saldoImporte` float DEFAULT NULL,
  `Forma_pago` varchar(100) DEFAULT NULL,
  `Referencia_pago` varchar(45) DEFAULT '-',
  `Estado` varchar(45) DEFAULT NULL,
  `Iva_cliente` varchar(45) DEFAULT NULL,
  `idCliente` int(11) NOT NULL,
  PRIMARY KEY (`idVenta`),
  KEY `fk_Venta_Cliente1_idx` (`idCliente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Venta`
--

LOCK TABLES `Venta` WRITE;
/*!40000 ALTER TABLE `Venta` DISABLE KEYS */;
INSERT INTO `Venta` VALUES (1,'2017-03-14 16:17:58',NULL,0,NULL,'-','Cerrada',NULL,0);
/*!40000 ALTER TABLE `Venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Venta_Producto`
--

DROP TABLE IF EXISTS `Venta_Producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Venta_Producto` (
  `idVenta` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Precio` float DEFAULT NULL,
  PRIMARY KEY (`idVenta`,`idProducto`),
  KEY `fk_Venta_has_Producto_Producto1_idx` (`idProducto`),
  KEY `fk_Venta_has_Producto_Venta1_idx` (`idVenta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Venta_Producto`
--

LOCK TABLES `Venta_Producto` WRITE;
/*!40000 ALTER TABLE `Venta_Producto` DISABLE KEYS */;
INSERT INTO `Venta_Producto` VALUES (1,0,0,NULL);
/*!40000 ALTER TABLE `Venta_Producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'VIVA'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `actualizarEstadoAsistencia` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8 */ ;;
/*!50003 SET character_set_results = utf8 */ ;;
/*!50003 SET collation_connection  = utf8_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = '-03:00' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`homestead`@`%`*/ /*!50106 EVENT `actualizarEstadoAsistencia` ON SCHEDULE EVERY 8 HOUR STARTS '2016-11-01 15:00:00' ENDS '2021-11-01 07:00:00' ON COMPLETION PRESERVE ENABLE DO BEGIN
      
		SET @turno =(select idTurno from Asistencia where Fecha=CURDATE() and CURTIME() between Hora_entrada AND Hora_salida);
		IF (@turno=1) THEN
			UPDATE Asistencia SET Confirmado="Ausente" WHERE idTurno=3 AND FECHA=(CURDATE()- interval 1 DAY) AND Check_in="Pendiente" AND Check_out="Pendiente";
            UPDATE Asistencia SET Confirmado="Vencido" WHERE idTurno=3 AND FECHA=(CURDATE()- interval 1 DAY) AND Check_in !="Pendiente" AND Check_out="Pendiente" AND Confirmado="Iniciado";
        ELSEIF(@turno=2) THEN
			UPDATE Asistencia SET Confirmado="Ausente" WHERE idTurno=1 AND FECHA=CURDATE() AND Check_in="Pendiente" AND Check_out="Pendiente";
            UPDATE Asistencia SET Confirmado="Vencido" WHERE idTurno=1 AND FECHA=CURDATE() AND Check_in !="Pendiente" AND Check_out="Pendiente" AND Confirmado="Iniciado";
		ELSEIF(@turno=3) THEN
			UPDATE Asistencia SET Confirmado="Ausente" WHERE idTurno=2 AND FECHA=CURDATE() AND Check_in="Pendiente" AND Check_out="Pendiente";
            UPDATE Asistencia SET Confirmado="Vencido" WHERE idTurno=2 AND FECHA=CURDATE() AND Check_in !="Pendiente" AND Check_out="Pendiente" AND Confirmado="Iniciado";
        END IF;    
        
		
      END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
/*!50106 DROP EVENT IF EXISTS `turnos_del_dia` */;;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8 */ ;;
/*!50003 SET character_set_results = utf8 */ ;;
/*!50003 SET collation_connection  = utf8_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`homestead`@`%`*/ /*!50106 EVENT `turnos_del_dia` ON SCHEDULE EVERY 1 DAY STARTS '2016-10-14 00:00:00' ON COMPLETION NOT PRESERVE DISABLE DO BEGIN
	
		-- copy deleted posts
		INSERT INTO Asistencia (idTurno, Fecha, Hora_entrada, Hora_salida, Codigo, Confirmado)
        VALUES (1, CURDATE(), 
        (SELECT Entrada_turno from Turno where idTurno=1),
        (SELECT Salida_turno from Turno where idTurno=1), 
        (select lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0)),
        "No");
        
        INSERT INTO Asistencia (idTurno, Fecha, Hora_entrada, Hora_salida, Codigo, Confirmado)
        VALUES (2, CURDATE(), 
        (SELECT Entrada_turno from Turno where idTurno=2),
        (SELECT Salida_turno from Turno where idTurno=2), 
        (select lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0)),
        "No");
        
        INSERT INTO Asistencia (idTurno, Fecha, Hora_entrada, Hora_salida, Codigo, Confirmado)
        VALUES (3, CURDATE(), 
        (SELECT Entrada_turno from Turno where idTurno=3),
        (SELECT Salida_turno from Turno where idTurno=3), 
        (select lpad(conv(floor(rand()*pow(36,8)), 10, 36), 8, 0)),
        "No");
        

	    

	END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'VIVA'
--
/*!50003 DROP PROCEDURE IF EXISTS `pagoCuentaCorriente` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`homestead`@`%` PROCEDURE `pagoCuentaCorriente`(IN importeP FLOAT, IN cliente INT, IN reciboId INT, IN medioPago VARCHAR(20))
BEGIN
    /*Obtener id de cuenta corriente del cliente*/
    SET @idCta=0;
    SELECT idCuenta_corriente INTO @idCta FROM Cuenta_corriente WHERE idCliente=cliente;
    /*Obtener el saldo anterioir de la cuenta corriente */
    SET @saldoAnterior=0;
    SELECT Saldo INTO @saldoAnterior FROM Cuenta_corriente_movimientos WHERE idCuenta_corriente=@idCta ORDER BY idCuenta_corriente_movimientos DESC LIMIT 1;
    /* Insertar el nuevo registro en cuenta corriente movimientos*/
    INSERT INTO Cuenta_corriente_movimientos 
    (idCuenta_corriente,Concepto, Medio_pago, Fecha, Debe, Haber, Saldo, Referencia)
    VALUES
    (@idCta,"Pago",medioPago,NOW(),0,importeP,@saldoAnterior+importeP,reciboId);
    /*Obtener valor actualizado de saldo para registrar en tabla cuenta corriente */
    SELECT Saldo INTO @saldoAnterior FROM Cuenta_corriente_movimientos WHERE idCuenta_corriente=@idCta ORDER BY idCuenta_corriente_movimientos DESC LIMIT 1;
    /* Actualizar la tabla cuenta corriente del cliente con balance y fecha ultuimo movimiento*/
    UPDATE Cuenta_corriente SET Balance=@saldoAnterior, Fecha_ultimo_movimiento=CURDATE() WHERE idCliente=cliente;
    
    /* Iterar en la tabla venta, restando de las deudas el importe abonado, cerrando las ventas segun corresponda */
    SET @saldo=0;
    SET @deuda=0;
    SELECT COUNT(idVenta) INTO @deuda FROM Venta WHERE Estado="Saldo Pendiente" AND idCliente=cliente;
	WHILE importeP>0 AND @deuda>0 DO
		
        SELECT saldoImporte INTO @saldo FROM Venta WHERE Estado="Saldo Pendiente" AND idCliente=cliente order by Fecha, idVenta ASC LIMIT 1;
		
        UPDATE Venta SET
        Estado=CASE WHEN importeP-saldoImporte>=0 THEN "Cerrada" ELSE "Saldo Pendiente" END,
		saldoImporte=CASE WHEN importeP-saldoImporte>=0 THEN 0 ELSE saldoImporte-importeP END
		WHERE Estado="Saldo Pendiente"
		AND idCliente=cliente
		order by Fecha, idVenta ASC LIMIT 1;
        
       SET importeP=importeP-@saldo;
       SET @deuda=@deuda-1;
    
    END WHILE;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `prueba` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`homestead`@`%` PROCEDURE `prueba`()
BEGIN
	UPDATE Cliente SET Sitio_web="esteticaSanchez.com" WHERE idCliente=3;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-03-15 19:40:54
