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


DROP TABLE IF EXISTS `carrito_sesion`;
CREATE TABLE `carrito_sesion` (
  `id_sesion` varchar(128) NOT NULL,
  `cedula_cliente` varchar(20) DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_sesion`),
  KEY `fk_carrito_cliente` (`cedula_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(12) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(30) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorias` VALUES ('22', 'Productos De Casa');
INSERT INTO `categorias` VALUES ('24', 'Accesorios');
INSERT INTO `categorias` VALUES ('26', 'Telefono');


DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `cedula_persona` varchar(20) NOT NULL,
  `residencia` text NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  `referencia_nombre` varchar(100) DEFAULT NULL,
  `referencia_telefono` varchar(20) DEFAULT NULL,
  `suscrito_boletin` tinyint(1) DEFAULT 0,
  `fecha_suscripcion` datetime DEFAULT NULL,
  `token_recuperacion` varchar(255) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL,
  UNIQUE KEY `cedula_persona` (`cedula_persona`),
  KEY `cedula_persona_2` (`cedula_persona`),
  CONSTRAINT `fk_cliente_persona` FOREIGN KEY (`cedula_persona`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` VALUES ('30000000', 'No especificado', 'activo', NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `clientes` VALUES ('V-3242343', 'Centro.cubiro', 'activo', NULL, NULL, '0', NULL, NULL, NULL);
INSERT INTO `clientes` VALUES ('V-34567567', 'Zona norte carorita km 10', 'activo', NULL, NULL, '0', NULL, NULL, NULL);


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
  UNIQUE KEY `uk_cedula` (`cedula_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `credenciales_online` VALUES ('3', '30000000', '$2y$10$EQC5cDRRThy7PK/lkkXsHOgBB92MWcsGgG6tbHOxt0dv3EGaZU7aC', '2026-06-12 07:20:39', '0', NULL, '1');


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
  KEY `idx_creditos_cliente` (`cedula_cliente`)
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
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cuotas` VALUES ('95', '24', '1', '2026-07-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);
INSERT INTO `cuotas` VALUES ('96', '24', '2', '2026-08-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);
INSERT INTO `cuotas` VALUES ('97', '24', '3', '2026-09-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);
INSERT INTO `cuotas` VALUES ('98', '24', '4', '2026-10-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);
INSERT INTO `cuotas` VALUES ('99', '24', '5', '2026-11-01', '0.00', NULL, 'pendiente', NULL, NULL, NULL);


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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `detalle_pedido` VALUES ('48', '27', '20', 'Parlante Bluetooth', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('49', '27', '18', 'Kit de Limpieza', '1', '12.99', '12.99');
INSERT INTO `detalle_pedido` VALUES ('50', '28', '20', 'Parlante Bluetooth', '1', '45.99', '45.99');
INSERT INTO `detalle_pedido` VALUES ('51', '28', '18', 'Kit de Limpieza', '1', '12.99', '12.99');


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
  `dias_garantia` int(11) NOT NULL,
  KEY `id_entrada_fk` (`id_entrada_fk`),
  KEY `id_producto_fk` (`id_producto_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



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
  CONSTRAINT `fk_direcciones_zona` FOREIGN KEY (`id_zona`) REFERENCES `zonas_envio` (`id_zona`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



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
INSERT INTO `empleados` VALUES ('V-56789456', 'no', 'activo', '1');


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
  KEY `idx_evaluacion_nivel` (`nivel_riesgo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `financiamientos`;
CREATE TABLE `financiamientos` (
  `id_financiamiento` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_persona` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_productos` int(11) NOT NULL,
  `id_unidad` int(11) DEFAULT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `pago_inicial` decimal(10,2) NOT NULL,
  `cantidad_cuotas` int(11) NOT NULL,
  `monto_cuota` decimal(10,2) NOT NULL,
  `dia_pago` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `estado_equipo` enum('activo','bloqueado') DEFAULT 'activo',
  `estatus_financiamiento` varchar(20) DEFAULT 'vigente',
  PRIMARY KEY (`id_financiamiento`),
  KEY `fk_cliente_finan` (`cedula_persona`),
  KEY `fk_telefono_finan` (`id_productos`),
  KEY `id_productos` (`id_productos`),
  CONSTRAINT `financiamientos_ibfk_1` FOREIGN KEY (`id_productos`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_financiamiento_persona` FOREIGN KEY (`cedula_persona`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `financiamientos` VALUES ('24', 'V-34567567', '9', '19', '1020.00', '100.00', '5', '184.00', '1', '2026-06-12', 'activo', 'vigente');


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
INSERT INTO `marcas` VALUES ('33', 'Test');
INSERT INTO `marcas` VALUES ('28', 'Test1');
INSERT INTO `marcas` VALUES ('29', 'Test2');
INSERT INTO `marcas` VALUES ('30', 'Test3');
INSERT INTO `marcas` VALUES ('31', 'Test4');
INSERT INTO `marcas` VALUES ('32', 'Test5');


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
  CONSTRAINT `fk_pedidos_direccion` FOREIGN KEY (`id_direccion_envio`) REFERENCES `direcciones` (`id_direccion`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pedidos` VALUES ('27', '30000000', 'PruebaCliente@gmail.com', 'Cliente Prueba', '00000000000', '2026-06-12 07:28:38', '68.98', '10.00', '68.98', 'pendiente', '{\"metodo\":\"pago_movil\",\"banco_emisor\":\"3\",\"fecha_seleccion\":\"2026-06-12 07:28:38\"}', NULL, NULL, 'pago_movil', NULL, NULL, NULL);
INSERT INTO `pedidos` VALUES ('28', '30000000', 'PruebaCliente@gmail.com', 'Cliente Prueba', '00000000000', '2026-06-12 08:00:44', '68.98', '10.00', '68.98', 'pendiente', '{\"metodo\":\"pago_movil\",\"banco_emisor\":\"8\",\"fecha_seleccion\":\"2026-06-12 08:00:44\"}', NULL, NULL, 'pago_movil', NULL, NULL, NULL);


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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `perfiles_financiamiento` VALUES ('3', 'V-3242343', 'Propia', '0', 'Soltero', 'Empleado', 'cajera', '150.52', '5');
INSERT INTO `perfiles_financiamiento` VALUES ('4', 'V-34567567', 'Propia', '0', 'Soltero', 'Empleado', 'Cajero', '650.46', '8');


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
INSERT INTO `persona` VALUES ('V-3242343', 'pedro', 'garcia', 'garpedro@gmail.com', '04240001020', '2006-02-08', 'Masculino', 'Centro.cubiro');
INSERT INTO `persona` VALUES ('V-34567567', 'Gabriel Noriega', 'Marquez', 'Gabriel_2026@gmail.com', '04268004070', '2003-02-12', 'Masculino', 'Zona norte carorita km 10');
INSERT INTO `persona` VALUES ('V-4562434', 'Gabriela alejandra', 'Garcia Silva', 'gabriela_priv@gmail.com', '04121005540', '2026-06-04', 'F', 'Barquisimeto urb don jesus');
INSERT INTO `persona` VALUES ('V-56789456', 'Juan josue', 'Mingueza palomo', 'juancito_josue@gmail.com', '04164007890', '2005-02-11', 'M', 'Zona norte duaca intercomunal 14');


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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `productos` VALUES ('9', 'Iphone 17', NULL, 'usado sin rayones', 'iphone17.jpg', '26', '1', '0', '1', '1', '1020.00', '1');
INSERT INTO `productos` VALUES ('10', 'Xiaomi Redmi Note 13', NULL, 'Smartphone 8GB RAM, 128GB', 'xiaomi_redmi13.jpg', '26', '1', '0', '0', '10', '320.00', '1');
INSERT INTO `productos` VALUES ('11', 'Samsung Galaxy A54', NULL, '256GB, pantalla 6.4\"', 'samsung_a54.jpg', '26', '1', '0', '0', '5', '450.00', '1');
INSERT INTO `productos` VALUES ('12', 'AirPods Pro 2', NULL, 'Audífonos inalámbricos', 'airpods_pro2.jpg', '24', '1', '0', '0', '15', '180.00', '1');
INSERT INTO `productos` VALUES ('13', 'Cargador Rápido 33W', NULL, 'USB-C carga rápida', NULL, '24', '1', '0', '0', '50', '25.00', '1');
INSERT INTO `productos` VALUES ('14', 'Funda Silicona iPhone 17', NULL, 'Funda protectora', NULL, '24', '1', '0', '0', '30', '15.00', '1');
INSERT INTO `productos` VALUES ('15', 'Cargador 20W', NULL, 'Cargador rápido USB-C 20W', 'cargador_20w.jpg', '24', '1', '0', '0', '50', '18.99', '1');
INSERT INTO `productos` VALUES ('16', 'Cable USB-C', NULL, 'Cable USB-C a USB-C 1m', 'cable_usbc.jpg', '24', '1', '0', '0', '100', '8.99', '1');
INSERT INTO `productos` VALUES ('17', 'Enchufe Inteligente', NULL, 'Enchufe WiFi compatible Alexa', 'enchufe_inteligente.jpg', '22', '1', '0', '0', '30', '25.99', '1');
INSERT INTO `productos` VALUES ('18', 'Kit de Limpieza', NULL, 'Kit completo para celulares', 'kit_limpieza.jpg', '24', '1', '0', '0', '40', '12.99', '1');
INSERT INTO `productos` VALUES ('19', 'Lámpara LED', NULL, 'Lámpara inteligente RGB', 'lampara_led.jpg', '22', '1', '0', '0', '25', '28.99', '1');
INSERT INTO `productos` VALUES ('20', 'Parlante Bluetooth', NULL, 'Parlante portátil 10W', 'parlante_bt.jpg', '22', '1', '0', '0', '20', '45.99', '1');
INSERT INTO `productos` VALUES ('21', 'Soporte Auto', NULL, 'Soporte magnético para auto', 'soporte_auto.jpg', '24', '1', '0', '0', '60', '15.99', '1');
INSERT INTO `productos` VALUES ('22', 'Raton', NULL, 'Raton nuevo', NULL, '24', '2', '2', '10', '3', '10.00', '1');
INSERT INTO `productos` VALUES ('23', 'Iphone xs max', NULL, '86% bateria', NULL, '26', '1', '0', '1', '1', '180.00', '1');


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
  CONSTRAINT `reportes_pago_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reportes_pago` VALUES ('2', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '04144563456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:29:34', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('3', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '04144563456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:30:29', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('4', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '04144563456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:31:46', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('5', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '04144563456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:34:40', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('6', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '04144563456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:35:48', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('7', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '04144563456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:37:01', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('8', '27', '30000000', '4353453', 'Banco Venezuela', NULL, '04144563456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:37:34', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('9', '27', '30000000', '234234', 'banco de venezuela', NULL, '2342343242', '40.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:38:35', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('10', '27', '30000000', '23423423', 'Banco Venezuela', NULL, '04525673456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:40:01', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('11', '27', '30000000', '23423423', 'Banco Venezuela', NULL, '04525673456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:40:41', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('12', '27', '30000000', '23423423', 'Banco Venezuela', NULL, '04525673456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:41:06', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('13', '27', '30000000', '12312312', 'Banco Venezuela', NULL, '0414-1444444', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 07:42:28', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('14', '28', '30000000', '12312312', 'Banco Venezuela', NULL, '04525673456', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 08:03:34', NULL, NULL, NULL);
INSERT INTO `reportes_pago` VALUES ('15', '28', '30000000', '12312312', 'Banco Venezuela', NULL, '00000000000', '69.00', '2026-06-12', 'Cliente Prueba', '30000000', NULL, 'pendiente', '2026-06-12 08:07:24', NULL, NULL, NULL);


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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `unidades_telefonos` VALUES ('19', '9', '351111111111111', '8GB', '128GB', 'Financiado', '2026-06-03 18:21:19');
INSERT INTO `unidades_telefonos` VALUES ('20', '10', '352222222222222', '8GB', '128GB', 'Disponible', '2026-06-03 18:21:19');
INSERT INTO `unidades_telefonos` VALUES ('21', '11', '353333333333333', '8GB', '256GB', 'Disponible', '2026-06-03 18:21:19');
INSERT INTO `unidades_telefonos` VALUES ('22', '23', '13562823423423', '8gb', '128gb', 'Disponible', '2026-06-12 04:02:25');


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