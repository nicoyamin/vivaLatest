-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema VIVA
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema VIVA
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `VIVA` DEFAULT CHARACTER SET utf8 ;
USE `VIVA` ;

-- -----------------------------------------------------
-- Table `VIVA`.`Persona`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Persona` (
  `idPersona` INT NOT NULL,
  `Apellido` VARCHAR(45) NOT NULL,
  `Nombre` VARCHAR(45) NOT NULL,
  `Documento` INT NULL,
  `Fecha_nacimiento` DATE NULL,
  `Direccion` VARCHAR(100) NULL,
  `Ciudad` VARCHAR(60) NULL,
  `Provincia` VARCHAR(60) NULL,
  `Codigo_postal` INT NULL,
  `Email` VARCHAR(60) NULL,
  `Telefono` VARCHAR(45) NULL,
  `Celular` VARCHAR(45) NULL,
  PRIMARY KEY (`idPersona`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Usuario` (
  `idUsuario` INT NOT NULL,
  `idPersona` INT NOT NULL,
  `Nombre_usuario` VARCHAR(45) NOT NULL,
  `Contraseña` VARCHAR(45) NOT NULL,
  `Privilegio` INT NOT NULL,
  `Fecha_creacion` DATE NULL,
  PRIMARY KEY (`idUsuario`),
  INDEX `fk_Usuario_Persona_idx` (`idPersona` ASC),
  CONSTRAINT `fk_Usuario_Persona`
    FOREIGN KEY (`idPersona`)
    REFERENCES `VIVA`.`Persona` (`idPersona`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Turno`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Turno` (
  `idTurno` INT NOT NULL,
  `Descripcion_turno` VARCHAR(45) NULL,
  `Cant_horas_turno` INT NULL,
  `Entrada_turno` TIME NOT NULL,
  `Salida_turno` TIME NOT NULL,
  PRIMARY KEY (`idTurno`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Asistencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Asistencia` (
  `idAsistencia` INT NOT NULL,
  `idUsuario` INT NOT NULL,
  `idTurno` INT NOT NULL,
  `Fecha` DATE NOT NULL,
  `Hora_entrada` DATETIME NOT NULL,
  `Hora_salida` DATETIME NOT NULL,
  PRIMARY KEY (`idAsistencia`),
  INDEX `fk_Asistencia_Usuario1_idx` (`idUsuario` ASC),
  INDEX `fk_Asistencia_Turno1_idx` (`idTurno` ASC),
  CONSTRAINT `fk_Asistencia_Usuario`
    FOREIGN KEY (`idUsuario`)
    REFERENCES `VIVA`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Asistencia_Turno1`
    FOREIGN KEY (`idTurno`)
    REFERENCES `VIVA`.`Turno` (`idTurno`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Producto_categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Producto_categoria` (
  `idCategoria` INT NOT NULL,
  `Categoria_nombre` VARCHAR(45) NOT NULL,
  `Categoria_descripcion` VARCHAR(100) NULL,
  PRIMARY KEY (`idCategoria`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Proveedor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Proveedor` (
  `idProveedor` INT NOT NULL,
  `Proveedor_nombre` VARCHAR(60) NULL,
  `Sitio_web` VARCHAR(60) NULL,
  `Fecha_alta` DATE NULL,
  `Representante` INT NOT NULL,
  PRIMARY KEY (`idProveedor`),
  INDEX `fk_Proveedor_Persona1_idx` (`Representante` ASC),
  CONSTRAINT `fk_Proveedor_Persona1`
    FOREIGN KEY (`Representante`)
    REFERENCES `VIVA`.`Persona` (`idPersona`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Producto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Producto` (
  `idProducto` INT NOT NULL,
  `idCategoria` INT NOT NULL,
  `Nombre_producto` VARCHAR(45) NOT NULL,
  `Descripcion_producto` VARCHAR(140) NULL,
  `Cantidad_unitaria_producto` FLOAT NULL,
  `Unidad_producto` VARCHAR(45) NULL,
  `Precio_unitario_producto` FLOAT NOT NULL,
  `Perecedero` VARCHAR(2) NULL,
  `Tiene_codigo_barras` VARCHAR(2) NULL,
  `idProveedor` INT NOT NULL,
  PRIMARY KEY (`idProducto`),
  INDEX `fk_Producto_Producto_categoria1_idx` (`idCategoria` ASC),
  INDEX `fk_Producto_Proveedor1_idx` (`idProveedor` ASC),
  CONSTRAINT `fk_Producto_Producto_categoria1`
    FOREIGN KEY (`idCategoria`)
    REFERENCES `VIVA`.`Producto_categoria` (`idCategoria`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Producto_Proveedor1`
    FOREIGN KEY (`idProveedor`)
    REFERENCES `VIVA`.`Proveedor` (`idProveedor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Stock_productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Stock_productos` (
  `idStock` INT NOT NULL,
  `idProducto` INT NOT NULL,
  `Disponible` VARCHAR(45) NULL,
  `Ubicacion` VARCHAR(45) NULL,
  `Fecha_caducidad` VARCHAR(45) NULL,
  `Codigo_barras` INT NULL,
  `Codigo_barras_pais` INT NULL,
  `Codigo_barras_empresa` INT NULL,
  `Codigo_barras_nro` INT NULL,
  `Codigo_barras_check` INT NULL,
  PRIMARY KEY (`idStock`),
  INDEX `fk_Stock_productos_Producto1_idx` (`idProducto` ASC),
  CONSTRAINT `fk_Stock_productos_Producto1`
    FOREIGN KEY (`idProducto`)
    REFERENCES `VIVA`.`Producto` (`idProducto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Stock_tipo_movimientos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Stock_tipo_movimientos` (
  `idStock_tipo_movimientos` INT NOT NULL,
  `Descripcion` VARCHAR(45) NULL,
  PRIMARY KEY (`idStock_tipo_movimientos`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Stock_concepto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Stock_concepto` (
  `idStock_concepto` INT NOT NULL,
  `Descripcion` VARCHAR(45) NULL,
  PRIMARY KEY (`idStock_concepto`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Stock_movimientos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Stock_movimientos` (
  `idStock_movimientos` INT NOT NULL,
  `idStock` INT NOT NULL,
  `Fecha` DATETIME NOT NULL,
  `idStock_tipo_movimientos` INT NOT NULL,
  `idStock_concepto` INT NOT NULL,
  PRIMARY KEY (`idStock_movimientos`),
  INDEX `fk_Stock_movimientos_Stock_productos1_idx` (`idStock` ASC),
  INDEX `fk_Stock_movimientos_Stock_tipo_movimientos1_idx` (`idStock_tipo_movimientos` ASC),
  INDEX `fk_Stock_movimientos_Stock_concepto1_idx` (`idStock_concepto` ASC),
  CONSTRAINT `fk_Stock_movimientos_Stock_productos1`
    FOREIGN KEY (`idStock`)
    REFERENCES `VIVA`.`Stock_productos` (`idStock`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Stock_movimientos_Stock_tipo_movimientos1`
    FOREIGN KEY (`idStock_tipo_movimientos`)
    REFERENCES `VIVA`.`Stock_tipo_movimientos` (`idStock_tipo_movimientos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Stock_movimientos_Stock_concepto1`
    FOREIGN KEY (`idStock_concepto`)
    REFERENCES `VIVA`.`Stock_concepto` (`idStock_concepto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Compra`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Compra` (
  `idCompra` INT NOT NULL,
  `Fecha` DATE NULL,
  `Condiciones_pago` VARCHAR(45) NULL,
  `Lugar_entrega` VARCHAR(45) NULL,
  `Fecha_entrega` DATE NULL,
  `Enviar_por` VARCHAR(45) NULL,
  `Estado` VARCHAR(45) NULL,
  PRIMARY KEY (`idCompra`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Orden_de_compra`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Orden_de_compra` (
  `idOrden_de_compra` INT NOT NULL,
  `idCompra` INT NOT NULL,
  `numero` INT NULL,
  `Fecha` DATE NULL,
  `Estado` VARCHAR(45) NULL,
  PRIMARY KEY (`idOrden_de_compra`),
  INDEX `fk_Orden_de_compra_Compra1_idx` (`idCompra` ASC),
  CONSTRAINT `fk_Orden_de_compra_Compra1`
    FOREIGN KEY (`idCompra`)
    REFERENCES `VIVA`.`Compra` (`idCompra`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Cliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Cliente` (
  `idCliente` INT NOT NULL,
  `Nombre` VARCHAR(60) NULL,
  `Sitio_web` VARCHAR(60) NULL,
  `Email` VARCHAR(60) NULL,
  `Fecha_alta` DATE NULL,
  `idPersona` INT NOT NULL,
  PRIMARY KEY (`idCliente`),
  INDEX `fk_Cliente_Persona1_idx` (`idPersona` ASC),
  CONSTRAINT `fk_Cliente_Persona1`
    FOREIGN KEY (`idPersona`)
    REFERENCES `VIVA`.`Persona` (`idPersona`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Cuenta_corriente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Cuenta_corriente` (
  `idCuenta_corriente` INT NOT NULL,
  `Tipo` VARCHAR(45) NOT NULL,
  `Estado` VARCHAR(45) NULL,
  `idCliente` INT NOT NULL,
  PRIMARY KEY (`idCuenta_corriente`),
  INDEX `fk_Cuenta_corriente_Proveedor1_idx` (`idCliente` ASC),
  CONSTRAINT `fk_Cuenta_corriente_Proveedor1`
    FOREIGN KEY (`idCliente`)
    REFERENCES `VIVA`.`Proveedor` (`idProveedor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Cuenta_corriente_Cliente1`
    FOREIGN KEY (`idCliente`)
    REFERENCES `VIVA`.`Cliente` (`idCliente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Cuenta_corriente_detalle`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Cuenta_corriente_detalle` (
  `idCuenta_corriente_detalle` INT NOT NULL,
  `idCuenta_corriente` INT NOT NULL,
  `Fecha_apertura` DATE NULL,
  `Fecha_ultimo_movimiento` DATETIME NULL,
  `Saldo` FLOAT NULL,
  PRIMARY KEY (`idCuenta_corriente_detalle`),
  INDEX `fk_Cuenta_corriente_detalle_Cuenta_corriente1_idx` (`idCuenta_corriente` ASC),
  CONSTRAINT `fk_Cuenta_corriente_detalle_Cuenta_corriente1`
    FOREIGN KEY (`idCuenta_corriente`)
    REFERENCES `VIVA`.`Cuenta_corriente` (`idCuenta_corriente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Albaran`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Albaran` (
  `idAlbaran` INT NOT NULL,
  `idOrden_de_compra` INT NOT NULL,
  `Numero` INT NULL,
  `Fecha` DATE NULL,
  `Estado` VARCHAR(45) NULL,
  PRIMARY KEY (`idAlbaran`),
  INDEX `fk_Albaran_Orden_de_compra1_idx` (`idOrden_de_compra` ASC),
  CONSTRAINT `fk_Albaran_Orden_de_compra1`
    FOREIGN KEY (`idOrden_de_compra`)
    REFERENCES `VIVA`.`Orden_de_compra` (`idOrden_de_compra`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Venta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Venta` (
  `idVenta` INT NOT NULL,
  `Fecha` TIMESTAMP NULL,
  `Importe` FLOAT NULL,
  `Forma_pago` VARCHAR(45) NULL,
  `Estado` VARCHAR(45) NULL,
  `idCliente` INT NOT NULL,
  PRIMARY KEY (`idVenta`),
  INDEX `fk_Venta_Cliente1_idx` (`idCliente` ASC),
  CONSTRAINT `fk_Venta_Cliente1`
    FOREIGN KEY (`idCliente`)
    REFERENCES `VIVA`.`Cliente` (`idCliente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Movimiento_bancario_tipo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Movimiento_bancario_tipo` (
  `idMovimiento_bancario_tipo` INT NOT NULL,
  `Descripcion` VARCHAR(45) NULL,
  PRIMARY KEY (`idMovimiento_bancario_tipo`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Movimiento_bancario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Movimiento_bancario` (
  `idMovimiento_bancario` INT NOT NULL,
  `Fecha` DATETIME NULL,
  `idMovimiento_bancario_tipo` INT NOT NULL,
  `Nro_cuenta` VARCHAR(45) NULL,
  `Descripcion_cuenta` VARCHAR(45) NULL,
  `Saldo` VARCHAR(45) NULL,
  PRIMARY KEY (`idMovimiento_bancario`),
  INDEX `fk_Movimiento_bancario_Movimiento_bancario_tipo1_idx` (`idMovimiento_bancario_tipo` ASC),
  CONSTRAINT `fk_Movimiento_bancario_Movimiento_bancario_tipo1`
    FOREIGN KEY (`idMovimiento_bancario_tipo`)
    REFERENCES `VIVA`.`Movimiento_bancario_tipo` (`idMovimiento_bancario_tipo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Cuenta_corriente_movimientos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Cuenta_corriente_movimientos` (
  `idCuenta_corriente_movimientos` INT NOT NULL,
  `idCuenta_corriente` INT NOT NULL,
  `Pago_nro` INT NULL,
  `Concepto` VARCHAR(45) NULL,
  `Medio_pago` VARCHAR(45) NULL,
  `Fecha` DATETIME NULL,
  `Debe` FLOAT NULL,
  `Haber` FLOAT NULL,
  `Saldo` FLOAT NULL,
  PRIMARY KEY (`idCuenta_corriente_movimientos`),
  INDEX `fk_Cuenta_corriente_movimientos_Cuenta_corriente1_idx` (`idCuenta_corriente` ASC),
  CONSTRAINT `fk_Cuenta_corriente_movimientos_Cuenta_corriente1`
    FOREIGN KEY (`idCuenta_corriente`)
    REFERENCES `VIVA`.`Cuenta_corriente` (`idCuenta_corriente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Compra_Producto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Compra_Producto` (
  `idCompra` INT NOT NULL,
  `idProducto` INT NOT NULL,
  `Cantidad` VARCHAR(45) NULL,
  PRIMARY KEY (`idCompra`, `idProducto`),
  INDEX `fk_Compra_has_Producto_Producto1_idx` (`idProducto` ASC),
  INDEX `fk_Compra_has_Producto_Compra1_idx` (`idCompra` ASC),
  CONSTRAINT `fk_Compra_has_Producto_Compra1`
    FOREIGN KEY (`idCompra`)
    REFERENCES `VIVA`.`Compra` (`idCompra`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Compra_has_Producto_Producto1`
    FOREIGN KEY (`idProducto`)
    REFERENCES `VIVA`.`Producto` (`idProducto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Venta_Producto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Venta_Producto` (
  `idVenta` INT NOT NULL,
  `idProducto` INT NOT NULL,
  `Cantidad` INT NULL,
  PRIMARY KEY (`idVenta`, `idProducto`),
  INDEX `fk_Venta_has_Producto_Producto1_idx` (`idProducto` ASC),
  INDEX `fk_Venta_has_Producto_Venta1_idx` (`idVenta` ASC),
  CONSTRAINT `fk_Venta_has_Producto_Venta1`
    FOREIGN KEY (`idVenta`)
    REFERENCES `VIVA`.`Venta` (`idVenta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Venta_has_Producto_Producto1`
    FOREIGN KEY (`idProducto`)
    REFERENCES `VIVA`.`Producto` (`idProducto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`Factura`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`Factura` (
  `idFactura` INT NOT NULL,
  `idVenta` INT NOT NULL,
  `numero` INT NOT NULL,
  `Fecha` TIMESTAMP NOT NULL,
  `Tipo` VARCHAR(1) NOT NULL,
  PRIMARY KEY (`idFactura`),
  INDEX `fk_Factura_Venta1_idx` (`idVenta` ASC),
  CONSTRAINT `fk_Factura_Venta1`
    FOREIGN KEY (`idVenta`)
    REFERENCES `VIVA`.`Venta` (`idVenta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`MovimientoBancario_Compra`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`MovimientoBancario_Compra` (
  `Movimiento_bancario_idMovimiento_bancario` INT NOT NULL,
  `Compra_idCompra` INT NOT NULL,
  `Importe` FLOAT NULL,
  PRIMARY KEY (`Movimiento_bancario_idMovimiento_bancario`, `Compra_idCompra`),
  INDEX `fk_Movimiento_bancario_has_Compra_Compra1_idx` (`Compra_idCompra` ASC),
  INDEX `fk_Movimiento_bancario_has_Compra_Movimiento_bancario1_idx` (`Movimiento_bancario_idMovimiento_bancario` ASC),
  CONSTRAINT `fk_Movimiento_bancario_has_Compra_Movimiento_bancario1`
    FOREIGN KEY (`Movimiento_bancario_idMovimiento_bancario`)
    REFERENCES `VIVA`.`Movimiento_bancario` (`idMovimiento_bancario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Movimiento_bancario_has_Compra_Compra1`
    FOREIGN KEY (`Compra_idCompra`)
    REFERENCES `VIVA`.`Compra` (`idCompra`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `VIVA`.`MovimientoBancario_Venta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `VIVA`.`MovimientoBancario_Venta` (
  `Movimiento_bancario_idMovimiento_bancario` INT NOT NULL,
  `Venta_idVenta` INT NOT NULL,
  `Importe` FLOAT NULL,
  PRIMARY KEY (`Movimiento_bancario_idMovimiento_bancario`, `Venta_idVenta`),
  INDEX `fk_Movimiento_bancario_has_Venta_Venta1_idx` (`Venta_idVenta` ASC),
  INDEX `fk_Movimiento_bancario_has_Venta_Movimiento_bancario1_idx` (`Movimiento_bancario_idMovimiento_bancario` ASC),
  CONSTRAINT `fk_Movimiento_bancario_has_Venta_Movimiento_bancario1`
    FOREIGN KEY (`Movimiento_bancario_idMovimiento_bancario`)
    REFERENCES `VIVA`.`Movimiento_bancario` (`idMovimiento_bancario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Movimiento_bancario_has_Venta_Venta1`
    FOREIGN KEY (`Venta_idVenta`)
    REFERENCES `VIVA`.`Venta` (`idVenta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
