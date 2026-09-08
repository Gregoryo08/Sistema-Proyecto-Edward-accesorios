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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cargos` VALUES ('1', 'Admin');


DROP TABLE IF EXISTS `carrito_sesion`;
CREATE TABLE `carrito_sesion` (
  `id_sesion` varchar(128) NOT NULL,
  `cedula_cliente` varchar(20) DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_sesion`),
  KEY `fk_carrito_cliente` (`cedula_cliente`),
  CONSTRAINT `fk_carrito_cliente` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(12) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(30) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categorias` VALUES ('22', 'Productos De Casa');
INSERT INTO `categorias` VALUES ('24', 'Accesorios');
INSERT INTO `categorias` VALUES ('26', 'Telefono');


DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `cedula_cliente` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `sexo` varchar(50) NOT NULL,
  `residencia` text NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  `referencia_nombre` varchar(100) DEFAULT NULL,
  `referencia_telefono` varchar(20) DEFAULT NULL,
  `suscrito_boletin` tinyint(1) DEFAULT 0,
  `fecha_suscripcion` datetime DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `token_recuperacion` varchar(255) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL,
  PRIMARY KEY (`cedula_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` VALUES ('V-40678456', 'Dayana', 'Perez', 'dayanita2025@gmail.com', '04126785645', '1995-10-18', 'Femenino', 'quibor centrico', 'activo', NULL, NULL, '0', NULL, NULL, NULL, NULL);


DROP TABLE IF EXISTS `credenciales_online`;
CREATE TABLE `credenciales_online` (
  `id_credencial` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `ultimo_cambio_password` timestamp NOT NULL DEFAULT current_timestamp(),
  `intentos_fallidos` int(11) DEFAULT 0,
  `token_recuperacion` varchar(100) DEFAULT NULL,
  `verificado_email` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_credencial`),
  UNIQUE KEY `uk_cedula` (`cedula_cliente`),
  CONSTRAINT `credenciales_online_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `credenciales_online` VALUES ('1', 'V-12345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-05-20 21:27:05', '0', NULL, '0');
INSERT INTO `credenciales_online` VALUES ('2', 'V-912345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-05-21 22:43:09', '0', NULL, '1');


DROP TABLE IF EXISTS `creditos`;
CREATE TABLE `creditos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(20) NOT NULL,
  `monto_total` decimal(12,2) NOT NULL,
  `monto_pagado` decimal(12,2) DEFAULT 0.00,
  `monto_pendiente` decimal(12,2) GENERATED ALWAYS AS (`monto_total` - `monto_pagado`) STORED,
  `cuotas` int(11) NOT NULL,
  `cuotas_pagadas` int(11) DEFAULT 0,
  `fecha_otorgamiento` datetime DEFAULT current_timestamp(),
  `fecha_vencimiento` datetime DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `estado` enum('activo','pagado','vencido','incobrable') DEFAULT 'activo',
  PRIMARY KEY (`id`),
  KEY `idx_creditos_estado` (`estado`),
  KEY `idx_creditos_cliente` (`cedula_cliente`),
  CONSTRAINT `fk_credito_cliente` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



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
  KEY `id_financiamiento` (`id_financiamiento`),
  CONSTRAINT `cuotas_ibfk_1` FOREIGN KEY (`id_financiamiento`) REFERENCES `financiamientos` (`id_financiamiento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cuotas` VALUES ('35', '11', '1', '2026-06-01', '144.00', '2026-05-28 15:15:31', 'pagado', '2', NULL, NULL);
INSERT INTO `cuotas` VALUES ('36', '11', '2', '2026-07-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);
INSERT INTO `cuotas` VALUES ('37', '11', '3', '2026-08-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);
INSERT INTO `cuotas` VALUES ('38', '11', '4', '2026-09-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);
INSERT INTO `cuotas` VALUES ('39', '11', '5', '2026-10-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);


DROP TABLE IF EXISTS `despachos`;
CREATE TABLE `despachos` (
  `id_despacho` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `tipo_despacho` enum('tienda','delivery') NOT NULL,
  `fecha_despacho` date NOT NULL,
  `hora_despacho` time DEFAULT NULL,
  `tienda_nombre` varchar(200) DEFAULT NULL,
  `tienda_direccion` varchar(300) DEFAULT NULL,
  `tienda_telefono` varchar(20) DEFAULT NULL,
  `direccion_entrega` varchar(300) DEFAULT NULL,
  `telefono_contacto1` varchar(20) DEFAULT NULL,
  `telefono_contacto2` varchar(20) DEFAULT NULL,
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
  `nombre_producto` varchar(150) NOT NULL COMMENT 'Snapshot del nombre al momento de compra',
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_pedido` (`id_pedido`),
  KEY `fk_detalle_producto` (`id_producto`),
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `detalle_pedido` VALUES ('1', '4', '1', 'Funda Universal para Celular', '2', '12.99', '25.98');
INSERT INTO `detalle_pedido` VALUES ('2', '4', '3', 'Parlante Bluetooth Portátil', '3', '45.99', '137.97');
INSERT INTO `detalle_pedido` VALUES ('3', '4', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('4', '5', '2', 'Cargador Rápido 20W USB-C', '1', '18.99', '18.99');
INSERT INTO `detalle_pedido` VALUES ('5', '5', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('6', '5', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');
INSERT INTO `detalle_pedido` VALUES ('7', '6', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('8', '6', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('9', '6', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');
INSERT INTO `detalle_pedido` VALUES ('10', '7', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('11', '7', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('12', '7', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');
INSERT INTO `detalle_pedido` VALUES ('13', '8', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('14', '8', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('15', '8', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');
INSERT INTO `detalle_pedido` VALUES ('16', '9', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('17', '9', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('18', '9', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');
INSERT INTO `detalle_pedido` VALUES ('19', '10', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('20', '10', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('21', '10', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');
INSERT INTO `detalle_pedido` VALUES ('22', '11', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('23', '11', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('24', '11', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');
INSERT INTO `detalle_pedido` VALUES ('25', '12', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('26', '12', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('27', '12', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');
INSERT INTO `detalle_pedido` VALUES ('28', '13', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('29', '13', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('30', '13', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');
INSERT INTO `detalle_pedido` VALUES ('31', '14', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('32', '14', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('33', '14', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');
INSERT INTO `detalle_pedido` VALUES ('34', '15', '2', 'Cargador Rápido 20W USB-C', '2', '18.99', '37.98');
INSERT INTO `detalle_pedido` VALUES ('35', '15', '3', 'Parlante Bluetooth Portátil', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('36', '15', '4', 'Lámpara LED Inteligente', '1', '28.99', '28.99');


DROP TABLE IF EXISTS `detalle_ventas`;
CREATE TABLE `detalle_ventas` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_venta` (`id_venta`),
  CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `detalle_ventas` VALUES ('12', '12', '2', '4', '10.00');
INSERT INTO `detalle_ventas` VALUES ('13', '13', '2', '3', '10.00');
INSERT INTO `detalle_ventas` VALUES ('14', '14', '2', '3', '10.00');
INSERT INTO `detalle_ventas` VALUES ('15', '15', '2', '3', '10.00');
INSERT INTO `detalle_ventas` VALUES ('16', '16', '2', '1', '10.00');
INSERT INTO `detalle_ventas` VALUES ('17', '17', '2', '3', '10.00');
INSERT INTO `detalle_ventas` VALUES ('18', '18', '2', '3', '10.00');
INSERT INTO `detalle_ventas` VALUES ('19', '19', '2', '3', '10.00');
INSERT INTO `detalle_ventas` VALUES ('20', '20', '2', '3', '10.00');
INSERT INTO `detalle_ventas` VALUES ('21', '21', '2', '2', '10.00');


DROP TABLE IF EXISTS `detalles_entrada`;
CREATE TABLE `detalles_entrada` (
  `id_entrada_fk` int(11) NOT NULL,
  `id_producto_fk` int(11) NOT NULL,
  `cantidad_entrada` decimal(12,2) NOT NULL,
  KEY `id_entrada_fk` (`id_entrada_fk`),
  KEY `id_producto_fk` (`id_producto_fk`),
  CONSTRAINT `detalles_entrada_ibfk_1` FOREIGN KEY (`id_entrada_fk`) REFERENCES `entradas_productos` (`id_entrada`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `detalles_entrada` VALUES ('60', '1', '2.00');
INSERT INTO `detalles_entrada` VALUES ('61', '2', '5.00');
INSERT INTO `detalles_entrada` VALUES ('62', '1', '10.00');


DROP TABLE IF EXISTS `direcciones`;
CREATE TABLE `direcciones` (
  `id_direccion` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(20) DEFAULT NULL COMMENT 'NULL si es invitado',
  `email_invitado` varchar(100) DEFAULT NULL COMMENT 'Si es invitado, su email',
  `nombre_completo` varchar(150) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `calle` varchar(150) NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `id_zona` int(11) NOT NULL,
  `referencia` text DEFAULT NULL,
  PRIMARY KEY (`id_direccion`),
  KEY `fk_direcciones_cliente` (`cedula_cliente`),
  KEY `fk_direcciones_zona` (`id_zona`),
  CONSTRAINT `fk_direcciones_cliente` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula_cliente`) ON DELETE CASCADE,
  CONSTRAINT `fk_direcciones_zona` FOREIGN KEY (`id_zona`) REFERENCES `zonas_envio` (`id_zona`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `direcciones` VALUES ('1', NULL, NULL, 'Cliente', '00000000', 'Sin dirección', 'Barquisimeto', NULL, '1', NULL);


DROP TABLE IF EXISTS `empleados`;
CREATE TABLE `empleados` (
  `cedula_empleado` varchar(20) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(150) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `perfil` varchar(20) NOT NULL DEFAULT 'no',
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  `id_cargo` int(12) NOT NULL,
  PRIMARY KEY (`cedula_empleado`),
  KEY `id_cargo` (`id_cargo`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `empleados` VALUES ('28000360', 'Anny', 'Perez', '04141234567', 'Sede Principal', 'anny@edwardaccesorios.com', 'cajera', 'activo', '1');
INSERT INTO `empleados` VALUES ('hotelcampana', 'Admin', 'Sistema', '000', 'Sede', 'admin@admin.com', 'suspendido', 'activo', '1');
INSERT INTO `empleados` VALUES ('V-0000000', 'Edward', 'Accesorios', '04120000000', 'Cosmo Centro', 'admin@gmail.com', 'no', 'activo', '1');
INSERT INTO `empleados` VALUES ('V-20123456', 'Edward', 'Sistema', '04125550000', 'Barquisimeto, Lara', 'edward@ejemplo.com', 'si', 'activo', '1');
INSERT INTO `empleados` VALUES ('V-30753799', 'Jose', 'Carrillo', '04121536417', 'yucatan via duaca km 14', 'josecarrillogc@gmail.com', 'no', 'activo', '1');


DROP TABLE IF EXISTS `entradas_productos`;
CREATE TABLE `entradas_productos` (
  `id_entrada` int(11) NOT NULL AUTO_INCREMENT,
  `rif_proveedor_fk` varchar(30) NOT NULL,
  `fecha_entrada` datetime NOT NULL,
  PRIMARY KEY (`id_entrada`),
  KEY `rif_proveedor_fk` (`rif_proveedor_fk`),
  CONSTRAINT `entradas_productos_ibfk_1` FOREIGN KEY (`rif_proveedor_fk`) REFERENCES `proveedores` (`rif_proveedor`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `entradas_productos` VALUES ('60', 'J-8777999', '2026-05-28 07:11:37');
INSERT INTO `entradas_productos` VALUES ('61', 'J-8777999', '2026-05-28 07:15:40');
INSERT INTO `entradas_productos` VALUES ('62', 'J-8777999', '2026-05-28 07:34:21');


DROP TABLE IF EXISTS `especialidades`;
CREATE TABLE `especialidades` (
  `id_especialidad` int(12) NOT NULL AUTO_INCREMENT,
  `nombre_especialidad` varchar(30) NOT NULL,
  PRIMARY KEY (`id_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `especialidades` VALUES ('18', 'Apple');
INSERT INTO `especialidades` VALUES ('19', 'Android');


DROP TABLE IF EXISTS `evaluaciones_crediticias`;
CREATE TABLE `evaluaciones_crediticias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(20) NOT NULL,
  `puntaje` decimal(5,2) NOT NULL,
  `nivel_riesgo` enum('muy_bajo','bajo','medio_bajo','medio','medio_alto','alto') NOT NULL,
  `monto_maximo` decimal(12,2) DEFAULT NULL,
  `fecha_evaluacion` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cliente_fecha` (`cedula_cliente`,`fecha_evaluacion`),
  KEY `idx_evaluacion_nivel` (`nivel_riesgo`),
  CONSTRAINT `fk_evaluacion_cliente` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `financiamientos`;
CREATE TABLE `financiamientos` (
  `id_financiamiento` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_productos` int(11) NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `pago_inicial` decimal(10,2) NOT NULL,
  `cantidad_cuotas` int(11) NOT NULL,
  `monto_cuota` decimal(10,2) NOT NULL,
  `dia_pago` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `estado_equipo` enum('activo','bloqueado') DEFAULT 'activo',
  `estatus_financiamiento` varchar(20) DEFAULT 'vigente',
  PRIMARY KEY (`id_financiamiento`),
  KEY `fk_cliente_finan` (`cedula_cliente`),
  KEY `fk_telefono_finan` (`id_productos`),
  KEY `id_productos` (`id_productos`),
  CONSTRAINT `financiamientos_ibfk_1` FOREIGN KEY (`id_productos`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cliente_finan` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula_cliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `financiamientos` VALUES ('11', 'V-40678456', '1', '1020.00', '300.00', '5', '144.00', '1', '2026-05-28', 'activo', 'vigente');


DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_marca` varchar(100) NOT NULL,
  PRIMARY KEY (`id_marca`),
  UNIQUE KEY `nombre_marca` (`nombre_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO `marcas` VALUES ('1', 'Apple');
INSERT INTO `marcas` VALUES ('3', 'Generico');
INSERT INTO `marcas` VALUES ('4', 'Papel');
INSERT INTO `marcas` VALUES ('2', 'Samsung');


DROP TABLE IF EXISTS `metodo_pago`;
CREATE TABLE `metodo_pago` (
  `id_metodopago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_metodopago` varchar(100) NOT NULL,
  `cuenta` varchar(50) DEFAULT '0',
  `estatus` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_metodopago`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `metodo_pago` VALUES ('1', 'Punto De Venta', '0', '1');
INSERT INTO `metodo_pago` VALUES ('2', 'Divisa', '0', '1');
INSERT INTO `metodo_pago` VALUES ('3', 'Pago Movil', '0', '1');


DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(20) DEFAULT NULL,
  `cedula_empleado` varchar(20) DEFAULT NULL,
  `tipo_notificacion` enum('pago_reportado','pago_confirmado','despacho_listo','pedido_entregado','sistema') NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `mensaje` text NOT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notificacion`),
  KEY `idx_cliente` (`cedula_cliente`),
  KEY `idx_empleado` (`cedula_empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `observaciones_turno`;
CREATE TABLE `observaciones_turno` (
  `id_observacion` int(12) NOT NULL AUTO_INCREMENT,
  `id_turno` int(12) NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id_observacion`),
  KEY `fk_turno_obs` (`id_turno`),
  CONSTRAINT `fk_turno_obs` FOREIGN KEY (`id_turno`) REFERENCES `turnos` (`id_turno`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `observaciones_turno` VALUES ('4', '4', 'arepa');


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
  `transaccion_id` varchar(100) DEFAULT NULL,
  `ip_cliente` varchar(45) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `fk_pedidos_cliente` (`cedula_cliente`),
  KEY `fk_pedidos_direccion` (`id_direccion_envio`),
  CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula_cliente`) ON DELETE SET NULL,
  CONSTRAINT `fk_pedidos_direccion` FOREIGN KEY (`id_direccion_envio`) REFERENCES `direcciones` (`id_direccion`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pedidos` VALUES ('4', NULL, 'gustmen1@gmail.com', 'gustavoGimenez', '0251-4433184', '2026-05-17 04:06:15', '201.93', '0.00', '201.93', 'pendiente', NULL, '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `pedidos` VALUES ('5', NULL, 'migment1@gmail.com', 'gustavo Gimenez', '0251-4433184', '2026-05-21 18:19:21', '93.97', '0.00', '93.97', '', '{\"metodo\":\"transferencia\",\"banco_receptor\":1,\"banco_emisor\":\"10\",\"fecha_seleccion\":\"2026-05-22 00:19:21\"}', NULL, NULL, 'transferencia', '', NULL, NULL);
INSERT INTO `pedidos` VALUES ('6', 'V-12345678', 'migment1@gmail.com', 'gustavo Gimenez', '04161768520', '2026-05-21 19:37:21', '112.96', '0.00', '112.96', '', '{\"metodo\":\"transferencia\",\"banco_receptor\":1,\"banco_emisor\":\"9\",\"fecha_seleccion\":\"2026-05-22 01:37:21\"}', NULL, NULL, 'transferencia', '', NULL, NULL);
INSERT INTO `pedidos` VALUES ('7', 'V-12345678', 'migment1@gmail.com', 'gustavo Gimenez', '04161768520', '2026-05-21 19:39:29', '112.96', '0.00', '112.96', '', '{\"metodo\":\"transferencia\",\"banco_receptor\":1,\"banco_emisor\":\"6\",\"fecha_seleccion\":\"2026-05-22 01:39:29\"}', NULL, NULL, 'transferencia', '', NULL, NULL);
INSERT INTO `pedidos` VALUES ('8', 'V-12345678', 'migment1@gmail.com', 'gustavo Gimenez', '0251-4433184', '2026-05-21 19:41:44', '112.96', '0.00', '112.96', '', '{\"metodo\":\"pago_movil\",\"banco_receptor\":2,\"banco_emisor\":\"7\",\"fecha_seleccion\":\"2026-05-22 01:41:44\"}', NULL, NULL, 'pago_movil', '', NULL, NULL);
INSERT INTO `pedidos` VALUES ('9', 'V-12345678', 'migment1@gmail.com', 'gustavo Gimenez', '0251-4433184', '2026-05-21 19:54:17', '112.96', '0.00', '112.96', '', '{\"metodo\":\"transferencia\",\"banco_receptor\":1,\"banco_emisor\":\"9\",\"fecha_seleccion\":\"2026-05-22 01:54:17\"}', NULL, NULL, 'transferencia', '', NULL, NULL);
INSERT INTO `pedidos` VALUES ('10', 'V-12345678', 'migment1@gmail.com', 'gustavo Gimenez', '0416-1768520', '2026-05-21 20:53:40', '112.96', '0.00', '112.96', '', '{\"metodo\":\"transferencia\",\"banco_receptor\":1,\"banco_emisor\":\"6\",\"fecha_seleccion\":\"2026-05-22 02:53:40\"}', NULL, NULL, 'transferencia', '', NULL, NULL);
INSERT INTO `pedidos` VALUES ('11', 'V-12345678', 'cliente@prueba.com', 'Cliente Prueba', '04121234567', '2026-05-25 13:11:46', '112.96', '0.00', '112.96', '', '{\"metodo\":\"pago_movil\",\"banco_receptor\":2,\"banco_emisor\":\"6\",\"fecha_seleccion\":\"2026-05-25 19:11:46\"}', NULL, NULL, 'pago_movil', '', NULL, NULL);
INSERT INTO `pedidos` VALUES ('12', 'V-12345678', 'cliente@prueba.com', 'Cliente Prueba', '04121234567', '2026-05-25 13:26:53', '112.96', '0.00', '112.96', '', '{\"metodo\":\"pago_movil\",\"banco_receptor\":2,\"banco_emisor\":\"6\",\"fecha_seleccion\":\"2026-05-25 19:26:53\"}', NULL, NULL, 'pago_movil', '', NULL, NULL);
INSERT INTO `pedidos` VALUES ('13', 'V-12345678', 'cliente@prueba.com', 'Cliente Prueba', '04121234567', '2026-05-25 13:27:55', '112.96', '0.00', '112.96', '', '{\"metodo\":\"pago_movil\",\"banco_receptor\":2,\"banco_emisor\":\"6\",\"fecha_seleccion\":\"2026-05-25 19:27:55\"}', NULL, NULL, 'pago_movil', '', NULL, NULL);
INSERT INTO `pedidos` VALUES ('14', 'V-12345678', 'cliente@prueba.com', 'Cliente Prueba', '04121234567', '2026-05-25 13:29:02', '112.96', '0.00', '112.96', '', '{\"metodo\":\"pago_movil\",\"banco_receptor\":2,\"banco_emisor\":\"6\",\"fecha_seleccion\":\"2026-05-25 19:29:02\"}', NULL, NULL, 'pago_movil', '', NULL, NULL);
INSERT INTO `pedidos` VALUES ('15', 'V-12345678', 'cliente@prueba.com', 'Cliente Prueba', '04121234567', '2026-05-25 13:30:26', '112.96', '0.00', '112.96', 'pendiente', '{\"metodo\":\"pago_movil\",\"banco_receptor\":2,\"banco_emisor\":\"6\",\"fecha_seleccion\":\"2026-05-25 19:30:26\"}', NULL, NULL, 'pago_movil', '', NULL, NULL);


DROP TABLE IF EXISTS `perfiles_financiamiento`;
CREATE TABLE `perfiles_financiamiento` (
  `id_perfil` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_cliente` varchar(20) NOT NULL,
  `tipo_residencia` enum('Propia','Familiar','Alquilada') DEFAULT 'Familiar',
  `carga_familiar` int(11) DEFAULT 0,
  `estado_civil` enum('Soltero','Casado','Divorciado','Viudo','En relación') DEFAULT 'Soltero',
  `profesion` enum('Empleado','Independiente','Estudiante (Becado)','Estudiante','Pensionado','Desempleado') DEFAULT 'Empleado',
  `ocupacion` varchar(100) DEFAULT NULL,
  `ingresos_mensuales` decimal(10,2) DEFAULT 0.00,
  `score_credito` int(11) DEFAULT 5,
  PRIMARY KEY (`id_perfil`),
  KEY `fk_perfil_cliente` (`cedula_cliente`),
  CONSTRAINT `fk_perfil_cliente` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `perfiles_financiamiento` VALUES ('1', 'V-40678456', 'Propia', '0', 'Soltero', 'Empleado', 'cajera', '146.90', '5');


DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_producto` varchar(150) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `productos` VALUES ('1', 'Iphone 17', 'usado sin rayones', '26', '1', '0', '1', '1', '1020.00', '1');
INSERT INTO `productos` VALUES ('2', 'Forros de silicona', 'Multicolores', '24', '3', '3', '20', '2', '10.00', '1');
INSERT INTO `productos` VALUES ('3', 'Galaxy s24', 'Samsung Nuevo', '26', '2', '0', '1', '1', '650.00', '1');
INSERT INTO `productos` VALUES ('5', 'Iphone xs', 'usado 87%', '26', '1', '0', '1', '1', '200.00', '1');


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


DROP TABLE IF EXISTS `reportes_pago`;
CREATE TABLE `reportes_pago` (
  `id_reporte` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `cedula_cliente` varchar(20) DEFAULT NULL,
  `referencia` varchar(50) NOT NULL,
  `banco_emisor` varchar(50) NOT NULL,
  `banco_receptor` varchar(50) DEFAULT NULL,
  `telefono_transferencia` varchar(20) NOT NULL,
  `monto_reportado` decimal(10,2) NOT NULL,
  `fecha_transferencia` date NOT NULL,
  `nombre_pagador` varchar(200) NOT NULL,
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
  CONSTRAINT `reportes_pago_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  CONSTRAINT `reportes_pago_ibfk_2` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula_cliente`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reportes_pago` VALUES ('1', '15', 'V-12345678', '0584678906', 'Banco Banesco', 'Banesco', '0412-1234567', '12950000.00', '2026-05-25', 'Cliente Prueba', 'V-12345678', NULL, 'pendiente', '2026-05-25 17:32:17', NULL, NULL, NULL);


DROP TABLE IF EXISTS `turno_empleado`;
CREATE TABLE `turno_empleado` (
  `id_turno_empleado` int(12) NOT NULL AUTO_INCREMENT,
  `id_turno` int(12) NOT NULL,
  `cedula_empleado` varchar(20) NOT NULL,
  PRIMARY KEY (`id_turno_empleado`),
  KEY `fk_turno` (`id_turno`),
  KEY `fk_empleado_turno` (`cedula_empleado`),
  CONSTRAINT `fk_empleado_turno` FOREIGN KEY (`cedula_empleado`) REFERENCES `empleados` (`cedula_empleado`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_turno` FOREIGN KEY (`id_turno`) REFERENCES `turnos` (`id_turno`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `turno_empleado` VALUES ('6', '4', 'V-0000000');


DROP TABLE IF EXISTS `turnos`;
CREATE TABLE `turnos` (
  `id_turno` int(12) NOT NULL AUTO_INCREMENT,
  `fecha_turno` date NOT NULL,
  `hora_entrada` time NOT NULL,
  `hora_salida` time NOT NULL,
  PRIMARY KEY (`id_turno`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `turnos` VALUES ('4', '2026-04-14', '10:54:00', '05:54:00');


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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `unidades_telefonos` VALUES ('1', '1', '23423423423234', '8', '128', 'Disponible', '2026-05-27 23:17:52');
INSERT INTO `unidades_telefonos` VALUES ('2', '3', '4876523834234', '8gb', '128', 'Disponible', '2026-05-28 13:03:20');
INSERT INTO `unidades_telefonos` VALUES ('3', '5', '32423423423432', '8', '128', 'Disponible', '2026-05-29 04:26:14');


DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(20) NOT NULL,
  `fecha` datetime NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `direccion` text NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `estado` int(11) DEFAULT 1,
  `referencia_pago` varchar(100) DEFAULT NULL,
  `tipo_envio` varchar(50) DEFAULT NULL,
  `notas_envio` text DEFAULT NULL,
  PRIMARY KEY (`id_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ventas` VALUES ('12', 'Cliente Genérico', '2026-05-28 21:16:37', '40.00', 'Divisa', 'Venta Presencial', 'N/A', '1', '', NULL, NULL);
INSERT INTO `ventas` VALUES ('13', 'Cliente Genérico', '2026-05-28 21:19:03', '30.00', 'Divisa', 'Venta Presencial', 'N/A', '1', '', NULL, NULL);
INSERT INTO `ventas` VALUES ('14', 'Cliente Genérico', '2026-05-28 21:22:36', '30.00', 'Divisa', 'Venta Presencial', 'N/A', '1', '', NULL, NULL);
INSERT INTO `ventas` VALUES ('15', 'Cliente Genérico', '2026-05-28 21:24:31', '30.00', 'Divisa', 'Venta Presencial', 'N/A', '1', '', NULL, NULL);
INSERT INTO `ventas` VALUES ('16', 'Cliente Genérico', '2026-05-28 21:26:56', '10.00', 'Divisa', 'Venta Presencial', 'N/A', '1', '', NULL, NULL);
INSERT INTO `ventas` VALUES ('17', 'Cliente Genérico', '2026-05-28 21:29:49', '30.00', 'Divisa', 'Venta Presencial', 'N/A', '1', '', NULL, NULL);
INSERT INTO `ventas` VALUES ('18', 'Cliente Genérico', '2026-05-28 21:34:01', '30.00', 'Divisa', 'Venta Presencial', 'N/A', '1', '', NULL, NULL);
INSERT INTO `ventas` VALUES ('19', 'Cliente Genérico', '2026-05-28 21:37:36', '30.00', 'Divisa', 'Venta Presencial', 'N/A', '1', '', NULL, NULL);
INSERT INTO `ventas` VALUES ('20', 'Cliente Genérico', '2026-05-28 21:39:37', '30.00', 'Divisa', 'Venta Presencial', 'N/A', '1', '', NULL, NULL);
INSERT INTO `ventas` VALUES ('21', 'Cliente Genérico', '2026-05-28 21:44:12', '20.00', 'Divisa', 'Venta Presencial', 'N/A', '1', '', NULL, NULL);


DROP TABLE IF EXISTS `zonas_envio`;
CREATE TABLE `zonas_envio` (
  `id_zona` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `costo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tiempo_estimado` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_zona`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `zonas_envio` VALUES ('1', 'Barquisimeto Centro', '0.00', '24 horas', '1');
INSERT INTO `zonas_envio` VALUES ('2', 'Barquisimeto Este', '5.00', '2 días', '1');
INSERT INTO `zonas_envio` VALUES ('3', 'Barquisimeto Oeste', '5.00', '2 días', '1');
INSERT INTO `zonas_envio` VALUES ('4', 'Municipio Iribarren (otros)', '8.00', '3 días', '1');
INSERT INTO `zonas_envio` VALUES ('5', 'Municipios cercanos (Palavecino, Crespo)', '10.00', '4-5 días', '1');
INSERT INTO `zonas_envio` VALUES ('6', 'Otros municipios de Lara', '15.00', '5-7 días', '1');


SET FOREIGN_KEY_CHECKS=1;