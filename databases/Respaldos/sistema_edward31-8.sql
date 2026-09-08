
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `bancos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bancos` (
  `id_banco` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_banco` varchar(100) NOT NULL,
  `codigo_banco` varchar(4) DEFAULT NULL,
  `numero_cuenta` varchar(20) NOT NULL,
  `cedula_banco` int(11) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `estatus` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_banco`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bancos` WRITE;
/*!40000 ALTER TABLE `bancos` DISABLE KEYS */;
INSERT INTO `bancos` VALUES (1,'Provincial',NULL,'23423423423423',435345345,'04525673456','activo','2026-04-13 03:59:39'),(2,'Mercantil',NULL,'423423499',563456743,'04893451234','activo','2026-04-13 04:01:47'),(3,'Banco de Venezuela','0102','01020202154578963784',44445698,'04141234567','activo','2026-05-21 20:30:16'),(4,'Banco Mercantil',NULL,'01050123456789012345',44445698,'04141234568','activo','2026-05-21 20:30:16'),(5,'Banco Provincial',NULL,'01080123456789012345',44445698,'04141234569','activo','2026-05-21 20:30:16'),(6,'Banco Banesco',NULL,'01340123456789012345',44445698,'04141234570','activo','2026-05-21 20:30:16'),(7,'Banco Nacional de Cr├®dito',NULL,'01910123456789012345',44445698,'04141234571','activo','2026-05-21 20:30:16'),(8,'Banco Bicentenario',NULL,'01750123456789012345',44445698,'04141234572','activo','2026-05-21 20:30:16'),(9,'Banco del Tesoro',NULL,'01630123456789012345',44445698,'04141234573','activo','2026-05-21 20:30:16'),(10,'Banco Plaza',NULL,'01380123456789012345',44445698,'04141234574','activo','2026-05-21 20:30:16'),(11,'Banco Activo',NULL,'01900123456789012345',44445698,'04141234575','activo','2026-05-21 20:30:16'),(12,'Banco Caron├¡',NULL,'01280123456789012345',44445698,'04141234576','activo','2026-05-21 20:30:16'),(80,'Banco Venezolano de Cr├®dito','0104','',0,'','activo','2026-08-27 21:07:23'),(81,'Banco del Caribe','0114','',0,'','activo','2026-08-27 21:07:23'),(82,'Banco Exterior','0115','',0,'','activo','2026-08-27 21:07:23'),(83,'Banesco','0134','',0,'','activo','2026-08-27 21:07:23'),(84,'Banco Sofitasa','0137','',0,'','activo','2026-08-27 21:07:23'),(85,'Banco de la Gente Emprendedora','0146','',0,'','activo','2026-08-27 21:07:23'),(86,'Banco Fondo Com├║n','0151','',0,'','activo','2026-08-27 21:07:23'),(87,'100% Banco','0156','',0,'','activo','2026-08-27 21:07:23'),(88,'DelSur','0157','',0,'','activo','2026-08-27 21:07:23'),(89,'Banco Agr├¡cola de Venezuela','0166','',0,'','activo','2026-08-27 21:07:23'),(90,'Bancrecer','0168','',0,'','activo','2026-08-27 21:07:23'),(91,'Mi Banco','0169','',0,'','activo','2026-08-27 21:07:23'),(92,'Bancamiga','0172','',0,'','activo','2026-08-27 21:07:23'),(93,'Banco Internacional de Desarrollo','0173','',0,'','activo','2026-08-27 21:07:23'),(94,'Banplus','0174','',0,'','activo','2026-08-27 21:07:23'),(95,'Banco Bicentenario del Pueblo','0175','',0,'','activo','2026-08-27 21:07:23'),(96,'Banco de la Fuerza Armada Nacional Bolivariana','0177','',0,'','activo','2026-08-27 21:07:23'),(97,'N58 Banco Digital','0178','',0,'','activo','2026-08-27 21:07:23'),(98,'Instituto Municipal de Cr├®dito Popular','0601','',0,'','activo','2026-08-27 21:07:23');
/*!40000 ALTER TABLE `bancos` ENABLE KEYS */;
UNLOCK TABLES;
-- Tabla `bancos_receptor` eliminada: NO forma parte de la estructura de BD firmada.
-- Los datos de las cuentas receptoras ahora viven fijos en
-- app/models/BancoReceptorModel.php
DROP TABLE IF EXISTS `cargos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cargos` (
  `id_cargo` int(12) NOT NULL AUTO_INCREMENT,
  `nombre_cargo` varchar(50) NOT NULL,
  PRIMARY KEY (`id_cargo`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cargos` WRITE;
/*!40000 ALTER TABLE `cargos` DISABLE KEYS */;
INSERT INTO `cargos` VALUES (1,'Admin'),(13,'Cajera');
/*!40000 ALTER TABLE `cargos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id_categoria` int(12) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(30) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (22,'Productos De Casa'),(24,'Accesorios'),(26,'Telefono'),(27,'Repuestos');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `cedula_persona` varchar(20) NOT NULL,
  `residencia` text NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'activo',
  UNIQUE KEY `cedula_persona` (`cedula_persona`),
  KEY `cedula_persona_2` (`cedula_persona`),
  CONSTRAINT `fk_cliente_persona` FOREIGN KEY (`cedula_persona`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES ('V-10222260','LA MATA CABUDARE','activo'),('V-10456123','Barquisimeto cerca de la catedral','activo'),('V-19180654','cabudate centro','activo'),('V-30000000','No especificado','activo'),('V-30000001','av la mata , cabudare','activo'),('V-30155522','entre, y, Calle 48','activo'),('V-310005002','andres bello vereda 2','activo'),('V-3242343','Centro.cubiro','activo'),('V-34567567','Zona norte carorita km 10','activo'),('V-38456234','Zona centro calle 15 con 14 y 16','activo'),('V-7418869','av la salle','activo'),('V-7890345','urbanizacion roca del este','activo');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  CONSTRAINT `fk_cuotas_financiamiento` FOREIGN KEY (`id_financiamiento`) REFERENCES `financiamientos` (`id_financiamiento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cuotas` WRITE;
/*!40000 ALTER TABLE `cuotas` DISABLE KEYS */;
INSERT INTO `cuotas` VALUES (56,7,1,'2026-07-01',205.00,'2026-06-18 22:07:50','pagado',2,NULL,NULL),(57,7,2,'2026-08-01',0.00,NULL,'pendiente',NULL,NULL,NULL),(58,7,3,'2026-09-01',0.00,NULL,'pendiente',NULL,NULL,NULL),(59,7,4,'2026-10-01',0.00,NULL,'pendiente',NULL,NULL,NULL),(60,8,1,'2026-07-01',0.00,NULL,'pendiente',NULL,NULL,NULL),(61,9,1,'2026-07-01',0.00,NULL,'pendiente',NULL,NULL,NULL),(62,10,1,'0011-02-01',0.00,NULL,'pendiente',NULL,NULL,NULL);
/*!40000 ALTER TABLE `cuotas` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_update_cuotas` AFTER UPDATE ON `cuotas` FOR EACH ROW BEGIN
    IF NEW.estado_cuota = 'pagado' AND OLD.estado_cuota = 'pendiente' THEN
        INSERT INTO sistema_edward_usuario.bitacora (tabla_afectada, accion, modulo, usuario_id, id_modulo)
        VALUES ('cuotas', 'Registrar Pago de Cuota', 'Administrar Financiamiento', @usuario_actual, NEW.id_cuota);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `despachos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `despachos` WRITE;
/*!40000 ALTER TABLE `despachos` DISABLE KEYS */;
INSERT INTO `despachos` VALUES (1,38,'delivery','2026-06-26',NULL,'pedro perez','0416-5141324','entre, y, Calle 48','pendiente','2026-06-30',NULL),(2,40,'delivery','2026-06-28',NULL,'pedro perez','0416-5141324','Av la salle','pendiente','2026-06-30',NULL),(4,41,'delivery','2026-07-31',NULL,'PEDRO TUA','04125336758','vereda2 sector andres bello','pendiente','2026-08-02',NULL),(5,42,'delivery','2026-07-31',NULL,'PEDRO TUA','04125336758','entre, y, Calle 48','pendiente','2026-08-01',NULL);
/*!40000 ALTER TABLE `despachos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `detalle_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `detalle_pedido` WRITE;
/*!40000 ALTER TABLE `detalle_pedido` DISABLE KEYS */;
INSERT INTO `detalle_pedido` VALUES (50,28,20,1,45.99,45.99),(51,28,18,1,12.99,12.99),(52,32,16,1,8.99,8.99),(53,32,12,1,180.00,180.00),(54,33,12,1,180.00,180.00),(55,33,16,2,8.99,17.98),(56,34,12,1,180.00,180.00),(57,34,16,1,8.99,8.99),(58,35,12,1,180.00,180.00),(59,35,16,1,8.99,8.99),(60,36,16,1,8.99,8.99),(61,36,15,1,18.99,18.99),(62,37,12,1,180.00,180.00),(63,37,25,1,10.00,10.00),(64,37,14,1,15.00,15.00),(65,38,12,1,180.00,180.00),(66,38,16,1,8.99,8.99),(67,39,12,1,180.00,180.00),(68,39,16,1,8.99,8.99),(69,40,16,2,8.99,17.98),(70,40,15,2,18.99,37.98),(71,41,12,1,180.00,180.00),(72,41,16,1,8.99,8.99),(73,42,25,1,10.00,10.00),(74,42,13,1,25.00,25.00),(75,43,12,1,180.00,180.00),(76,44,22,1,10.00,10.00),(77,44,10,1,320.00,320.00),(78,45,16,1,8.99,8.99),(79,45,15,1,18.99,18.99),(80,46,15,1,18.99,18.99),(81,46,16,1,8.99,8.99),(82,47,15,1,18.99,18.99),(83,47,16,1,8.99,8.99),(84,48,15,2,18.99,37.98),(85,48,27,2,20.00,40.00),(86,49,16,3,8.99,26.97),(87,50,16,2,8.99,17.98),(88,51,16,1,8.99,8.99),(89,51,15,1,18.99,18.99),(90,52,12,14,180.00,2520.00),(91,53,12,10,180.00,1800.00);
/*!40000 ALTER TABLE `detalle_pedido` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `detalle_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_ventas` (
  `id_detalle_ventas` int(11) NOT NULL AUTO_INCREMENT,
  `id_ventas` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_detalle_ventas`),
  KEY `id_ventas` (`id_ventas`),
  KEY `id_producto` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `detalle_ventas` WRITE;
/*!40000 ALTER TABLE `detalle_ventas` DISABLE KEYS */;
INSERT INTO `detalle_ventas` VALUES (10,13,10,1,320.00),(11,18,12,1,180.00),(12,19,12,1,180.00),(15,22,17,1,25.99),(16,23,17,1,25.99),(17,24,17,1,25.99),(19,26,17,1,25.99),(20,27,12,1,180.00),(21,27,16,1,8.99),(22,28,16,2,8.99),(23,28,15,2,18.99),(25,29,14,1,15.00),(26,30,12,1,180.00),(27,30,16,1,8.99),(29,31,25,1,10.00),(30,31,13,1,25.00),(32,32,12,1,180.00),(33,33,20,1,45.99),(34,33,18,1,12.99),(36,34,20,1,45.99),(37,34,18,1,12.99),(39,35,20,1,45.99),(40,35,18,1,12.99),(42,36,20,1,45.99),(43,36,18,1,12.99),(45,37,20,1,45.99),(46,37,18,1,12.99),(48,38,20,1,45.99),(49,38,18,1,12.99),(51,39,20,1,45.99),(52,39,18,1,12.99),(54,40,20,1,45.99),(55,40,18,1,12.99),(57,41,20,1,45.99),(58,41,18,1,12.99),(60,42,20,1,45.99),(61,42,18,1,12.99),(63,43,12,1,180.00),(64,43,16,1,8.99),(66,44,20,1,45.99),(67,44,18,1,12.99),(69,45,20,1,45.99),(70,45,18,1,12.99),(72,46,12,1,180.00),(73,46,25,1,10.00),(74,46,14,1,15.00),(75,47,22,1,10.00),(76,47,10,1,320.00),(78,48,16,1,8.99),(79,48,15,1,18.99),(81,49,15,2,18.99),(82,49,27,2,20.00);
/*!40000 ALTER TABLE `detalle_ventas` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `detalles_entrada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalles_entrada` (
  `id_entrada_fk` int(11) NOT NULL,
  `id_producto_fk` int(11) NOT NULL,
  `cantidad_entrada` decimal(12,2) NOT NULL,
  `dias_garantia` int(11) NOT NULL,
  KEY `id_entrada_fk` (`id_entrada_fk`),
  KEY `id_producto_fk` (`id_producto_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `detalles_entrada` WRITE;
/*!40000 ALTER TABLE `detalles_entrada` DISABLE KEYS */;
INSERT INTO `detalles_entrada` VALUES (67,16,8.00,0),(69,16,9.00,0),(69,18,10.00,6),(70,27,2.00,2);
/*!40000 ALTER TABLE `detalles_entrada` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `detalles_financiamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `detalles_financiamiento` WRITE;
/*!40000 ALTER TABLE `detalles_financiamiento` DISABLE KEYS */;
INSERT INTO `detalles_financiamiento` VALUES (7,7,9,19,'activo'),(8,8,23,22,'bloqueado'),(9,9,11,21,'bloqueado'),(10,10,10,20,'bloqueado');
/*!40000 ALTER TABLE `detalles_financiamiento` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `detalles_servicio_tecnico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalles_servicio_tecnico` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_servicio` int(11) NOT NULL,
  `id_especialidad` int(11) NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `nota_tecnico` text DEFAULT NULL,
  `garantia_dias` int(11) DEFAULT 30,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_servicio` (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `detalles_servicio_tecnico` WRITE;
/*!40000 ALTER TABLE `detalles_servicio_tecnico` DISABLE KEYS */;
INSERT INTO `detalles_servicio_tecnico` VALUES (17,21,18,NULL,NULL,30),(18,22,18,'5667788999',NULL,30);
/*!40000 ALTER TABLE `detalles_servicio_tecnico` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES ('V-4562434','no','activo',1),('V-56789456','si','activo',13),('V-14567234','no','activo',1),('V-22222227','no','activo',1);
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `entradas_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entradas_productos` (
  `id_entrada` int(11) NOT NULL AUTO_INCREMENT,
  `rif_proveedor_fk` varchar(30) NOT NULL,
  `fecha_entrada` datetime NOT NULL,
  PRIMARY KEY (`id_entrada`),
  KEY `rif_proveedor_fk` (`rif_proveedor_fk`),
  CONSTRAINT `entradas_productos_ibfk_1` FOREIGN KEY (`rif_proveedor_fk`) REFERENCES `proveedores` (`rif_proveedor`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `entradas_productos` WRITE;
/*!40000 ALTER TABLE `entradas_productos` DISABLE KEYS */;
INSERT INTO `entradas_productos` VALUES (67,'J-8777999','2026-06-15 07:25:12'),(69,'G-27272727','2026-06-15 07:40:17'),(70,'J-8777999','2026-06-15 12:13:26');
/*!40000 ALTER TABLE `entradas_productos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `especialidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `especialidades` (
  `id_especialidad` int(12) NOT NULL AUTO_INCREMENT,
  `nombre_especialidad` varchar(30) NOT NULL,
  PRIMARY KEY (`id_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `especialidades` WRITE;
/*!40000 ALTER TABLE `especialidades` DISABLE KEYS */;
INSERT INTO `especialidades` VALUES (18,'Apple'),(19,'Android');
/*!40000 ALTER TABLE `especialidades` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `financiamientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `financiamientos` WRITE;
/*!40000 ALTER TABLE `financiamientos` DISABLE KEYS */;
INSERT INTO `financiamientos` VALUES (7,'V-38456234',1020.00,200.00,4,205.00,1,'2026-06-18','vigente'),(8,'V-38456234',180.00,500.00,1,-320.00,1,'2026-06-18','vigente'),(9,'V-7890345',450.00,10.00,1,440.00,1,'2026-06-19','vigente'),(10,'V-38456234',320.00,10.00,1,310.00,1,'0011-01-01','vigente');
/*!40000 ALTER TABLE `financiamientos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_insert_financiamiento` AFTER INSERT ON `financiamientos` FOR EACH ROW BEGIN
    INSERT INTO sistema_edward_usuario.bitacora (tabla_afectada, accion, modulo, usuario_id, id_modulo)
    VALUES ('financiamientos', 'Registrar Financiamiento', 'Administrar Financiamiento', @usuario_actual, NEW.id_financiamiento);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_update_financiamiento` AFTER UPDATE ON `financiamientos` FOR EACH ROW BEGIN
    IF NEW.estatus_financiamiento != OLD.estatus_financiamiento THEN
        INSERT INTO sistema_edward_usuario.bitacora (tabla_afectada, accion, modulo, usuario_id, id_modulo)
        VALUES ('financiamientos', CONCAT('Cambio de estado a: ', NEW.estatus_financiamiento), 'Administrar Financiamiento', @usuario_actual, NEW.id_financiamiento);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_marca` varchar(100) NOT NULL,
  PRIMARY KEY (`id_marca`),
  UNIQUE KEY `nombre_marca` (`nombre_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Apple'),(3,'Generico'),(2,'Samsung');
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `metodo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metodo_pago` (
  `id_metodopago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_metodopago` varchar(100) NOT NULL,
  `moneda` varchar(20) NOT NULL,
  `cuenta` varchar(50) DEFAULT '0',
  `estatus` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_metodopago`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `metodo_pago` WRITE;
/*!40000 ALTER TABLE `metodo_pago` DISABLE KEYS */;
INSERT INTO `metodo_pago` VALUES (1,'Punto De Venta','VES','0',1),(2,'Divisa','USD','0',1),(3,'Pago Movil','VES','0',1);
/*!40000 ALTER TABLE `metodo_pago` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `observaciones_turno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `observaciones_turno` (
  `id_observacion` int(12) NOT NULL AUTO_INCREMENT,
  `id_turno` int(12) NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id_observacion`),
  KEY `fk_turno_obs` (`id_turno`),
  CONSTRAINT `fk_turno_obs` FOREIGN KEY (`id_turno`) REFERENCES `turnos` (`id_turno`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `observaciones_turno` WRITE;
/*!40000 ALTER TABLE `observaciones_turno` DISABLE KEYS */;
INSERT INTO `observaciones_turno` VALUES (7,4,'retrazo');
/*!40000 ALTER TABLE `observaciones_turno` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ordenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ordenes` WRITE;
/*!40000 ALTER TABLE `ordenes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ordenes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `pago_online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pago_online` (
  `id_reporte` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `cedula_persona` varchar(20) NOT NULL,
  `referencia` varchar(50) NOT NULL,
  `banco_emisor` varchar(50) NOT NULL,
  `banco_receptor` varchar(50) DEFAULT NULL,
  `telefono_transferencia` varchar(20) DEFAULT '00000000000' COMMENT 'Tel├®fono desde donde se hizo la transferencia',
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
  KEY `cedula_cliente` (`cedula_persona`),
  KEY `idx_estado` (`estado_verificacion`),
  KEY `idx_referencia` (`referencia`),
  CONSTRAINT `pago_online_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `pago_online` WRITE;
/*!40000 ALTER TABLE `pago_online` DISABLE KEYS */;
INSERT INTO `pago_online` VALUES (14,28,'30000000','12312312','Banco Venezuela',NULL,'00000000000',NULL,69.00,'2026-06-12','30000000',NULL,'aprobado','2026-06-12 08:03:34','2026-07-30 22:43:27','administrador',NULL),(15,28,'30000000','12312312','Banco Venezuela',NULL,'00000000000',NULL,69.00,'2026-06-12','30000000',NULL,'aprobado','2026-06-12 08:07:24','2026-07-30 22:23:12','administrador',NULL),(16,35,'30000000','0584678906','Banco Banesco','Banesco','04161768520','Cliente Prueba',198.99,'2026-06-24','30000000',NULL,'aprobado','2026-06-23 23:40:14','2026-07-30 22:23:00','administrador',NULL),(17,36,'V-17418869','0584678905','Banco Banesco','Banesco','04126785641','San Oviedo',128.99,'2026-06-24','V-17418869',NULL,'pendiente','2026-06-24 13:18:49',NULL,NULL,NULL),(18,37,'30000000','0584678910','Banco Venezuela','Banesco','04121234560','Cliente Prueba',185.00,'2026-06-24','30000000',NULL,'aprobado','2026-06-24 16:24:27','2026-07-30 22:43:54','administrador',NULL),(19,38,'V-10456123','12562365514','Banco Banesco','Banesco','04126785645','Marielys Alessandra Yamal Herrera',19899.00,'2026-06-25','V-10456123',NULL,'aprobado','2026-06-24 22:25:10','2026-06-25 16:51:19','administrador',NULL),(20,40,'V-310005002','0584678956','Banco Banesco','Banesco','04146785645','elie herna',66.00,'2026-06-27','V-310005002',NULL,'aprobado','2026-06-27 13:16:53','2026-06-27 16:01:47','administrador',NULL),(21,41,'V-310005002','0584678980','Banco Plaza','Banesco','04126785645','elie herna',1989900.00,'2026-06-28','V-310005002',NULL,'aprobado','2026-06-28 01:23:14','2026-06-28 02:42:09','administrador',NULL),(22,42,'V-310005002','0584678980','Banco Provincial','Banesco','04126785646','elie herna',19899.00,'2026-06-28','V-310005002',NULL,'aprobado','2026-06-28 02:38:57','2026-06-28 02:42:14','administrador',NULL),(23,43,'V-310005002','0584678992','Banco Provincial','Banesco','04126785646','elie herna',189.00,'2026-06-28','V-310005002',NULL,'aprobado','2026-06-28 02:40:27','2026-06-28 05:00:14','administrador',NULL),(24,44,'V-310005002','0584678110','Banco Banesco','Banesco','04126785645','elie herna',20559736.00,'2026-06-28','V-310005002',NULL,'aprobado','2026-06-28 06:13:50','2026-07-30 22:49:59','administrador',NULL),(25,45,'V-19180654','0988776655446','Banco Banesco','Banesco','04168586758','Eva Perez',27.98,'2026-07-08','V-19180654',NULL,'aprobado','2026-07-08 03:40:03','2026-07-30 22:50:34','administrador',NULL),(26,48,'V-30155522','1212121211212','Banco Provincial','Banesco','04125445645','Moshe Chano',77.98,'2026-07-30','V-30155522',NULL,'aprobado','2026-07-30 20:51:03','2026-07-30 22:50:52','administrador',NULL),(27,49,'V-7418869','5156151515856','Banco Nacional de Cr├®dito','Banesco','04161564645','Gustavo Gimenez',26.97,'2026-08-02','V-7418869',NULL,'pendiente','2026-08-02 21:23:22',NULL,NULL,NULL),(28,50,'V-7418869','5154475147544','Banco Plaza','Banesco','04165566895','Gustavo Gimenez',17.98,'2026-08-17','V-7418869',NULL,'pendiente','2026-08-17 19:55:07',NULL,NULL,NULL),(29,53,'V-7418869','1525256486869','Banco de Venezuela','Banesco','04168586758','Gustavo Gimenez',1800.00,'2026-08-25','V-7418869',NULL,'rechazado','2026-08-24 22:26:05','2026-08-24 23:09:41','administrador','los datos no coinciden ni aparecen en la cuenta bancaria');
/*!40000 ALTER TABLE `pago_online` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_pedido_aprobado_a_venta` AFTER UPDATE ON `pago_online` FOR EACH ROW BEGIN

    -- Solo cuando el pago pasa a 'aprobado'

    IF NEW.estado_verificacion = 'aprobado' AND OLD.estado_verificacion != 'aprobado' THEN

        

        -- 1. Insertar en ventas (cabecera)

        INSERT INTO ventas (

            fecha_venta,

            origen_venta,

            total,

            cedula_persona,      -- Ô£à Cliente que compr├│

            cedula_usuario,      -- Ô£à Usuario que atendi├│ (cajera)

            id_pedido            -- Ô£à Relaci├│n con pedido

        ) VALUES (

            NOW(),

            'ecommerce',

            (SELECT total FROM pedidos WHERE id_pedido = NEW.id_pedido),

            NEW.cedula_persona,  -- Ô£à CORREGIDO: usa cedula_persona

            NEW.verificado_por,  -- Ô£à Cajera que aprob├│

            NEW.id_pedido

        );

        

        -- 2. Obtener el ID de la venta reci├®n creada

        SET @id_venta = LAST_INSERT_ID();

        

        -- 3. Insertar en detalle_ventas (productos)

        INSERT INTO detalle_ventas (

            id_ventas,

            id_producto,

            cantidad,

            precio

        )

        SELECT 

            @id_venta,

            id_producto,

            cantidad,

            precio_unitario

        FROM detalle_pedido

        WHERE id_pedido = NEW.id_pedido;

        

    END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_ventas` int(11) NOT NULL,
  `id_metodopago` int(11) NOT NULL,
  `monto_recibido` decimal(12,2) NOT NULL,
  `referencia` varchar(50) DEFAULT NULL,
  `creado_el` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pago`),
  KEY `id_ventas` (`id_ventas`),
  KEY `id_metodopago` (`id_metodopago`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES (1,13,2,371.20,NULL,'2026-06-09 01:40:19'),(2,18,2,208.80,NULL,'2026-06-09 02:41:27'),(3,19,2,200.00,NULL,'2026-06-09 02:42:13'),(4,19,3,4834.46,'02514765','2026-06-09 02:42:13'),(5,22,2,30.15,NULL,'2026-06-14 20:39:53'),(6,23,1,16000.00,NULL,'2026-06-14 20:56:48'),(7,23,2,1.02,NULL,'2026-06-14 20:56:48'),(8,24,2,30.15,NULL,'2026-06-14 18:41:34'),(9,26,2,30.15,NULL,'2026-06-15 01:04:23'),(10,29,2,20.00,NULL,'2026-06-27 16:12:55'),(11,29,2,20.00,NULL,'2026-06-27 16:12:55');
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_persona` varchar(20) NOT NULL,
  `email_invitado` varchar(100) DEFAULT NULL,
  `nombre_cliente` varchar(150) NOT NULL,
  `telefono_cliente` varchar(20) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL,
  `costo_envio` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','revision','aprobado','enviado','entregado','rechazado','cancelado') NOT NULL DEFAULT 'pendiente',
  `motivo_rechazo` text DEFAULT NULL COMMENT 'Motivo del rechazo del pago (visible para el cliente)',
  `verificado_por` varchar(20) DEFAULT NULL COMMENT 'C├®dula de la cajera que aprob├│ el pago',
  `datos_pago` text DEFAULT NULL,
  `direccion_entrega` text DEFAULT NULL,
  `id_direccion_envio` int(11) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `fk_pedidos_cliente` (`cedula_persona`),
  KEY `fk_pedidos_direccion` (`id_direccion_envio`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (28,'V-30000000','PruebaCliente@gmail.com','Cliente Prueba','00000000000','2026-06-12 08:00:44',68.98,10.00,68.98,'aprobado',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"8\",\"fecha_seleccion\":\"2026-06-12 08:00:44\"}',NULL,NULL,'pago_movil'),(32,'V-30000000','PruebaCliente@gmail.com','Cliente Prueba','00000000000','2026-06-21 17:46:12',188.99,0.00,188.99,'pendiente',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-21 23:46:12\"}',NULL,NULL,'pago_movil'),(33,'V-30000000','PruebaCliente@gmail.com','Cliente Prueba','00000000000','2026-06-23 17:07:09',197.98,0.00,197.98,'pendiente',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-23 23:07:09\"}',NULL,NULL,'pago_movil'),(34,'V-30000000','PruebaCliente@gmail.com','Cliente Prueba','00000000000','2026-06-23 17:20:21',188.99,0.00,188.99,'pendiente',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"5\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-23 23:20:21\"}',NULL,NULL,'pago_movil'),(35,'V-30000000','PruebaCliente@gmail.com','Cliente Prueba','00000000000','2026-06-23 19:02:32',188.99,0.00,188.99,'aprobado',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-24 01:02:32\"}',NULL,NULL,'pago_movil'),(36,'V-17418869','sanmen1@gmail.com','San Oviedo','04126785645','2026-06-24 09:17:53',27.98,0.00,27.98,'pendiente',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-24 15:17:53\"}',NULL,NULL,'pago_movil'),(37,'V-30000000','PruebaCliente@gmail.com','Cliente Prueba','00000000000','2026-06-24 12:04:23',215.00,0.00,215.00,'aprobado',NULL,NULL,'{\"metodo\":\"transferencia\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"delivery\",\"fecha\":\"2026-06-24 18:04:24\"}','av.la salle callejon1',NULL,'transferencia'),(38,'V-10456123','marielys@gmail.com','Marielys Alessandra Yamal Herrera','04125673456','2026-06-24 18:24:43',198.99,0.00,198.99,'enviado',NULL,'CAJERA01','{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"delivery\",\"fecha\":\"2026-06-25 00:24:43\"}','entre, y, Calle 48',NULL,'pago_movil'),(39,'V-30000001','green@gmail.com','green verde','04221511322','2026-06-26 00:19:43',198.99,0.00,198.99,'pendiente',NULL,NULL,'{\"metodo\":\"transferencia\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"delivery\",\"fecha\":\"2026-06-26 06:19:44\"}','av lamata entre, Calle 4 y 5',NULL,'transferencia'),(40,'V-310005002','elierna@gmail.com','elie herna','04141555355','2026-06-27 09:14:37',65.96,0.00,65.96,'enviado',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"delivery\",\"fecha\":\"2026-06-27 15:14:37\"}','Av la salle',NULL,'pago_movil'),(41,'V-310005002','elierna@gmail.com','elie herna','04141555355','2026-06-27 19:26:27',198.99,0.00,198.99,'enviado',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"10\",\"tipo_entrega\":\"delivery\",\"fecha\":\"2026-06-28 01:26:27\"}','vereda2 sector andres bello',NULL,'pago_movil'),(42,'V-310005002','elierna@gmail.com','elie herna','04141555355','2026-06-27 21:23:57',45.00,0.00,45.00,'enviado',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"5\",\"tipo_entrega\":\"delivery\",\"fecha\":\"2026-06-28 03:23:57\"}','entre, y, Calle 48',NULL,'pago_movil'),(43,'V-310005002','elierna@gmail.com','elie herna','04141555355','2026-06-27 22:39:45',180.00,0.00,180.00,'aprobado',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"7\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-28 04:39:45\"}',NULL,NULL,'pago_movil'),(44,'V-310005002','elierna@gmail.com','elie herna','04141555355','2026-06-28 00:55:54',330.00,0.00,330.00,'aprobado',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-06-28 06:55:57\"}',NULL,NULL,'pago_movil'),(45,'V-19180654','evanpen1@gmail.com','Eva Perez','04168545445','2026-07-07 22:17:21',27.98,0.00,27.98,'aprobado',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-07-08 04:17:22\"}',NULL,NULL,'pago_movil'),(46,'V-19180654','evanpen1@gmail.com','Eva Perez','04168545445','2026-07-09 12:16:50',37.98,0.00,37.98,'pendiente',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"delivery\",\"fecha\":\"2026-07-09 18:16:50\"}','av los horcones',NULL,'pago_movil'),(47,'V-7418869','gustmen1@gmail.com','Gustavo Gimenez','04161768520','2026-07-16 09:44:03',27.98,0.00,27.98,'pendiente',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":\"6\",\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-07-16 15:44:03\"}',NULL,NULL,'pago_movil'),(48,'V-30155522','CHANO2225@GMAIL.COM','Moshe Chano','04123356569','2026-07-30 14:18:20',77.98,0.00,77.98,'aprobado',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":null,\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-07-30 20:18:20\"}',NULL,NULL,'pago_movil'),(49,'V-7418869','gustmen1@gmail.com','Gustavo Gimenez','04161768520','2026-08-02 17:22:14',26.97,0.00,26.97,'pendiente',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":null,\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-08-02 23:22:14\"}',NULL,NULL,'pago_movil'),(50,'V-7418869','gustmen1@gmail.com','Gustavo Gimenez','04161768520','2026-08-17 15:50:46',17.98,0.00,17.98,'pendiente',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":null,\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-08-17 21:50:46\"}',NULL,NULL,'pago_movil'),(51,'V-7418869','gustmen1@gmail.com','Gustavo Gimenez','04161768520','2026-08-24 18:21:03',27.98,0.00,27.98,'pendiente',NULL,NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":null,\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-08-25 00:21:04\"}',NULL,NULL,'pago_movil'),(52,'V-7418869','gustmen1@gmail.com','Gustavo Gimenez','04161768520','2026-08-24 18:23:18',2520.00,0.00,2520.00,'pendiente',NULL,NULL,'{\"metodo\":\"transferencia\",\"banco_emisor\":null,\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-08-25 00:23:18\"}',NULL,NULL,'transferencia'),(53,'V-7418869','gustmen1@gmail.com','Gustavo Gimenez','04161768520','2026-08-24 18:25:06',1800.00,0.00,1800.00,'rechazado','los datos no coinciden ni aparecen en la cuenta bancaria',NULL,'{\"metodo\":\"pago_movil\",\"banco_emisor\":null,\"tipo_entrega\":\"retiro\",\"fecha\":\"2026-08-25 00:25:06\"}',NULL,NULL,'pago_movil');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `perfiles_financiamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perfiles_financiamiento` (
  `id_perfil` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_persona` varchar(20) NOT NULL,
  `tipo_residencia` enum('Propia','Familiar','Alquilada') DEFAULT 'Familiar',
  `carga_familiar` int(11) DEFAULT 0,
  `estado_civil` enum('Soltero','Casado','Divorciado','Viudo','En relaci├│n') DEFAULT 'Soltero',
  `profesion` enum('Empleado','Independiente','Estudiante (Becado)','Estudiante','Pensionado','Desempleado') DEFAULT 'Empleado',
  `ocupacion` varchar(100) DEFAULT NULL,
  `ingresos_mensuales` decimal(10,2) DEFAULT 0.00,
  `score_credito` int(11) DEFAULT 5,
  PRIMARY KEY (`id_perfil`),
  KEY `fk_perfil_persona` (`cedula_persona`),
  CONSTRAINT `fk_perfil_persona` FOREIGN KEY (`cedula_persona`) REFERENCES `persona` (`cedula_persona`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `perfiles_financiamiento` WRITE;
/*!40000 ALTER TABLE `perfiles_financiamiento` DISABLE KEYS */;
INSERT INTO `perfiles_financiamiento` VALUES (3,'V-3242343','Propia',0,'Soltero','Empleado','cajera',150.52,5),(4,'V-34567567','Propia',0,'Soltero','Empleado','Cajero',650.46,8),(5,'V-10456123','Propia',0,'Soltero','Empleado','Cajera',154.46,5),(6,'V-7890345','Propia',0,'Soltero','Empleado','Gerente',171.62,5),(7,'V-38456234','Propia',0,'Soltero','Empleado','Gerente',136.19,5);
/*!40000 ALTER TABLE `perfiles_financiamiento` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `persona` WRITE;
/*!40000 ALTER TABLE `persona` DISABLE KEYS */;
INSERT INTO `persona` VALUES ('V-10222260','Jose','Cueri','JOSECUERI1@gmail.com','04165658978','1956-02-15','Masculino','LA MATA CABUDARE'),('V-10456123','Marielys Alessandra','Yamal Herrera','marielys@gmail.com','04125673456','2001-06-13','Femenino','Barquisimeto cerca de la catedral'),('V-14567234','Maria eugenia','Aguilar garcia','mariale@gmail.com','04163456789','2008-02-13','F','cubiro lomas arriba'),('V-19180654','Eva','Perez','evanpen1@gmail.com','04168545445','2001-12-12','Femenino','cabudate centro'),('V-22222227','Gabriela','Torre','angelito@gmail.com','04122323232','0001-01-01','','gvgvghhgghghjghjghj'),('V-30000000','Cliente','Prueba','PruebaCliente@gmail.com','00000000000',NULL,'No especificado','No especificado'),('V-30000001','green','verde','green@gmail.com','04221511322','1983-05-15','Masculino','av la mata , cabudare'),('V-30155522','Moshe','Chano','CHANO2225@GMAIL.COM','04123356569','2008-01-15','Masculino','entre, y, Calle 48'),('V-310005002','elie','herna','elierna@gmail.com','04141555355','2009-02-02','Femenino','andres bello vereda 2'),('V-3242343','pedro josue','garcia torre','garpedro@gmail.com','04240001020','2006-02-08','Masculino','Centro.cubiro'),('V-34567567','Gabriel Noriega','Marquez','Gabriel_2026@gmail.com','04268004070','2003-02-12','Masculino','Zona norte carorita km 10'),('V-38456234','Ederson Alejandro','Gonzalez Turan','Ederson@gmail.com','04124674023','2004-01-05','Masculino','Zona centro calle 15 con 14 y 16'),('V-4562434','Gabriela alejandra','Garcia Silva','gabriela_priv@gmail.com','04121005540','2026-06-04','F','Barquisimeto urb don jesus'),('V-56789456','Juan josue','Mingueza palomo','josecarrillogc@gmail.com','04164007890','2005-02-11','M','Zona norte duaca intercomunal 14'),('V-7418869','Gustavo','Gimenez','gustmen1@gmail.com','04161768520','1970-01-04','Masculino','av la salle'),('V-7890345','Gerardo Fernando','Hernandez Monsalve','gerafer@gmail.com','04244562367','1995-06-14','Masculino','urbanizacion roca del este');
/*!40000 ALTER TABLE `persona` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (9,'Iphone 17',NULL,'usado sin rayones','iphone17.jpg',26,1,0,1,1,1020.00,1),(10,'Xiaomi Redmi Note 13',NULL,'Smartphone 8GB RAM, 128GB','xiaomi_redmi13.jpg',26,1,0,0,1,320.00,1),(11,'Samsung Galaxy A54',NULL,'256GB, pantalla 6.4','samsung_a54.jpg',26,1,0,0,1,450.00,1),(12,'AirPods Pro 2',NULL,'Aud├¡fonos inal├ímbricos','airpods_pro2.jpg',24,1,0,0,15,180.00,1),(13,'Cargador R├ípido 33W',NULL,'USB-C carga r├ípida','cargador_rapido_33w.jpg',24,1,0,0,50,25.00,1),(14,'Funda Silicona iPhone 17',NULL,'Funda protectora','funda_silicona_iphone_17.jpg',24,1,0,0,29,15.00,1),(15,'Cargador 20W',NULL,'Cargador r├ípido USB-C 20W','cargador_20w.jpg',24,1,0,0,50,18.99,1),(16,'Cable USB-C',NULL,'Cable USB-C a USB-C 1m','cable_usbc.jpg',24,1,0,0,100,8.99,1),(17,'Enchufe Inteligente',NULL,'Enchufe WiFi compatible Alexa','enchufe_inteligente.jpg',22,1,0,0,29,25.99,1),(18,'Kit de Limpieza',NULL,'Kit completo para celulares','kit_limpieza.jpg',24,1,0,0,40,12.99,1),(19,'L├ímpara LED',NULL,'L├ímpara inteligente RGB','lampara_led.jpg',22,1,0,0,25,28.99,1),(20,'Parlante Bluetooth',NULL,'Parlante port├ítil 10W','parlante_bt.jpg',22,1,0,0,20,45.99,1),(21,'Soporte Auto',NULL,'Soporte magn├®tico para auto','soporte_auto.jpg',24,1,0,0,60,15.99,1),(22,'Raton',NULL,'Raton nuevo','raton.jpg',24,2,2,10,3,10.00,1),(23,'Iphone xs max',NULL,'86% bateria','iphone_xs_max.jpg',26,1,0,1,1,180.00,1),(25,'Cargador de iphone',NULL,'carga rapida','cargador_de_iphone.jpg',24,1,5,10,1,10.00,1),(27,'Pantalla oled iphone',NULL,'Pantalla nueva','pantalla_oled_iphone.jpg',27,3,2,10,5,20.00,1),(28,'Iphone 17 pro',NULL,'memoria 500gb','iphone_17_pro.jpg',26,1,0,1,1,0.00,1),(29,'Discossd',NULL,'Disco ssd 250 gb para laptop y cpu','discossd.jpg',24,2,5,15,5,35.00,1),(30,'Discossd Grande',NULL,'Disco ssd 500 gb para laptop y cpu','discossd_grande.jpg',24,2,5,15,5,45.00,1);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_insert_productos` AFTER INSERT ON `productos` FOR EACH ROW BEGIN

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_update_productos` AFTER UPDATE ON `productos` FOR EACH ROW BEGIN
    INSERT INTO sistema_edward_usuario.bitacora (tabla_afectada, accion, modulo, usuario_id, id_modulo)
    VALUES ('productos', 'Modificar', 'Administrar Productos', @usuario_actual, OLD.id_producto);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_delete_productos` AFTER DELETE ON `productos` FOR EACH ROW BEGIN
    INSERT INTO sistema_edward_usuario.bitacora (tabla_afectada, accion, modulo, usuario_id, id_modulo)
    VALUES ('productos', 'Eliminar', 'Administrar Productos', @usuario_actual, OLD.id_producto);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedores` (
  `rif_proveedor` varchar(30) NOT NULL,
  `nombre_proveedor` varchar(50) NOT NULL,
  `telefono_proveedor` varchar(15) NOT NULL,
  `correo_proveedor` varchar(50) NOT NULL,
  `ubicacion_proveedor` varchar(100) NOT NULL,
  PRIMARY KEY (`rif_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES ('G-27272727','Francheska Monsalve','04145746099','francheskamonsalvemedina@gmail.com','La Segoviana'),('J-8777999','Yorlek Medina','041451135899','yorlekmedina@gmail.com','La Floresta');
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `reservas_stock_temporal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservas_stock_temporal` (
  `id_reserva` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `token_sesion` varchar(255) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  PRIMARY KEY (`id_reserva`),
  KEY `id_producto` (`id_producto`),
  KEY `fecha_expiracion` (`fecha_expiracion`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `reservas_stock_temporal` WRITE;
/*!40000 ALTER TABLE `reservas_stock_temporal` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservas_stock_temporal` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `servicio_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servicio_productos` (
  `id_servicio_producto` int(11) NOT NULL AUTO_INCREMENT,
  `id_servicio` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_servicio_producto`),
  KEY `fk_sp_servicio` (`id_servicio`),
  KEY `fk_sp_producto` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `servicio_productos` WRITE;
/*!40000 ALTER TABLE `servicio_productos` DISABLE KEYS */;
INSERT INTO `servicio_productos` VALUES (42,21,32,1,5.00);
/*!40000 ALTER TABLE `servicio_productos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `servicio_tecnico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servicio_tecnico` (
  `id_servicio` int(11) NOT NULL AUTO_INCREMENT,
  `cedula_persona` varchar(20) NOT NULL,
  `equipo_descripcion` text NOT NULL,
  `falla_inicial` text NOT NULL,
  `estado` enum('Pendiente','Reparado','Entregado','Cobrado') DEFAULT NULL,
  `monto_total` decimal(10,2) DEFAULT 0.00,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_servicio`),
  KEY `fk_servicio_cliente` (`cedula_persona`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `servicio_tecnico` WRITE;
/*!40000 ALTER TABLE `servicio_tecnico` DISABLE KEYS */;
INSERT INTO `servicio_tecnico` VALUES (21,'V-10456123','iphone 14','cambio de pin','Pendiente',5.00,'2026-07-09 08:43:39'),(22,'V-30345234','iphone 17 pro max','no carga','Pendiente',440.00,'2026-07-09 17:00:13');
/*!40000 ALTER TABLE `servicio_tecnico` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `servicio_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `servicio_venta` WRITE;
/*!40000 ALTER TABLE `servicio_venta` DISABLE KEYS */;
/*!40000 ALTER TABLE `servicio_venta` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `tasa_cambio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasa_cambio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tasa` decimal(12,2) NOT NULL DEFAULT 60.00,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fuente` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `tasa_cambio` WRITE;
/*!40000 ALTER TABLE `tasa_cambio` DISABLE KEYS */;
INSERT INTO `tasa_cambio` VALUES (1,785.07,'2026-08-25 00:21:10','autoscrape');
/*!40000 ALTER TABLE `tasa_cambio` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `turno_empleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `turno_empleado` WRITE;
/*!40000 ALTER TABLE `turno_empleado` DISABLE KEYS */;
INSERT INTO `turno_empleado` VALUES (7,4,'V-4562434');
/*!40000 ALTER TABLE `turno_empleado` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `turnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `turnos` (
  `id_turno` int(12) NOT NULL AUTO_INCREMENT,
  `fecha_turno` date NOT NULL,
  `hora_entrada` time NOT NULL,
  `hora_salida` time NOT NULL,
  PRIMARY KEY (`id_turno`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `turnos` WRITE;
/*!40000 ALTER TABLE `turnos` DISABLE KEYS */;
INSERT INTO `turnos` VALUES (4,'2026-06-12','10:54:00','05:54:00');
/*!40000 ALTER TABLE `turnos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `unidades_telefonos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `unidades_telefonos` WRITE;
/*!40000 ALTER TABLE `unidades_telefonos` DISABLE KEYS */;
INSERT INTO `unidades_telefonos` VALUES (19,9,'351111111111111','8GB','128GB','Financiado','2026-06-03 18:21:19'),(20,10,'352222222222222','8GB','128GB','Financiado','2026-06-03 18:21:19'),(21,11,'353333333333333','8GB','256GB','Financiado','2026-06-03 18:21:19'),(22,23,'13562823423423','8gb','128gb','Financiado','2026-06-12 04:02:25'),(24,28,'876775765768697798095555','8gb','512gb','Disponible','2026-07-12 17:07:55');
/*!40000 ALTER TABLE `unidades_telefonos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ventas` (
  `id_ventas` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_venta` timestamp NOT NULL DEFAULT current_timestamp(),
  `origen_venta` enum('presencial','ecommerce') NOT NULL DEFAULT 'presencial',
  `total` decimal(12,2) NOT NULL,
  `cedula_persona` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cedula_usuario` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_pedido` int(11) DEFAULT NULL COMMENT 'ID del pedido que gener├│ esta venta',
  PRIMARY KEY (`id_ventas`),
  KEY `cedula_usuario` (`cedula_usuario`),
  KEY `cedula_persona` (`cedula_persona`),
  KEY `idx_ventas_fecha` (`fecha_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (13,'2026-06-09 01:40:19','presencial',203926.14,'V-40678456','administrador',NULL),(18,'2026-06-09 02:41:27','presencial',114708.46,'V-40678456','administrador',NULL),(19,'2026-06-09 02:42:13','presencial',114708.46,'V-40678456','administrador',NULL),(22,'2026-06-14 20:39:53','presencial',30.15,NULL,'administrador',NULL),(23,'2026-06-14 20:56:48','presencial',30.15,NULL,'administrador',NULL),(24,'2026-06-14 18:41:34','presencial',30.15,'30000000','administrador',NULL),(26,'2026-06-15 01:04:23','presencial',30.15,NULL,'V-56789456',NULL),(27,'2026-06-25 21:21:56','ecommerce',198.99,'V-10456123','CAJERA01',38),(28,'2026-06-27 16:01:47','ecommerce',65.96,'V-310005002','administrador',40),(29,'2026-06-27 16:12:54','presencial',17.40,NULL,'administrador',NULL),(30,'2026-06-28 02:42:09','ecommerce',198.99,'V-310005002','administrador',41),(31,'2026-06-28 02:42:14','ecommerce',45.00,'V-310005002','administrador',42),(32,'2026-06-28 05:00:14','ecommerce',180.00,'V-310005002','administrador',43),(33,'2026-07-30 16:27:58','ecommerce',68.98,'30000000','administrador',27),(34,'2026-07-30 16:40:41','ecommerce',68.98,'30000000','administrador',27),(35,'2026-07-30 16:42:08','ecommerce',68.98,'30000000','administrador',27),(36,'2026-07-30 17:30:46','ecommerce',68.98,'30000000','administrador',27),(37,'2026-07-30 17:30:59','ecommerce',68.98,'30000000','administrador',27),(38,'2026-07-30 22:03:17','ecommerce',68.98,'30000000','administrador',27),(39,'2026-07-30 22:03:29','ecommerce',68.98,'30000000','administrador',27),(40,'2026-07-30 22:22:21','ecommerce',68.98,'30000000','administrador',27),(41,'2026-07-30 22:22:33','ecommerce',68.98,'30000000','administrador',27),(42,'2026-07-30 22:22:49','ecommerce',68.98,'30000000','administrador',27),(43,'2026-07-30 22:23:00','ecommerce',188.99,'30000000','administrador',35),(44,'2026-07-30 22:23:12','ecommerce',68.98,'30000000','administrador',28),(45,'2026-07-30 22:42:48','ecommerce',68.98,'30000000','administrador',28),(46,'2026-07-30 22:43:54','ecommerce',215.00,'30000000','administrador',37),(47,'2026-07-30 22:49:59','ecommerce',330.00,'V-310005002','administrador',44),(48,'2026-07-30 22:50:34','ecommerce',27.98,'V-19180654','administrador',45),(49,'2026-07-30 22:50:52','ecommerce',77.98,'V-30155522','administrador',48);
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

