SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `bancos`;
CREATE TABLE `bancos` (
  `id_banco` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_banco` varchar(100) NOT NULL,
  `numero_cuenta` varchar(20) NOT NULL,
  `cedula_banco` int(11) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `estatus` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_banco`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bancos` VALUES ('1', 'Provincial', '23423423423423', '435345345', '04525673456', 'activo', '2026-04-13 03:59:39');
INSERT INTO `bancos` VALUES ('2', 'Mercantil', '423423499', '563456743', '04893451234', 'activo', '2026-04-13 04:01:47');
INSERT INTO `bancos` VALUES ('3', 'Banco de Venezuela', '01020202154578963784', '44445698', '04141234567', 'activo', '2026-05-21 20:30:16');
INSERT INTO `bancos` VALUES ('4', 'Banco Mercantil', '01050123456789012345', '44445698', '04141234568', 'activo', '2026-05-21 20:30:16');
INSERT INTO `bancos` VALUES ('5', 'Banco Provincial', '01080123456789012345', '44445698', '04141234569', 'activo', '2026-05-21 20:30:16');
INSERT INTO `bancos` VALUES ('6', 'Banco Banesco', '01340123456789012345', '44445698', '04141234570', 'activo', '2026-05-21 20:30:16');
INSERT INTO `bancos` VALUES ('7', 'Banco Nacional de Crédito', '01910123456789012345', '44445698', '04141234571', 'activo', '2026-05-21 20:30:16');
INSERT INTO `bancos` VALUES ('8', 'Banco Bicentenario', '01750123456789012345', '44445698', '04141234572', 'activo', '2026-05-21 20:30:16');
INSERT INTO `bancos` VALUES ('9', 'Banco del Tesoro', '01630123456789012345', '44445698', '04141234573', 'activo', '2026-05-21 20:30:16');
INSERT INTO `bancos` VALUES ('10', 'Banco Plaza', '01380123456789012345', '44445698', '04141234574', 'activo', '2026-05-21 20:30:16');
INSERT INTO `bancos` VALUES ('11', 'Banco Activo', '01900123456789012345', '44445698', '04141234575', 'activo', '2026-05-21 20:30:16');
INSERT INTO `bancos` VALUES ('12', 'Banco Caroní', '01280123456789012345', '44445698', '04141234576', 'activo', '2026-05-21 20:30:16');


DROP TABLE IF EXISTS `cargos`;
CREATE TABLE `cargos` (
  `id_cargo` int(12) NOT NULL AUTO_INCREMENT,
  `nombre_cargo` varchar(50) NOT NULL,
  PRIMARY KEY (`id_cargo`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cargos` VALUES ('1', 'Admin');
INSERT INTO `cargos` VALUES ('13', 'Cajera');


DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(12) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(30) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorias` VALUES ('22', 'Productos De Casa');
INSERT INTO `categorias` VALUES ('24', 'Accesorios');
INSERT INTO `categorias` VALUES ('26', 'Telefono');
INSERT INTO `categorias` VALUES ('27', 'Repuestos');


DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `cedula_persona` varchar(20) NOT NULL,
  `residencia` text NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  UNIQUE KEY `cedula_persona` (`cedula_persona`),
  KEY `cedula_persona_2` (`cedula_persona`),
  CONSTRAINT `fk_cliente_persona` FOREIGN KEY (`cedula_persona`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` VALUES ('30000000', 'No especificado', 'activo');
INSERT INTO `clientes` VALUES ('V-10456123', 'Barquisimeto cerca de la catedral', 'activo');
INSERT INTO `clientes` VALUES ('V-3242343', 'Centro.cubiro', 'activo');
INSERT INTO `clientes` VALUES ('V-34567567', 'Zona norte carorita km 10', 'activo');
INSERT INTO `clientes` VALUES ('V-38456234', 'Zona centro calle 15 con 14 y 16', 'activo');
INSERT INTO `clientes` VALUES ('V-7890345', 'urbanizacion roca del este', 'activo');


DROP TABLE IF EXISTS `cuotas`;
CREATE TABLE `cuotas` (
  `id_cuota` int(11) NOT NULL AUTO_INCREMENT,
  `id_financiamiento` int(11) NOT NULL,
  `numero_cuota` int(11) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `monto_pagado` decimal(10,2) DEFAULT 0.00,
  `fecha_pago_realizado` timestamp NULL DEFAULT NULL,
  `estado_cuota` enum('pendiente','pagado','atrasado') DEFAULT 'pendiente',
  `id_metodopago` int(11) DEFAULT NULL,
  `id_banco` int(11) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_cuota`),
  KEY `fk_cuotas_financiamiento` (`id_financiamiento`),
  KEY `id_banco` (`id_banco`),
  KEY `id_metodopago` (`id_metodopago`),
  CONSTRAINT `cuotas_ibfk_1` FOREIGN KEY (`id_banco`) REFERENCES `bancos` (`id_banco`),
  CONSTRAINT `cuotas_ibfk_2` FOREIGN KEY (`id_metodopago`) REFERENCES `metodo_pago` (`id_metodopago`),
  CONSTRAINT `fk_cuotas_financiamiento` FOREIGN KEY (`id_financiamiento`) REFERENCES `financiamientos` (`id_financiamiento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cuotas` VALUES ('67', '11', '1', '2026-07-10', '120.00', '2026-06-24 20:41:20', 'pagado', '2', NULL, NULL);
INSERT INTO `cuotas` VALUES ('68', '12', '1', '2026-07-01', '130.00', '2026-07-01 05:20:22', 'pagado', '2', NULL, NULL);
INSERT INTO `cuotas` VALUES ('69', '12', '1', '2026-08-01', '66.67', '2026-07-01 05:23:48', 'pagado', '2', NULL, NULL);
INSERT INTO `cuotas` VALUES ('79', '12', '1', '2026-08-01', '52.50', '2026-07-01 05:31:10', 'pagado', '2', NULL, NULL);
INSERT INTO `cuotas` VALUES ('80', '12', '2', '2026-09-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);
INSERT INTO `cuotas` VALUES ('81', '12', '3', '2026-10-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);
INSERT INTO `cuotas` VALUES ('82', '12', '4', '2026-11-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);


DROP TABLE IF EXISTS `despachos`;
CREATE TABLE `despachos` (
  `id_despacho` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `tipo_despacho` enum('tienda','delivery') NOT NULL,
  `fecha_despacho` date NOT NULL,
  `hora_despacho` time DEFAULT NULL,
  `despachador_nombre` varchar(100) DEFAULT NULL,
  `despachador_telefono` varchar(20) DEFAULT NULL,
  `instrucciones_entrega` text DEFAULT NULL,
  `estado_despacho` enum('pendiente','asignado','en_ruta','entregado','cancelado') DEFAULT 'pendiente',
  `fecha_entrega_estimada` date DEFAULT NULL,
  `fecha_entrega_real` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_despacho`),
  KEY `id_pedido` (`id_pedido`),
  KEY `idx_estado_desp` (`estado_despacho`),
  CONSTRAINT `despachos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `detalle_pedido`;
CREATE TABLE `detalle_pedido` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_pedido` (`id_pedido`),
  KEY `fk_detalle_producto` (`id_producto`),
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `detalle_pedido` VALUES ('48', '27', '20', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('49', '27', '18', '1', '12.99', '12.99');
INSERT INTO `detalle_pedido` VALUES ('50', '28', '20', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('51', '28', '18', '1', '12.99', '12.99');
INSERT INTO `detalle_pedido` VALUES ('52', '32', '16', '1', '8.99', '8.99');
INSERT INTO `detalle_pedido` VALUES ('53', '32', '12', '1', '180.00', '180.00');
INSERT INTO `detalle_pedido` VALUES ('54', '33', '12', '1', '180.00', '180.00');
INSERT INTO `detalle_pedido` VALUES ('55', '33', '16', '2', '8.99', '17.98');
INSERT INTO `detalle_pedido` VALUES ('56', '34', '12', '1', '180.00', '180.00');
INSERT INTO `detalle_pedido` VALUES ('57', '34', '16', '1', '8.99', '8.99');
INSERT INTO `detalle_pedido` VALUES ('58', '35', '12', '1', '180.00', '180.00');
INSERT INTO `detalle_pedido` VALUES ('59', '35', '16', '1', '8.99', '8.99');


DROP TABLE IF EXISTS `detalle_venta`;
CREATE TABLE `detalle_venta` (
  `id_detalle_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle_venta`),
  KEY `FK_id_venta` (`id_venta`),
  KEY `FK_id_producto` (`id_producto`),
  CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `detalle_venta` VALUES ('1', '3', '10', '1', '320.00', '320.00');
INSERT INTO `detalle_venta` VALUES ('2', '4', '23', '1', '180.00', '180.00');


DROP TABLE IF EXISTS `detalles_entrada`;
CREATE TABLE `detalles_entrada` (
  `id_entrada_fk` int(11) NOT NULL,
  `id_producto_fk` int(11) NOT NULL,
  `cantidad_entrada` decimal(12,2) NOT NULL,
  `dias_garantia` int(11) NOT NULL,
  KEY `id_entrada_fk` (`id_entrada_fk`),
  KEY `id_producto_fk` (`id_producto_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `detalles_entrada` VALUES ('67', '16', '8.00', '0');
INSERT INTO `detalles_entrada` VALUES ('69', '16', '9.00', '0');
INSERT INTO `detalles_entrada` VALUES ('69', '18', '10.00', '6');
INSERT INTO `detalles_entrada` VALUES ('70', '27', '2.00', '2');


DROP TABLE IF EXISTS `detalles_financiamiento`;
CREATE TABLE `detalles_financiamiento` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_financiamiento` int(11) NOT NULL,
  `id_productos` int(11) NOT NULL,
  `id_unidad` int(11) DEFAULT NULL,
  `estado_equipo` enum('activo','bloqueado') DEFAULT 'activo',
  PRIMARY KEY (`id_detalle`),
  KEY `fk_finan_detalles_ref` (`id_financiamiento`),
  KEY `fk_producto_detalles_ref` (`id_productos`),
  CONSTRAINT `fk_finan_detalles_ref` FOREIGN KEY (`id_financiamiento`) REFERENCES `financiamientos` (`id_financiamiento`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_producto_detalles_ref` FOREIGN KEY (`id_productos`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `detalles_financiamiento` VALUES ('11', '11', '10', '20', 'activo');
INSERT INTO `detalles_financiamiento` VALUES ('12', '12', '23', '22', 'activo');
INSERT INTO `detalles_financiamiento` VALUES ('13', '13', '9', '19', 'activo');


DROP TABLE IF EXISTS `empleados`;
CREATE TABLE `empleados` (
  `cedula_persona` varchar(20) DEFAULT NULL,
  `perfil` varchar(20) NOT NULL DEFAULT 'no',
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  `id_cargo` int(12) NOT NULL,
  KEY `id_cargo` (`id_cargo`),
  KEY `cedula_persona` (`cedula_persona`),
  CONSTRAINT `fk_empleado_persona` FOREIGN KEY (`cedula_persona`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `relacion empleado-cargo` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `empleados` VALUES ('V-4562434', 'no', 'activo', '1');
INSERT INTO `empleados` VALUES ('V-56789456', 'si', 'activo', '13');
INSERT INTO `empleados` VALUES ('V-14567234', 'no', 'activo', '1');
INSERT INTO `empleados` VALUES ('V-22222222', 'no', 'activo', '13');
INSERT INTO `empleados` VALUES ('V-22222227', 'no', 'activo', '1');
INSERT INTO `empleados` VALUES ('V-300888024', 'no', 'activo', '1');


DROP TABLE IF EXISTS `entradas_productos`;
CREATE TABLE `entradas_productos` (
  `id_entrada` int(11) NOT NULL AUTO_INCREMENT,
  `rif_proveedor_fk` varchar(30) NOT NULL,
  `fecha_entrada` datetime NOT NULL,
  PRIMARY KEY (`id_entrada`),
  KEY `rif_proveedor_fk` (`rif_proveedor_fk`),
  CONSTRAINT `entradas_productos_ibfk_1` FOREIGN KEY (`rif_proveedor_fk`) REFERENCES `proveedores` (`rif_proveedor`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `entradas_productos` VALUES ('67', 'J-8777999', '2026-06-15 07:25:12');
INSERT INTO `entradas_productos` VALUES ('69', 'G-27272727', '2026-06-15 07:40:17');
INSERT INTO `entradas_productos` VALUES ('70', 'J-8777999', '2026-06-15 12:13:26');


DROP TABLE IF EXISTS `especialidades`;
CREATE TABLE `especialidades` (
  `id_especialidad` int(12) NOT NULL AUTO_INCREMENT,
  `nombre_especialidad` varchar(30) NOT NULL,
  PRIMARY KEY (`id_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `especialidades` VALUES ('18', 'Apple');
INSERT INTO `especialidades` VALUES ('19', 'Android');


DROP TABLE IF EXISTS `financiamientos`;
CREATE TABLE `financiamientos` (
  `id_financiamiento` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_persona` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `pago_inicial` decimal(10,2) NOT NULL,
  `cantidad_cuotas` int(11) NOT NULL,
  `monto_cuota` decimal(10,2) NOT NULL,
  `dia_pago` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `estatus_financiamiento` varchar(20) DEFAULT 'vigente',
  PRIMARY KEY (`id_financiamiento`),
  KEY `fk_financiamiento_persona` (`cedula_persona`),
  CONSTRAINT `fk_financiamiento_persona` FOREIGN KEY (`cedula_persona`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `financiamientos` VALUES ('11', 'V-34567567', '320.00', '200.00', '1', '120.00', '10', '2026-06-24', 'finalizado');
INSERT INTO `financiamientos` VALUES ('12', 'V-38456234', '250.00', '40.00', '4', '52.50', '1', '2026-07-01', 'finalizado');
INSERT INTO `financiamientos` VALUES ('13', 'V-7890345', '1020.00', '50.00', '3', '323.33', '1', '2026-07-01', 'anulado');


DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_marca` varchar(100) NOT NULL,
  PRIMARY KEY (`id_marca`),
  UNIQUE KEY `nombre_marca` (`nombre_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `marcas` VALUES ('1', 'Apple');
INSERT INTO `marcas` VALUES ('3', 'Generico');
INSERT INTO `marcas` VALUES ('2', 'Samsung');


DROP TABLE IF EXISTS `metodo_pago`;
CREATE TABLE `metodo_pago` (
  `id_metodopago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_metodopago` varchar(100) NOT NULL,
  `moneda` varchar(20) NOT NULL,
  `cuenta` varchar(50) DEFAULT '0',
  `estatus` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_metodopago`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `metodo_pago` VALUES ('1', 'Punto De Venta', 'VES', '0', '1');
INSERT INTO `metodo_pago` VALUES ('2', 'Divisa', 'USD', '0', '1');
INSERT INTO `metodo_pago` VALUES ('3', 'Pago Movil', 'VES', '0', '1');


DROP TABLE IF EXISTS `observaciones_turno`;
CREATE TABLE `observaciones_turno` (
  `id_observacion` int(12) NOT NULL AUTO_INCREMENT,
  `id_turno` int(12) NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id_observacion`),
  KEY `fk_turno_obs` (`id_turno`),
  CONSTRAINT `fk_turno_obs` FOREIGN KEY (`id_turno`) REFERENCES `turnos` (`id_turno`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `observaciones_turno` VALUES ('7', '4', 'retrazo');


DROP TABLE IF EXISTS `ordenes`;
CREATE TABLE `ordenes` (
  `id_orden` int(11) NOT NULL AUTO_INCREMENT,
  `cliente` varchar(150) NOT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'Pendiente',
  `equipo` varchar(150) NOT NULL,
  `imei` varchar(50) NOT NULL,
  `horas_estimadas` time NOT NULL DEFAULT '00:00:00',
  `observaciones` text DEFAULT NULL,
  `repuestos` text NOT NULL,
  `monto_reparacion` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_repuestos` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_orden` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `pago_online`;
CREATE TABLE `pago_online` (
  `id_reporte` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `cedula_cliente` varchar(20) DEFAULT NULL,
  `referencia` varchar(50) NOT NULL,
  `banco_emisor` varchar(50) NOT NULL,
  `banco_receptor` varchar(50) DEFAULT NULL,
  `telefono_transferencia` varchar(20) DEFAULT '00000000000' COMMENT 'Teléfono desde donde se hizo la transferencia',
  `nombre_pagador` varchar(100) DEFAULT NULL COMMENT 'Nombre completo del pagador',
  `monto_reportado` decimal(10,2) NOT NULL,
  `fecha_transferencia` date NOT NULL,
  `cedula_pagador` varchar(20) DEFAULT NULL,
  `comprobante_adjunto` varchar(500) DEFAULT NULL,
  `estado_verificacion` enum('pendiente','en_revision','aprobado','rechazado') DEFAULT 'pendiente',
  `fecha_reporte` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_verificacion` timestamp NULL DEFAULT NULL,
  `verificado_por` varchar(20) DEFAULT NULL,
  `motivo_rechazo` text DEFAULT NULL,
  PRIMARY KEY (`id_reporte`),
  KEY `id_pedido` (`id_pedido`),
  KEY `cedula_cliente` (`cedula_cliente`),
  KEY `idx_estado` (`estado_verificacion`),
  KEY `idx_referencia` (`referencia`),
  CONSTRAINT `pago_online_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pago_online` VALUES ('2', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:29:34', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('3', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:30:29', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('4', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:31:46', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('5', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:34:40', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('6', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:35:48', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('7', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:37:01', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('8', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:37:34', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('9', '27', '30000000', '234234', 'banco de venezuela', NULL, '00000000000', NULL, '40.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:38:35', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('10', '27', '30000000', '23423423', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:40:01', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('11', '27', '30000000', '23423423', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:40:41', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('12', '27', '30000000', '23423423', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:41:06', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('13', '27', '30000000', '12312312', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 07:42:28', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('14', '28', '30000000', '12312312', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 08:03:34', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('15', '28', '30000000', '12312312', 'Banco Venezuela', NULL, '00000000000', NULL, '69.00', '2026-06-12', '30000000', NULL, 'pendiente', '2026-06-12 08:07:24', NULL, NULL, NULL);
INSERT INTO `pago_online` VALUES ('16', '35', '30000000', '0584678906', 'Banco Banesco', 'Banesco', '04161768520', 'Cliente Prueba', '198.99', '2026-06-24', '30000000', NULL, 'pendiente', '2026-06-23 23:40:14', NULL, NULL, NULL);


DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(20) DEFAULT NULL,
  `email_invitado` varchar(100) DEFAULT NULL,
  `nombre_cliente` varchar(150) NOT NULL,
  `telefono_cliente` varchar(20) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL,
  `costo_envio` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','pagado','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
  `datos_pago` text DEFAULT NULL,
  `direccion_entrega` text DEFAULT NULL,
  `id_direccion_envio` int(11) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `fk_pedidos_cliente` (`cedula_cliente`),
  KEY `fk_pedidos_direccion` (`id_direccion_envio`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pedidos` VALUES ('27', '30000000', 'PruebaCliente@gmail.com', 'Cliente Prueba', '00000000000', '2026-06-12 07:28:38', '68.98', '10.00', '68.98', 'pendiente', '{\"metodo\":\"pago_movil\",\"banco_emisor\":\"3\",\"fecha_seleccion\":\"2026-06-12 07:28:38\"}', NULL, NULL, 'pago_movil');
INSERT INTO `pedidos` VALUES ('28', '30000000', 'PruebaCliente@gmail.com', 'Cliente Prueba', '00000000000', '2026-06-12 08:00:44', '68.98', '10.00', '68.98', 'pendiente', '{\"metodo\":\"pago_movil\",\"banco_emisor\":\"8\",\"fecha_seleccion\":\"2026-06-12 08:00:44\"}', NULL, NULL, 'pago_movil');
INSERT INTO `pedidos` VALUES ('32', '30000000', 'PruebaCliente@gmail.com', 'Cliente Prueba', '00000000000', '2026-06-21 17:46:12', '188.99', '0.00', '188.99', 'pendiente', '{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-21 23:46:12\"}', NULL, NULL, 'pago_movil');
INSERT INTO `pedidos` VALUES ('33', '30000000', 'PruebaCliente@gmail.com', 'Cliente Prueba', '00000000000', '2026-06-23 17:07:09', '197.98', '0.00', '197.98', 'pendiente', '{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-23 23:07:09\"}', NULL, NULL, 'pago_movil');
INSERT INTO `pedidos` VALUES ('34', '30000000', 'PruebaCliente@gmail.com', 'Cliente Prueba', '00000000000', '2026-06-23 17:20:21', '188.99', '0.00', '188.99', 'pendiente', '{\"metodo\":\"pago_movil\",\"banco_emisor\":\"5\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-23 23:20:21\"}', NULL, NULL, 'pago_movil');
INSERT INTO `pedidos` VALUES ('35', '30000000', 'PruebaCliente@gmail.com', 'Cliente Prueba', '00000000000', '2026-06-23 19:02:32', '188.99', '0.00', '188.99', 'pendiente', '{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-24 01:02:32\"}', NULL, NULL, 'pago_movil');


DROP TABLE IF EXISTS `perfiles_financiamiento`;
CREATE TABLE `perfiles_financiamiento` (
  `id_perfil` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_persona` varchar(20) NOT NULL,
  `tipo_residencia` enum('Propia','Familiar','Alquilada') DEFAULT 'Familiar',
  `carga_familiar` int(11) DEFAULT 0,
  `estado_civil` enum('Soltero','Casado','Divorciado','Viudo','En relación') DEFAULT 'Soltero',
  `profesion` enum('Empleado','Independiente','Estudiante (Becado)','Estudiante','Pensionado','Desempleado') DEFAULT 'Empleado',
  `ocupacion` varchar(100) DEFAULT NULL,
  `ingresos_mensuales` decimal(10,2) DEFAULT 0.00,
  `score_credito` int(11) DEFAULT 5,
  PRIMARY KEY (`id_perfil`),
  KEY `fk_perfil_persona` (`cedula_persona`),
  CONSTRAINT `fk_perfil_persona` FOREIGN KEY (`cedula_persona`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `perfiles_financiamiento` VALUES ('3', 'V-3242343', 'Propia', '0', 'Soltero', 'Empleado', 'cajera', '150.52', '5');
INSERT INTO `perfiles_financiamiento` VALUES ('4', 'V-34567567', 'Propia', '0', 'Soltero', 'Empleado', 'Cajero', '650.46', '8');
INSERT INTO `perfiles_financiamiento` VALUES ('5', 'V-10456123', 'Propia', '0', 'Soltero', 'Empleado', 'Cajera', '154.46', '5');
INSERT INTO `perfiles_financiamiento` VALUES ('6', 'V-7890345', 'Propia', '0', 'Soltero', 'Empleado', 'Gerente', '171.62', '5');
INSERT INTO `perfiles_financiamiento` VALUES ('7', 'V-38456234', 'Propia', '0', 'Soltero', 'Empleado', 'Gerente', '136.19', '5');


DROP TABLE IF EXISTS `persona`;
CREATE TABLE `persona` (
  `cedula_persona` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` varchar(15) DEFAULT NULL,
  `direccion` varchar(50) NOT NULL,
  PRIMARY KEY (`cedula_persona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `persona` VALUES ('30000000', 'Cliente', 'Prueba', 'PruebaCliente@gmail.com', '00000000000', NULL, 'No especificado', 'No especificado');
INSERT INTO `persona` VALUES ('V-10456123', 'Marielys Alessandra', 'Yamal Herrera', 'marielys@gmail.com', '04125673456', '2001-06-13', 'Femenino', 'Barquisimeto cerca de la catedral');
INSERT INTO `persona` VALUES ('V-14567234', 'Maria eugenia', 'Aguilar garcia', 'mariale@gmail.com', '04163456789', '2008-02-13', 'F', 'cubiro lomas arriba');
INSERT INTO `persona` VALUES ('V-22222222', 'Gabriela', 'Garcia', 'PruebaCliente@gmail.com', '04120202020', '0001-04-01', 'M', 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb');
INSERT INTO `persona` VALUES ('V-22222227', 'Gabriela', 'Torre', 'angelito@gmail.com', '04122323232', '0001-01-01', '', 'gvgvghhgghghjghjghj');
INSERT INTO `persona` VALUES ('V-300888024', 'Jose', 'Jose', 'PruebaCliente@gmail.com', '04120587814', '2023-05-28', 'M', 'jksjkhdkjshdkjshd');
INSERT INTO `persona` VALUES ('V-3242343', 'pedro josue', 'garcia torre', 'garpedro@gmail.com', '04240001020', '2006-02-08', 'Masculino', 'Centro.cubiro');
INSERT INTO `persona` VALUES ('V-34567567', 'Gabriel Noriega', 'Marquez', 'Gabriel_2026@gmail.com', '04268004070', '2003-02-12', 'Masculino', 'Zona norte carorita km 10');
INSERT INTO `persona` VALUES ('V-38456234', 'Ederson Alejandro', 'Gonzalez Turan', 'Ederson@gmail.com', '04124674023', '2004-01-05', 'Masculino', 'Zona centro calle 15 con 14 y 16');
INSERT INTO `persona` VALUES ('V-4562434', 'Gabriela alejandra', 'Garcia Silva', 'gabriela_priv@gmail.com', '04121005540', '2026-06-04', 'F', 'Barquisimeto urb don jesus');
INSERT INTO `persona` VALUES ('V-56789456', 'Juan josue', 'Mingueza palomo', 'josecarrillogc@gmail.com', '04164007890', '2005-02-11', 'M', 'Zona norte duaca intercomunal 14');
INSERT INTO `persona` VALUES ('V-7890345', 'Gerardo Fernando', 'Hernandez Monsalve', 'gerafer@gmail.com', '04244562367', '1995-06-14', 'Masculino', 'urbanizacion roca del este');


DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_producto` varchar(150) NOT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `descripcion` varchar(100) NOT NULL,
  `imagen_principal` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `id_marca` int(11) DEFAULT NULL,
  `stock_minimo` int(11) DEFAULT 0,
  `stock_maximo` int(11) DEFAULT 0,
  `stock_actual` int(11) DEFAULT 0,
  `precio_detal` decimal(10,2) NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_producto`),
  KEY `fk_prod_categoria` (`id_categoria`),
  KEY `fk_prod_marca` (`id_marca`),
  CONSTRAINT `fk_prod_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_prod_marca` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id_marca`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `productos` VALUES ('9', 'Iphone 17', NULL, 'usado sin rayones', 'iphone17.jpg', '26', '1', '0', '1', '1', '1020.00', '1');
INSERT INTO `productos` VALUES ('10', 'Xiaomi Redmi Note 13', NULL, 'Smartphone 8GB RAM, 128GB', 'xiaomi_redmi13.jpg', '26', '1', '0', '0', '1', '320.00', '1');
INSERT INTO `productos` VALUES ('11', 'Samsung Galaxy A54', NULL, '256GB, pantalla 6.4', 'samsung_a54.jpg', '26', '1', '0', '0', '1', '450.00', '1');
INSERT INTO `productos` VALUES ('12', 'AirPods Pro 2', NULL, 'Audífonos inalámbricos', 'airpods_pro2.jpg', '24', '1', '0', '0', '15', '180.00', '1');
INSERT INTO `productos` VALUES ('13', 'Cargador Rápido 33W', NULL, 'USB-C carga rápida', NULL, '24', '1', '0', '0', '50', '25.00', '1');
INSERT INTO `productos` VALUES ('14', 'Funda Silicona iPhone 17', NULL, 'Funda protectora', NULL, '24', '1', '0', '0', '30', '15.00', '1');
INSERT INTO `productos` VALUES ('15', 'Cargador 20W', NULL, 'Cargador rápido USB-C 20W', 'cargador_20w.jpg', '24', '1', '0', '0', '50', '18.99', '1');
INSERT INTO `productos` VALUES ('16', 'Cable USB-C', NULL, 'Cable USB-C a USB-C 1m', 'cable_usbc.jpg', '24', '1', '0', '0', '100', '8.99', '1');
INSERT INTO `productos` VALUES ('17', 'Enchufe Inteligente', NULL, 'Enchufe WiFi compatible Alexa', 'enchufe_inteligente.jpg', '22', '1', '0', '0', '29', '25.99', '1');
INSERT INTO `productos` VALUES ('18', 'Kit de Limpieza', NULL, 'Kit completo para celulares', 'kit_limpieza.jpg', '24', '1', '0', '0', '40', '12.99', '1');
INSERT INTO `productos` VALUES ('19', 'Lámpara LED', NULL, 'Lámpara inteligente RGB', 'lampara_led.jpg', '22', '1', '0', '0', '25', '28.99', '1');
INSERT INTO `productos` VALUES ('20', 'Parlante Bluetooth', NULL, 'Parlante portátil 10W', 'parlante_bt.jpg', '22', '1', '0', '0', '20', '45.99', '1');
INSERT INTO `productos` VALUES ('21', 'Soporte Auto', NULL, 'Soporte magnético para auto', 'soporte_auto.jpg', '24', '1', '0', '0', '60', '15.99', '1');
INSERT INTO `productos` VALUES ('22', 'Raton', NULL, 'Raton nuevo', NULL, '24', '2', '2', '10', '3', '10.00', '1');
INSERT INTO `productos` VALUES ('23', 'Iphone xs max', NULL, '86% bateria', NULL, '26', '1', '0', '1', '1', '180.00', '1');
INSERT INTO `productos` VALUES ('25', 'Cargador de iphone', NULL, 'carga rapida', NULL, '24', '1', '5', '10', '1', '10.00', '1');
INSERT INTO `productos` VALUES ('27', 'Pantalla oled iphone', NULL, 'Pantalla nueva', NULL, '27', '3', '2', '10', '5', '20.00', '1');


DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `rif_proveedor` varchar(30) NOT NULL,
  `nombre_proveedor` varchar(50) NOT NULL,
  `telefono_proveedor` varchar(15) NOT NULL,
  `correo_proveedor` varchar(50) NOT NULL,
  `ubicacion_proveedor` varchar(100) NOT NULL,
  PRIMARY KEY (`rif_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `proveedores` VALUES ('G-27272727', 'Francheska Monsalve', '04145746099', 'francheskamonsalvemedina@gmail.com', 'La Segoviana');
INSERT INTO `proveedores` VALUES ('J-8777999', 'Yorlek Medina', '041451135899', 'yorlekmedina@gmail.com', 'La Floresta');


DROP TABLE IF EXISTS `servicio_venta`;
CREATE TABLE `servicio_venta` (
  `id_servicio_venta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `direccion` varchar(250) NOT NULL,
  `metodo_pago` varchar(100) NOT NULL,
  `referencia_pago` varchar(150) DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_servicio_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `turno_empleado`;
CREATE TABLE `turno_empleado` (
  `id_turno_empleado` int(12) NOT NULL AUTO_INCREMENT,
  `id_turno` int(12) NOT NULL,
  `cedula_persona` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_turno_empleado`),
  KEY `fk_turno` (`id_turno`),
  KEY `fk_empleado_turno` (`cedula_persona`),
  KEY `cedula_persona` (`cedula_persona`),
  CONSTRAINT `fk_turno` FOREIGN KEY (`id_turno`) REFERENCES `turnos` (`id_turno`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `turno_empleado` VALUES ('7', '4', 'V-4562434');


DROP TABLE IF EXISTS `turnos`;
CREATE TABLE `turnos` (
  `id_turno` int(12) NOT NULL AUTO_INCREMENT,
  `fecha_turno` date NOT NULL,
  `hora_entrada` time NOT NULL,
  `hora_salida` time NOT NULL,
  PRIMARY KEY (`id_turno`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `turnos` VALUES ('4', '2026-06-12', '10:54:00', '05:54:00');


DROP TABLE IF EXISTS `unidades_telefonos`;
CREATE TABLE `unidades_telefonos` (
  `id_unidad` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `imei` varchar(25) NOT NULL,
  `memoria_ram` varchar(20) DEFAULT NULL,
  `almacenamiento` varchar(20) DEFAULT NULL,
  `estado_venta` enum('Disponible','Vendido','Financiado') DEFAULT 'Disponible',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_unidad`),
  UNIQUE KEY `imei` (`imei`),
  KEY `fk_unidad_producto` (`id_producto`),
  CONSTRAINT `fk_unidad_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `unidades_telefonos` VALUES ('19', '9', '351111111111111', '8GB', '128GB', 'Financiado', '2026-06-03 18:21:19');
INSERT INTO `unidades_telefonos` VALUES ('20', '10', '352222222222222', '8GB', '128GB', 'Financiado', '2026-06-03 18:21:19');
INSERT INTO `unidades_telefonos` VALUES ('21', '11', '353333333333333', '8GB', '256GB', 'Disponible', '2026-06-03 18:21:19');
INSERT INTO `unidades_telefonos` VALUES ('22', '23', '13562823423423', '8gb', '128gb', 'Financiado', '2026-06-12 04:02:25');


DROP TABLE IF EXISTS `usuario_web`;
CREATE TABLE `usuario_web` (
  `id_credencial` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `ultimo_cambio_password` timestamp NOT NULL DEFAULT current_timestamp(),
  `intentos_fallidos` int(11) DEFAULT 0,
  `token_recuperacion` varchar(100) DEFAULT NULL,
  `verificado_email` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_credencial`),
  UNIQUE KEY `uk_cedula` (`cedula_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuario_web` VALUES ('3', '30000000', '$2y$10$pVIHhvo3Vpv4MTaE7MmzQuGYv3.IEopELr2hi8ACocCgcTZYDCEXq', '2026-06-12 07:20:39', '0', NULL, '1');
INSERT INTO `usuario_web` VALUES ('16', 'V-10456123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-06-21 06:36:06', '2', NULL, '1');
INSERT INTO `usuario_web` VALUES ('17', 'V-3242343', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-06-21 06:36:06', '0', NULL, '1');
INSERT INTO `usuario_web` VALUES ('18', 'V-34567567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-06-21 06:36:06', '0', NULL, '1');
INSERT INTO `usuario_web` VALUES ('19', 'V-38456234', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-06-21 06:36:06', '0', NULL, '1');
INSERT INTO `usuario_web` VALUES ('20', 'V-7890345', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-06-21 06:36:06', '0', NULL, '1');


DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_venta` datetime NOT NULL DEFAULT current_timestamp(),
  `origen_venta` varchar(15) NOT NULL,
  `total_venta` decimal(10,2) NOT NULL,
  `cedula_persona` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cedula_usuario` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_venta`),
  KEY `FK_cedula_persona` (`cedula_persona`) USING BTREE,
  KEY `FK_cedula_usuario` (`cedula_usuario`) USING BTREE,
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cedula_persona`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`cedula_usuario`) REFERENCES `sistema_edward_usuario`.`usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ventas` VALUES ('1', '2026-06-24 20:35:07', 'Financiamiento', '450.00', 'V-7890345', 'administrador');
INSERT INTO `ventas` VALUES ('3', '2026-06-24 20:41:20', 'Financiamiento', '320.00', 'V-34567567', 'administrador');
INSERT INTO `ventas` VALUES ('4', '2026-07-01 05:20:22', 'Financiamiento', '180.00', 'V-38456234', 'administrador');


SET FOREIGN_KEY_CHECKS=1;