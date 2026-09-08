-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sistema_edward_usuario
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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

--
-- Table structure for table `acciones`
--

DROP TABLE IF EXISTS `acciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acciones` (
  `id_accion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_accion` varchar(50) NOT NULL,
  PRIMARY KEY (`id_accion`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acciones`
--

LOCK TABLES `acciones` WRITE;
/*!40000 ALTER TABLE `acciones` DISABLE KEYS */;
INSERT INTO `acciones` VALUES (1,'registrar'),(2,'consultar'),(3,'modificar'),(4,'eliminar'),(5,'listar'),(6,'control_total'),(7,'aprobar_pago'),(8,'rechazar_pago');
/*!40000 ALTER TABLE `acciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitacora`
--

DROP TABLE IF EXISTS `bitacora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bitacora` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tabla_afectada` varchar(50) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` varchar(20) NOT NULL,
  `modulo` varchar(100) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora`
--

LOCK TABLES `bitacora` WRITE;
/*!40000 ALTER TABLE `bitacora` DISABLE KEYS */;
INSERT INTO `bitacora` VALUES (4,'','Modificar Marca','2026-05-29 04:14:39','V-30753799','Administrar Marcas',0),(5,'productos','Registrar','2026-05-29 04:26:14','V-30753799','Administrar Productos',5),(6,'financiamientos','Registrar Financiamiento','2026-05-29 04:45:38','V-30753799','Administrar Financiamiento',12),(7,'cuotas','Registrar Pago de Cuota','2026-05-29 04:45:53','V-30753799','Administrar Financiamiento',40),(8,'financiamientos','Registrar Financiamiento','2026-05-29 05:02:06','V-30753799','Administrar Financiamiento',13),(9,'cuotas','Registrar Pago de Cuota','2026-05-29 06:04:33','V-30753799','Administrar Financiamiento',45),(10,'financiamientos','Cambio de estado a: finalizado','2026-05-29 06:04:42','V-30753799','Administrar Financiamiento',13),(11,'cuotas','Registrar Pago de Cuota','2026-05-29 06:06:47','V-30753799','Administrar Financiamiento',46),(12,'financiamientos','Registrar Financiamiento','2026-05-29 07:32:03','V-30753799','Administrar Financiamiento',14),(13,'productos','Eliminar','2026-06-03 18:13:14','administrador','Administrar Productos',1),(14,'productos','Eliminar','2026-06-03 18:13:24','administrador','Administrar Productos',3),(15,'productos','Eliminar','2026-06-03 18:13:28','administrador','Administrar Productos',2),(16,'productos','Eliminar','2026-06-03 18:13:33','administrador','Administrar Productos',5),(17,'financiamientos','Registrar Financiamiento','2026-06-05 03:51:36','administrador','Administrar Financiamiento',15),(18,'financiamientos','Registrar Financiamiento','2026-06-05 04:08:35','administrador','Administrar Financiamiento',16),(19,'financiamientos','Registrar Financiamiento','2026-06-05 04:39:13','administrador','Administrar Financiamiento',17),(20,'cuotas','Registrar Pago de Cuota','2026-06-05 04:39:39','administrador','Administrar Financiamiento',67),(21,'financiamientos','Cambio de estado a: finalizado','2026-06-05 04:40:02','administrador','Administrar Financiamiento',17),(22,'financiamientos','Registrar Financiamiento','2026-06-05 04:40:28','administrador','Administrar Financiamiento',18),(23,'financiamientos','Cambio de estado a: anulado','2026-06-05 04:45:55','administrador','Administrar Financiamiento',18),(24,'financiamientos','Registrar Financiamiento','2026-06-05 04:55:54','administrador','Administrar Financiamiento',19),(25,'financiamientos','Registrar Financiamiento','2026-06-05 05:03:30','V-30753799','Administrar Financiamiento',20),(26,'cuotas','Registrar Pago de Cuota','2026-06-05 05:03:42','V-30753799','Administrar Financiamiento',79),(27,'financiamientos','Cambio de estado a: finalizado','2026-06-05 05:04:48','V-30753799','Administrar Financiamiento',20),(28,'marcas','Registrar','2026-06-05 05:33:50','V-30753799','Administrar Marcas',5),(29,'marcas','Eliminar','2026-06-05 05:33:59','V-30753799','Administrar Marcas',5),(30,'marcas','Eliminar','2026-06-05 05:34:05','V-30753799','Administrar Marcas',4),(31,'productos','Modificar','2026-06-08 17:26:48','administrador','Administrar Productos',12),(32,'productos','Modificar','2026-06-08 17:26:48','administrador','Administrar Productos',20),(33,'productos','Modificar','2026-06-08 17:26:49','administrador','Administrar Productos',21),(34,'productos','Modificar','2026-06-08 17:38:12','administrador','Administrar Productos',22),(35,'productos','Modificar','2026-06-08 17:38:12','administrador','Administrar Productos',20),(36,'productos','Modificar','2026-06-08 17:38:12','administrador','Administrar Productos',23),(37,'productos','Modificar','2026-06-08 17:38:13','administrador','Administrar Productos',13),(38,'productos','Modificar','2026-06-08 22:23:51','administrador','Administrar Productos',22),(39,'productos','Modificar','2026-06-08 22:23:51','administrador','Administrar Productos',20),(40,'productos','Modificar','2026-06-08 22:23:51','administrador','Administrar Productos',23),(41,'productos','Modificar','2026-06-08 22:23:51','administrador','Administrar Productos',13),(42,'productos','Modificar','2026-06-08 22:24:44','administrador','Administrar Productos',12),(43,'productos','Modificar','2026-06-08 22:24:44','administrador','Administrar Productos',20),(44,'productos','Modificar','2026-06-08 22:24:44','administrador','Administrar Productos',21),(45,'productos','Modificar','2026-06-08 22:27:14','administrador','Administrar Productos',16),(46,'productos','Modificar','2026-06-08 22:27:14','administrador','Administrar Productos',23),(47,'productos','Modificar','2026-06-08 22:27:15','administrador','Administrar Productos',12),(48,'productos','Modificar','2026-06-08 22:30:59','administrador','Administrar Productos',13),(49,'productos','Modificar','2026-06-08 22:31:31','administrador','Administrar Productos',20),(50,'productos','Modificar','2026-06-08 22:33:52','administrador','Administrar Productos',13),(51,'productos','Modificar','2026-06-08 22:39:21','administrador','Administrar Productos',20),(52,'productos','Modificar','2026-06-08 22:39:40','administrador','Administrar Productos',16),(53,'productos','Modificar','2026-06-08 22:39:40','administrador','Administrar Productos',23),(54,'productos','Modificar','2026-06-08 22:39:40','administrador','Administrar Productos',12),(55,'productos','Modificar','2026-06-08 22:39:59','administrador','Administrar Productos',12),(56,'productos','Modificar','2026-06-08 22:40:20','administrador','Administrar Productos',12),(57,'productos','Modificar','2026-06-09 00:23:34','administrador','Administrar Productos',12),(58,'productos','Modificar','2026-06-09 00:26:19','administrador','Administrar Productos',15),(59,'productos','Modificar','2026-06-09 00:28:59','administrador','Administrar Productos',12),(60,'productos','Modificar','2026-06-09 00:30:17','administrador','Administrar Productos',12),(61,'productos','Modificar','2026-06-09 00:31:02','administrador','Administrar Productos',14),(62,'productos','Modificar','2026-06-09 00:31:34','administrador','Administrar Productos',16),(63,'productos','Modificar','2026-06-09 00:40:14','administrador','Administrar Productos',23),(64,'productos','Modificar','2026-06-09 00:43:04','administrador','Administrar Productos',23),(65,'productos','Modificar','2026-06-09 00:43:33','administrador','Administrar Productos',23),(66,'productos','Modificar','2026-06-09 00:47:41','administrador','Administrar Productos',23),(67,'productos','Modificar','2026-06-09 00:47:49','administrador','Administrar Productos',12),(68,'productos','Modificar','2026-06-09 00:48:01','administrador','Administrar Productos',12),(69,'productos','Modificar','2026-06-09 00:48:08','administrador','Administrar Productos',15),(70,'productos','Modificar','2026-06-09 00:48:41','administrador','Administrar Productos',12),(71,'productos','Modificar','2026-06-09 00:49:00','administrador','Administrar Productos',18),(72,'productos','Modificar','2026-06-09 00:49:16','administrador','Administrar Productos',17),(73,'productos','Modificar','2026-06-09 00:49:43','administrador','Administrar Productos',15),(74,'productos','Modificar','2026-06-09 00:50:03','administrador','Administrar Productos',13),(75,'productos','Modificar','2026-06-09 00:50:34','administrador','Administrar Productos',21),(76,'productos','Modificar','2026-06-09 00:51:56','administrador','Administrar Productos',12),(77,'productos','Modificar','2026-06-09 00:52:21','administrador','Administrar Productos',23),(78,'productos','Modificar','2026-06-09 00:52:43','administrador','Administrar Productos',16),(79,'productos','Modificar','2026-06-09 00:57:40','administrador','Administrar Productos',18),(80,'productos','Modificar','2026-06-09 00:57:50','administrador','Administrar Productos',18),(81,'productos','Modificar','2026-06-09 00:58:12','administrador','Administrar Productos',16),(82,'productos','Modificar','2026-06-09 02:20:52','administrador','Administrar Productos',13),(83,'productos','Modificar','2026-06-09 02:21:30','administrador','Administrar Productos',22),(84,'productos','Modificar','2026-06-09 02:34:15','administrador','Administrar Productos',17),(85,'productos','Modificar','2026-06-09 02:35:48','administrador','Administrar Productos',17),(86,'productos','Modificar','2026-06-09 02:36:28','administrador','Administrar Productos',12),(87,'productos','Modificar','2026-06-09 04:22:55','administrador','Administrar Productos',12),(88,'productos','Modificar','2026-06-09 04:23:12','administrador','Administrar Productos',23),(89,'productos','Modificar','2026-06-09 04:23:22','administrador','Administrar Productos',16),(90,'productos','Modificar','2026-06-09 05:14:13','administrador','Administrar Productos',22),(91,'productos','Modificar','2026-06-09 05:14:26','administrador','Administrar Productos',13),(92,'productos','Modificar','2026-06-09 05:14:40','administrador','Administrar Productos',17),(93,'productos','Modificar','2026-06-09 05:14:52','administrador','Administrar Productos',12),(94,'productos','Modificar','2026-06-09 05:15:54','administrador','Administrar Productos',21),(95,'productos','Modificar','2026-06-09 05:15:55','administrador','Administrar Productos',18),(96,'productos','Modificar','2026-06-09 05:15:55','administrador','Administrar Productos',22),(97,'productos','Modificar','2026-06-09 05:17:24','administrador','Administrar Productos',22),(98,'productos','Modificar','2026-06-09 05:18:07','administrador','Administrar Productos',21),(99,'productos','Modificar','2026-06-09 05:18:13','administrador','Administrar Productos',18),(100,'productos','Modificar','2026-06-09 05:18:13','administrador','Administrar Productos',22),(101,'productos','Modificar','2026-06-09 05:19:02','administrador','Administrar Productos',16),(102,'financiamientos','Registrar Financiamiento','2026-06-09 05:27:52','administrador','Administrar Financiamiento',21),(103,'cuotas','Registrar Pago de Cuota','2026-06-09 05:29:26','administrador','Administrar Financiamiento',83),(104,'financiamientos','Cambio de estado a: finalizado','2026-06-09 05:29:42','administrador','Administrar Financiamiento',21),(105,'productos','Modificar','2026-06-09 05:37:45','administrador','Administrar Productos',12),(106,'productos','Modificar','2026-06-09 05:37:46','administrador','Administrar Productos',22),(107,'productos','Modificar','2026-06-09 05:38:05','administrador','Administrar Productos',22),(108,'productos','Modificar','2026-06-09 05:38:20','administrador','Administrar Productos',16),(109,'productos','Modificar','2026-06-21 03:22:31','root','Administrar Productos',13),(110,'productos','Modificar','2026-06-21 03:22:31','root','Administrar Productos',14),(111,'productos','Modificar','2026-06-21 03:22:32','root','Administrar Productos',22),(112,'productos','Modificar','2026-06-21 03:22:32','root','Administrar Productos',23),(113,'productos','Modificar','2026-06-21 03:22:32','root','Administrar Productos',25),(114,'productos','Modificar','2026-06-21 03:22:32','root','Administrar Productos',27),(115,'cajera_panel','ACCESO','2026-06-25 05:17:36','administrador','Ventas Online',0),(116,'cajera_panel','ACCESO','2026-06-25 05:50:53','administrador','Ventas Online',0),(117,'cajera_panel','ACCESO','2026-06-25 05:50:56','administrador','Ventas Online',0),(118,'cajera_panel','ACCESO','2026-06-25 05:51:26','administrador','Ventas Online',0),(119,'cajera_panel','ACCESO','2026-06-25 05:54:03','administrador','Ventas Online',0),(120,'cajera_panel','ACCESO','2026-06-25 05:54:04','administrador','Ventas Online',0),(121,'cajera_panel','ACCESO','2026-06-25 06:27:38','administrador','Ventas Online',0),(122,'cajera_panel','ACCESO','2026-06-25 06:27:41','administrador','Ventas Online',0),(123,'cajera_panel','ACCESO','2026-06-25 16:33:58','administrador','Ventas Online',0),(124,'cajera_panel','ACCESO','2026-06-25 16:51:05','administrador','Ventas Online',0),(125,'pago_online','APROBAR_PAGO','2026-06-25 16:51:19','administrador','Ventas Online',19),(126,'cajera_panel','ACCESO','2026-06-26 01:02:31','administrador','Ventas Online',0),(127,'cajera_panel','ACCESO','2026-06-26 01:57:13','administrador','Ventas Online',0),(128,'cajera_panel','ACCESO','2026-06-26 01:57:16','administrador','Ventas Online',0),(129,'cajera_panel','ACCESO','2026-06-26 02:18:35','administrador','Ventas Online',0),(130,'cajera_panel','ACCESO','2026-06-26 03:27:52','administrador','Ventas Online',0),(131,'despachos','ASIGNAR_DESPACHO','2026-06-26 03:28:40','administrador','Ventas Online',38),(132,'cajera_panel','ACCESO','2026-06-27 03:09:06','administrador','Ventas Online',0),(133,'cajera_panel','ACCESO','2026-06-27 16:01:22','administrador','Ventas Online',0),(134,'pago_online','APROBAR_PAGO','2026-06-27 16:01:47','administrador','Ventas Online',20),(135,'cajera_panel','ACCESO','2026-06-27 16:02:21','administrador','Ventas Online',0),(136,'productos','Modificar','2026-06-27 16:12:54','administrador','Administrar Productos',14),(137,'cajera_panel','ACCESO','2026-06-27 16:13:13','administrador','Ventas Online',0),(138,'cajera_panel','ACCESO','2026-06-28 02:42:00','administrador','Ventas Online',0),(139,'pago_online','APROBAR_PAGO','2026-06-28 02:42:09','administrador','Ventas Online',21),(140,'pago_online','APROBAR_PAGO','2026-06-28 02:42:14','administrador','Ventas Online',22),(141,'cajera_panel','ACCESO','2026-06-28 02:50:45','administrador','Ventas Online',0),(142,'despachos','ASIGNAR_DESPACHO','2026-06-28 02:51:51','administrador','Ventas Online',40),(143,'cajera_panel','ACCESO','2026-06-28 03:03:11','administrador','Ventas Online',0),(144,'cajera_panel','ACCESO','2026-06-28 04:58:09','administrador','Ventas Online',0),(145,'pago_online','APROBAR_PAGO','2026-06-28 05:00:14','administrador','Ventas Online',23),(146,'cajera_panel','ACCESO','2026-06-28 05:27:04','administrador','Ventas Online',0),(147,'cajera_panel','ACCESO','2026-06-28 06:14:30','administrador','Ventas Online',0),(148,'cajera_panel','ACCESO','2026-06-28 06:44:05','administrador','Ventas Online',0),(149,'cajera_panel','ACCESO','2026-06-28 18:35:39','administrador','Ventas Online',0),(150,'cajera_panel','ACCESO','2026-06-28 18:38:46','administrador','Ventas Online',0),(151,'cajera_panel','ACCESO','2026-06-28 19:26:04','administrador','Ventas Online',0),(152,'cajera_panel','ACCESO','2026-06-28 23:33:27','administrador','Ventas Online',0),(153,'cajera_panel','ACCESO','2026-06-28 23:41:09','administrador','Ventas Online',0),(154,'cajera_panel','ACCESO','2026-06-28 23:51:33','administrador','Ventas Online',0),(155,'cajera_panel','ACCESO','2026-06-29 00:00:54','administrador','Ventas Online',0),(156,'cajera_panel','ACCESO','2026-06-29 07:46:59','administrador','Ventas Online',0),(157,'cajera_panel','ACCESO','2026-06-29 11:08:03','administrador','Ventas Online',0),(158,'cajera_panel','ACCESO','2026-06-29 11:09:58','administrador','Ventas Online',0),(159,'cajera_panel','ACCESO','2026-06-29 11:10:20','administrador','Ventas Online',0),(160,'cajera_panel','ACCESO','2026-06-29 11:10:55','administrador','Ventas Online',0),(161,'cajera_panel','ACCESO','2026-06-29 11:11:24','administrador','Ventas Online',0),(162,'cajera_panel','ACCESO','2026-06-29 11:17:58','administrador','Ventas Online',0),(163,'cajera_panel','ACCESO','2026-06-29 11:22:51','administrador','Ventas Online',0),(164,'productos','Modificar','2026-07-27 20:43:56','root','Administrar Productos',28),(165,'productos','Modificar','2026-07-27 20:43:56','root','Administrar Productos',29),(166,'productos','Modificar','2026-07-27 20:43:56','root','Administrar Productos',30),(167,'cajera_panel','ACCESO','2026-08-24 23:09:00','administrador','Ventas Online',0),(168,'pago_online','RECHAZAR_PAGO','2026-08-24 23:09:43','administrador','Ventas Online',29),(169,'cajera_panel','ACCESO','2026-08-24 23:09:48','administrador','Ventas Online',0);
/*!40000 ALTER TABLE `bitacora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitacora_detalles`
--

DROP TABLE IF EXISTS `bitacora_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bitacora_detalles` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_bitacora` int(11) NOT NULL,
  `campo_afectado` varchar(50) DEFAULT NULL,
  `valor_antiguo` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_bitacora` (`id_bitacora`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora_detalles`
--

LOCK TABLES `bitacora_detalles` WRITE;
/*!40000 ALTER TABLE `bitacora_detalles` DISABLE KEYS */;
INSERT INTO `bitacora_detalles` VALUES (11,18,'Detalle Completo Registro','N/A','Monto Total: 180.00; Pago Inicial: 50.00; Cuotas: 2; Monto Cuota: 65.00; D├¡a Pago: 1; Fecha Inicio: 2026-07-25; C├®dula Persona: V-34567567'),(12,19,'Detalle de cambios','Inicial: 50.00; Cuotas: 2; D├¡a Pago: 1; ','Inicial: 20.00; Cuotas: 3; D├¡a Pago: 10; ');
/*!40000 ALTER TABLE `bitacora_detalles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intentos_fallidos`
--

DROP TABLE IF EXISTS `intentos_fallidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `intentos_fallidos` (
  `id_intento` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_intento`),
  KEY `idx_ip_fecha` (`ip`,`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intentos_fallidos`
--

LOCK TABLES `intentos_fallidos` WRITE;
/*!40000 ALTER TABLE `intentos_fallidos` DISABLE KEYS */;
/*!40000 ALTER TABLE `intentos_fallidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulos`
--

DROP TABLE IF EXISTS `modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modulos` (
  `id_modulo` int(12) NOT NULL AUTO_INCREMENT,
  `nombre_modulo` varchar(100) NOT NULL,
  PRIMARY KEY (`id_modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
INSERT INTO `modulos` VALUES (3,'Administrar Clientes'),(4,'Administrar Empleados'),(6,'Administrar Turnos'),(8,'Administrar Productos'),(17,'Administrar Reportes'),(18,'Consultar Bitacora'),(19,'Administrar Usuarios'),(20,'Administrar Roles'),(21,'Administrar Modulos'),(23,'Administrar Telefono'),(24,'Administrar Servicio Tecnico'),(25,'Administrar Bancos'),(26,'Administrar Metodos de Pago'),(27,'Administrar Perfil'),(28,'Administrar Marcas'),(29,'Administrar Categoria'),(30,'Administrar Especialidad'),(33,'Administrar Financiamiento'),(34,'Administrar Chequeo'),(35,'Administrar Ventas'),(36,'Administrar Proveedores'),(37,'Administrar Entradas'),(38,'Administrar Notificacion'),(39,'Administrar Base De Datos'),(40,'Administrar Bitacora'),(41,'Administrar Orden'),(42,'Administrar Chequeo Orden'),(43,'Administrar Servicio Venta'),(44,'Administrar Ventas Online');
/*!40000 ALTER TABLE `modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `mensaje` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(50) NOT NULL,
  PRIMARY KEY (`id_notificacion`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
INSERT INTO `notificaciones` VALUES (4,'Prueba de stock: El producto X tiene poco stock','2026-05-28 19:32:04','stock'),(5,'Prueba de stock: Producto bajo en inventario','2026-05-28 19:32:39','stock'),(6,'Prueba de stock: Producto bajo en inventario','2026-05-28 19:32:47','stock'),(7,'El producto \'Forros de silicona\' alcanzó stock crítico: 3','2026-05-28 21:14:35','stock'),(8,'El producto \'Forros de silicona\' alcanzó stock crítico: 0','2026-05-28 21:16:37','stock'),(9,'El producto \'Forros de silicona\' tiene stock crítico: 1 unidades.','2026-05-28 21:19:03','stock'),(10,'El producto \'Forros de silicona\' alcanzó stock crítico: 1','2026-05-28 21:22:36','stock'),(11,'El producto \'Forros de silicona\' alcanzó stock crítico: 1','2026-05-28 21:24:31','stock'),(12,'El producto \'Forros de silicona\' alcanzó stock crítico: 3','2026-05-28 21:26:56','stock'),(14,'El producto \'Forros de silicona\' alcanzó stock crítico: 1','2026-05-28 21:34:01','stock'),(15,'El producto \'Forros de silicona\' alcanzó stock crítico: 1','2026-05-28 21:37:36','stock'),(16,'El producto \'Forros de silicona\' alcanzó stock crítico: 1','2026-05-28 21:39:37','stock'),(17,'El producto \'Forros de silicona\' alcanzó stock crítico: 2','2026-05-28 21:44:12','stock'),(26,'Financiamiento #13 (Cliente: Dayana Perez, Producto: Iphone 17) vence en 48 horas.','2026-05-29 06:01:52','recordatorio'),(27,'Financiamiento #13 (Cliente: Dayana Perez, Producto: Iphone 17) bloqueado por morasidad.','2026-05-29 06:04:16','alerta'),(28,'Pago recibido en cuota #45','2026-05-29 06:04:33','pago'),(29,'Pago recibido en cuota #46','2026-05-29 06:06:47','pago'),(30,'Pago recibido en cuota #67','2026-06-05 04:39:39','pago'),(31,'Pago recibido en cuota #79','2026-06-05 05:03:42','pago'),(32,'El producto Mica tiene stock bajo: 5 unidades.','2026-06-08 17:35:37','stock'),(33,'Entrada #61 eliminada por usuario administrador','2026-06-08 22:23:35','eliminacion'),(34,'Entrada #60 eliminada por usuario administrador','2026-06-08 22:23:44','eliminacion'),(35,'Entrada #64 eliminada por usuario administrador','2026-06-08 22:23:52','eliminacion'),(36,'Entrada #63 eliminada por usuario administrador','2026-06-08 22:24:44','eliminacion'),(37,'Entrada #62 eliminada por usuario administrador','2026-06-08 22:25:07','eliminacion'),(38,'Entrada de productos #65 registrada por proveedor J-27272727','2026-06-08 22:27:15','registro'),(39,'Entrada de productos #66 registrada por proveedor G-27272727','2026-06-08 22:30:59','registro'),(40,'Entrada de productos #67 registrada por proveedor J-8777999','2026-06-08 22:31:31','registro'),(41,'Entrada #66 eliminada por usuario administrador','2026-06-08 22:33:53','eliminacion'),(42,'Entrada #67 eliminada por usuario administrador','2026-06-08 22:39:21','eliminacion'),(43,'Entrada #65 eliminada por usuario administrador','2026-06-08 22:39:40','eliminacion'),(44,'Entrada de productos #68 registrada por proveedor J-8777999','2026-06-08 22:40:00','registro'),(45,'Entrada #68 modificada por usuario administrador','2026-06-08 22:40:21','modificacion'),(46,'Entrada #69 registrada:\n50 x Producto ID 12','2026-06-09 00:23:34','info'),(47,'Entrada #70 registrada:\n70 x Producto ID 15','2026-06-09 00:26:20','info'),(48,'El producto AirPods Pro 2 tiene stock bajo: 0 unidades.','2026-06-09 00:30:17','stock'),(49,'El producto Cable USB-C tiene stock bajo: 0 unidades.','2026-06-09 00:31:34','stock'),(50,'El producto Kit de Limpieza tiene stock bajo: 0 unidades.','2026-06-09 00:49:00','stock'),(51,'El producto Enchufe Inteligente tiene stock bajo: 0 unidades.','2026-06-09 00:49:16','stock'),(52,'Llegó mercancía: 3 x AirPods Pro 2','2026-06-09 00:51:56','info'),(53,'Garantía por vencer: Mica - Vence el 2026-06-10 20:52:21','2026-06-09 00:52:23','aviso'),(54,'Llegó mercancía: 20 x Cable USB-C','2026-06-09 00:52:43','info'),(55,'Garantía por vencer: Mica - Vence el 2026-06-10 20:52:21','2026-06-09 00:52:46','aviso'),(56,'Garantía por vencer: Mica - Vence el 2026-06-10 20:52:21','2026-06-09 00:53:43','aviso'),(57,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 00:57:13','alerta'),(58,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 00:57:13','alerta'),(59,'Garantía por vencer: Mica - Vence el 2026-06-10 20:52:21','2026-06-09 00:57:13','aviso'),(60,'Llegó mercancía: 2 x Kit de Limpieza','2026-06-09 00:57:40','info'),(61,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 00:57:43','alerta'),(62,'Garantía por vencer: Mica - Vence el 2026-06-10 20:52:21','2026-06-09 00:57:43','aviso'),(63,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 00:57:52','alerta'),(64,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 00:57:52','alerta'),(65,'Garantía por vencer: Mica - Vence el 2026-06-10 20:52:21','2026-06-09 00:57:53','aviso'),(66,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 00:58:14','alerta'),(67,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 00:58:15','alerta'),(68,'Garantía por vencer: Mica - Vence el 2026-06-10 20:52:21','2026-06-09 00:58:15','aviso'),(69,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 02:20:25','alerta'),(70,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 02:20:25','alerta'),(71,'Garantía por vencer: Mica - Quedan 2 días','2026-06-09 02:20:25','aviso'),(72,'Llegó mercancía: 20 x Cargador Rápido 33W','2026-06-09 02:20:52','info'),(73,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 02:20:53','alerta'),(74,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 02:20:54','alerta'),(75,'Garantía por vencer: Mica - Quedan 2 días','2026-06-09 02:20:54','aviso'),(76,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 02:21:31','alerta'),(77,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 02:21:32','alerta'),(78,'Garantía por vencer: Mica - Quedan 2 días','2026-06-09 02:21:32','aviso'),(79,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 02:21:32','aviso'),(80,'Llegó mercancía: 90 x Enchufe Inteligente','2026-06-09 02:34:15','info'),(81,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 02:34:16','alerta'),(82,'Garantía por vencer: Mica - Quedan 2 días','2026-06-09 02:34:17','aviso'),(83,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 02:34:17','aviso'),(84,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 02:34:30','alerta'),(85,'Garantía por vencer: Mica - Quedan 2 días','2026-06-09 02:34:30','aviso'),(86,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 02:34:31','aviso'),(87,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 02:34:51','alerta'),(88,'Garantía por vencer: Mica - Quedan 2 días','2026-06-09 02:34:51','aviso'),(89,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 02:34:51','aviso'),(90,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 02:35:49','alerta'),(91,'Garantía por vencer: Mica - Quedan 2 días','2026-06-09 02:35:49','aviso'),(92,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 02:35:50','aviso'),(93,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 02:36:01','alerta'),(94,'Garantía por vencer: Mica - Quedan 2 días','2026-06-09 02:36:01','aviso'),(95,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 02:36:01','aviso'),(96,'Llegó mercancía: 16 x AirPods Pro 2','2026-06-09 02:36:28','info'),(97,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 02:36:29','alerta'),(98,'Garantía por vencer: Mica - Quedan 2 días','2026-06-09 02:36:30','aviso'),(99,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 02:36:30','aviso'),(100,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 02:36:41','alerta'),(101,'Garantía por vencer: Mica - Quedan 2 días','2026-06-09 02:36:41','aviso'),(102,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 02:36:41','aviso'),(103,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 04:22:56','alerta'),(104,'Garantía vence mañana: Mica','2026-06-09 04:22:56','aviso'),(105,'Garantía por vencer: Protector de pantalla - Quedan 2 días','2026-06-09 04:22:56','aviso'),(106,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 04:23:13','alerta'),(107,'Garantía por vencer: Protector de pantalla - Quedan 2 días','2026-06-09 04:23:14','aviso'),(108,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 04:23:31','alerta'),(109,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 04:23:34','alerta'),(110,'Garantía por vencer: Protector de pantalla - Quedan 2 días','2026-06-09 04:23:35','aviso'),(111,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 04:23:42','alerta'),(112,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 04:23:49','alerta'),(113,'Garantía por vencer: Protector de pantalla - Quedan 2 días','2026-06-09 04:23:50','aviso'),(114,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:14:03','alerta'),(115,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 05:14:04','alerta'),(116,'Garantía por vencer: Protector de pantalla - Quedan 2 días','2026-06-09 05:14:04','aviso'),(117,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:14:17','alerta'),(118,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 05:14:17','alerta'),(119,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:14:32','alerta'),(120,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 05:14:32','alerta'),(121,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:14:43','alerta'),(122,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:14:44','alerta'),(123,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 05:14:46','alerta'),(124,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:14:57','alerta'),(125,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:14:57','alerta'),(126,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 05:14:58','alerta'),(127,'Llegó mercancía: 70 x Soporte Auto','2026-06-09 05:15:54','info'),(128,'Llegó mercancía: 2 x Kit de Limpieza','2026-06-09 05:15:55','info'),(129,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:16:00','alerta'),(130,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 05:16:00','alerta'),(131,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 05:16:00','aviso'),(132,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:16:37','alerta'),(133,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 05:16:37','alerta'),(134,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 05:16:38','aviso'),(135,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:17:34','alerta'),(136,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 05:17:35','alerta'),(137,'Garantía por vencer: Protector de pantalla - Quedan 3 días','2026-06-09 05:17:36','aviso'),(138,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:18:22','alerta'),(139,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:18:22','alerta'),(140,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 05:18:23','alerta'),(141,'Llegó mercancía: 2 x Cable USB-C','2026-06-09 05:19:02','info'),(142,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:19:07','alerta'),(143,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:19:07','alerta'),(144,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:19:37','alerta'),(145,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:19:37','alerta'),(146,'Pago recibido en cuota #83','2026-06-09 05:29:27','pago'),(147,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:37:14','alerta'),(148,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:37:14','alerta'),(149,'Llegó mercancía: 8 x AirPods Pro 2','2026-06-09 05:37:45','info'),(150,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:37:47','alerta'),(151,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:37:47','alerta'),(152,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:38:06','alerta'),(153,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:38:07','alerta'),(154,'Reposición necesaria: Kit de Limpieza - Quedan 0 unidades','2026-06-09 05:38:21','alerta'),(155,'Reposición necesaria: Enchufe Inteligente - Quedan 0 unidades','2026-06-09 05:38:21','alerta'),(156,'Reposición necesaria: Cable USB-C - Quedan 0 unidades','2026-06-09 05:38:22','alerta'),(157,'Financiamiento #8 (Cliente: Ederson Alejandro Gonzalez Turan, Producto: Iphone xs max) vence en 48 horas.','2026-06-29 11:16:41','recordatorio'),(158,'Financiamiento #9 (Cliente: Gerardo Fernando Hernandez Monsalve, Producto: Samsung Galaxy A54) vence en 48 horas.','2026-06-29 11:16:41','recordatorio'),(159,'Financiamiento #8 (Cliente: Ederson Alejandro Gonzalez Turan, Producto: Iphone xs max) bloqueado por morosidad.','2026-07-11 20:20:25','alerta'),(160,'Financiamiento #9 (Cliente: Gerardo Fernando Hernandez Monsalve, Producto: Samsung Galaxy A54) bloqueado por morosidad.','2026-07-11 20:20:29','alerta'),(161,'El producto Discossd tiene stock bajo: 5 unidades.','2026-07-27 19:47:03','stock'),(162,'El producto Discossd Grande tiene stock bajo: 5 unidades.','2026-07-27 20:38:06','stock');
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones_usuario`
--

DROP TABLE IF EXISTS `notificaciones_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificaciones_usuario` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cedula_usuario` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_notificacion` int(11) DEFAULT NULL,
  `leida` tinyint(1) NOT NULL,
  `enviada` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_notificacion` (`id_notificacion`),
  KEY `cedula_usuario` (`cedula_usuario`),
  CONSTRAINT `notificaciones_usuario_ibfk_1` FOREIGN KEY (`id_notificacion`) REFERENCES `notificaciones` (`id_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notificaciones_usuario_ibfk_2` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones_usuario`
--

LOCK TABLES `notificaciones_usuario` WRITE;
/*!40000 ALTER TABLE `notificaciones_usuario` DISABLE KEYS */;
INSERT INTO `notificaciones_usuario` VALUES (21,'administrador',30,1,0),(22,'V-30753799',31,0,0),(23,'administrador',32,1,0),(24,'administrador',33,1,0),(25,'administrador',34,1,0),(26,'administrador',35,1,0),(27,'administrador',36,1,0),(28,'administrador',37,1,0),(29,'administrador',38,1,0),(30,'administrador',39,1,0),(31,'administrador',40,1,0),(32,'administrador',41,1,0),(33,'administrador',42,1,0),(34,'administrador',43,1,0),(35,'administrador',44,1,0),(36,'administrador',45,1,0),(37,'administrador',46,1,0),(38,'administrador',47,1,0),(39,'administrador',48,1,0),(40,'administrador',49,1,0),(41,'administrador',50,1,0),(42,'administrador',51,1,0),(43,'administrador',52,1,0),(44,'administrador',53,1,0),(45,'administrador',54,1,0),(46,'administrador',55,1,0),(47,'administrador',56,1,0),(48,'administrador',57,1,0),(49,'administrador',58,1,0),(50,'administrador',59,1,0),(51,'administrador',60,1,0),(52,'administrador',61,1,0),(53,'administrador',62,1,0),(54,'administrador',63,1,0),(55,'administrador',64,0,0),(56,'administrador',65,1,0),(57,'administrador',66,0,0),(58,'administrador',67,0,0),(59,'administrador',68,0,0),(60,'administrador',69,0,0),(61,'administrador',70,0,0),(62,'administrador',71,0,0),(63,'administrador',72,0,0),(64,'administrador',73,0,0),(65,'administrador',74,0,0),(66,'administrador',75,0,0),(67,'administrador',76,0,0),(68,'administrador',77,0,0),(69,'administrador',78,0,0),(70,'administrador',79,0,0),(71,'administrador',80,0,0),(72,'administrador',81,1,0),(73,'administrador',82,1,0),(74,'administrador',83,1,0),(75,'administrador',84,1,0),(76,'administrador',85,1,0),(77,'administrador',86,1,0),(78,'administrador',87,1,0),(79,'administrador',88,1,0),(80,'administrador',89,1,0),(81,'administrador',90,0,0),(82,'administrador',91,0,0),(83,'administrador',92,0,0),(84,'administrador',93,0,0),(85,'administrador',94,0,0),(86,'administrador',95,0,0),(87,'administrador',96,0,0),(88,'administrador',97,0,0),(89,'administrador',98,0,0),(90,'administrador',99,1,0),(91,'administrador',100,0,0),(92,'administrador',101,0,0),(93,'administrador',102,1,0),(94,'administrador',103,0,0),(95,'administrador',104,1,0),(96,'administrador',105,0,0),(97,'administrador',106,0,0),(98,'administrador',107,0,0),(99,'administrador',108,0,0),(100,'administrador',109,0,0),(101,'administrador',110,0,0),(102,'administrador',111,0,0),(103,'administrador',112,0,0),(104,'administrador',113,0,0),(105,'administrador',114,0,0),(106,'administrador',115,0,0),(107,'administrador',116,0,0),(108,'administrador',117,0,0),(109,'administrador',118,0,0),(110,'administrador',119,0,0),(111,'administrador',120,0,0),(112,'administrador',121,0,0),(113,'administrador',122,0,0),(114,'administrador',123,0,0),(115,'administrador',124,0,0),(116,'administrador',125,0,0),(117,'administrador',126,0,0),(118,'administrador',127,0,0),(119,'administrador',128,0,0),(120,'administrador',129,0,0),(121,'administrador',130,0,0),(122,'administrador',131,0,0),(123,'administrador',132,0,0),(124,'administrador',133,0,0),(125,'administrador',134,1,0),(126,'administrador',135,0,0),(127,'administrador',136,1,0),(128,'administrador',137,0,0),(129,'administrador',138,0,0),(130,'administrador',139,0,0),(131,'administrador',140,0,0),(132,'administrador',141,0,0),(133,'administrador',142,0,0),(134,'administrador',143,0,0),(135,'administrador',144,0,0),(136,'administrador',145,0,0),(137,'administrador',146,0,0),(138,'administrador',147,0,0),(139,'administrador',148,0,0),(140,'administrador',149,1,0),(141,'administrador',150,1,0),(142,'administrador',151,0,0),(143,'administrador',152,0,0),(144,'administrador',153,1,0),(145,'administrador',154,1,0),(146,'administrador',155,1,0),(147,'administrador',156,1,0),(148,'administrador',157,0,0),(149,'administrador',158,0,0),(150,'administrador',159,0,0),(151,'administrador',160,0,0),(152,'administrador',161,0,0),(153,'administrador',162,0,0);
/*!40000 ALTER TABLE `notificaciones_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol_permisos`
--

DROP TABLE IF EXISTS `rol_permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rol_permisos` (
  `id_rol` int(12) NOT NULL,
  `id_modulo` int(12) NOT NULL,
  `id_accion` int(11) NOT NULL,
  PRIMARY KEY (`id_rol`,`id_modulo`,`id_accion`),
  KEY `fk_rol_permisos_modulo` (`id_modulo`),
  KEY `fk_rol_permisos_accion` (`id_accion`),
  CONSTRAINT `fk_rol_permisos_accion` FOREIGN KEY (`id_accion`) REFERENCES `acciones` (`id_accion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rol_permisos_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rol_permisos_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`idRol`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol_permisos`
--

LOCK TABLES `rol_permisos` WRITE;
/*!40000 ALTER TABLE `rol_permisos` DISABLE KEYS */;
INSERT INTO `rol_permisos` VALUES (1,3,1),(1,3,2),(1,3,3),(1,3,4),(1,3,5),(1,3,6),(1,4,1),(1,4,2),(1,4,3),(1,4,4),(1,4,5),(1,4,6),(1,6,1),(1,6,2),(1,6,3),(1,6,4),(1,6,5),(1,6,6),(1,8,1),(1,8,2),(1,8,3),(1,8,4),(1,8,5),(1,8,6),(1,17,1),(1,17,2),(1,17,3),(1,17,4),(1,17,5),(1,17,6),(1,18,1),(1,18,2),(1,18,3),(1,18,4),(1,18,5),(1,18,6),(1,19,1),(1,19,2),(1,19,3),(1,19,4),(1,19,5),(1,19,6),(1,20,1),(1,20,2),(1,20,3),(1,20,4),(1,20,5),(1,20,6),(1,21,1),(1,21,2),(1,21,3),(1,21,4),(1,21,5),(1,21,6),(1,23,1),(1,23,2),(1,23,3),(1,23,4),(1,23,5),(1,23,6),(1,24,1),(1,24,2),(1,24,3),(1,24,4),(1,24,5),(1,24,6),(1,25,1),(1,25,2),(1,25,3),(1,25,4),(1,25,5),(1,25,6),(1,26,1),(1,26,2),(1,26,3),(1,26,4),(1,26,5),(1,26,6),(1,27,1),(1,27,2),(1,27,3),(1,27,4),(1,27,5),(1,27,6),(1,28,1),(1,28,2),(1,28,3),(1,28,4),(1,28,5),(1,28,6),(1,29,1),(1,29,2),(1,29,3),(1,29,4),(1,29,5),(1,29,6),(1,30,1),(1,30,2),(1,30,3),(1,30,4),(1,30,5),(1,30,6),(1,33,1),(1,33,2),(1,33,3),(1,33,4),(1,33,5),(1,33,6),(1,34,1),(1,34,2),(1,34,3),(1,34,4),(1,34,5),(1,34,6),(1,35,1),(1,35,2),(1,35,3),(1,35,4),(1,35,5),(1,35,6),(1,36,1),(1,36,2),(1,36,3),(1,36,4),(1,36,5),(1,36,6),(1,37,1),(1,37,2),(1,37,3),(1,37,4),(1,37,5),(1,37,6),(1,41,1),(1,41,2),(1,41,3),(1,41,4),(1,41,5),(1,41,6),(1,42,1),(1,42,2),(1,42,3),(1,42,4),(1,42,5),(1,42,6),(1,43,1),(1,43,2),(1,43,3),(1,43,4),(1,43,5),(1,43,6),(1,44,7),(1,44,8),(3,3,1),(3,3,2),(3,3,3),(3,3,4),(3,3,5),(3,3,6),(3,4,1),(3,4,2),(3,4,3),(3,4,4),(3,4,5),(3,4,6),(3,6,1),(3,6,2),(3,6,3),(3,6,4),(3,6,5),(3,6,6),(3,8,1),(3,8,2),(3,8,3),(3,8,4),(3,8,5),(3,8,6),(3,17,1),(3,17,2),(3,17,3),(3,17,4),(3,17,5),(3,17,6),(3,18,1),(3,18,2),(3,18,3),(3,18,4),(3,18,5),(3,18,6),(3,19,1),(3,19,2),(3,19,3),(3,19,4),(3,19,5),(3,19,6),(3,20,1),(3,20,2),(3,20,3),(3,20,4),(3,20,5),(3,20,6),(3,21,1),(3,21,2),(3,21,3),(3,21,4),(3,21,5),(3,21,6),(3,23,1),(3,23,2),(3,23,3),(3,23,4),(3,23,5),(3,23,6),(3,24,1),(3,24,2),(3,24,3),(3,24,4),(3,24,5),(3,24,6),(3,25,1),(3,25,2),(3,25,3),(3,25,4),(3,25,5),(3,25,6),(3,26,1),(3,26,2),(3,26,3),(3,26,4),(3,26,5),(3,26,6),(3,27,1),(3,27,2),(3,27,3),(3,27,4),(3,27,5),(3,27,6),(3,28,1),(3,28,2),(3,28,3),(3,28,4),(3,28,5),(3,28,6),(3,29,1),(3,29,2),(3,29,3),(3,29,4),(3,29,5),(3,29,6),(3,30,1),(3,30,2),(3,30,3),(3,30,4),(3,30,5),(3,30,6),(3,33,1),(3,33,2),(3,33,3),(3,33,4),(3,33,5),(3,33,6),(3,34,1),(3,34,2),(3,34,3),(3,34,4),(3,34,5),(3,34,6),(3,35,1),(3,35,2),(3,35,3),(3,35,4),(3,35,5),(3,35,6),(3,36,1),(3,36,2),(3,36,3),(3,36,4),(3,36,5),(3,36,6),(3,37,1),(3,37,2),(3,37,3),(3,37,4),(3,37,5),(3,37,6),(3,38,1),(3,38,2),(3,38,3),(3,38,4),(3,38,5),(3,38,6),(3,39,1),(3,39,2),(3,39,3),(3,39,4),(3,39,5),(3,39,6),(3,40,1),(3,40,2),(3,40,3),(3,40,4),(3,40,5),(3,40,6),(3,41,1),(3,41,2),(3,41,3),(3,41,4),(3,41,5),(3,41,6),(3,42,1),(3,42,2),(3,42,3),(3,42,4),(3,42,5),(3,42,6),(3,43,1),(3,43,2),(3,43,3),(3,43,4),(3,43,5),(3,43,6),(3,44,7),(3,44,8),(6,27,1),(6,27,2),(6,27,3),(6,27,4),(6,27,5),(6,27,6);
/*!40000 ALTER TABLE `rol_permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `idRol` int(12) NOT NULL AUTO_INCREMENT,
  `descripcion_rol` varchar(50) NOT NULL,
  PRIMARY KEY (`idRol`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Superusuario'),(3,'Administrador'),(6,'Cliente');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `cedula_usuario` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `clave` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `estatus` varchar(100) NOT NULL DEFAULT 'Activo',
  `codigo` varchar(6) DEFAULT NULL,
  `id_rol` int(12) NOT NULL DEFAULT 1,
  PRIMARY KEY (`cedula_usuario`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`idRol`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES ('admin_nuevo','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,NULL,'Activo',NULL,1),('administrador','$2y$10$HaDJvRG8VebpzQMme7UrJulz7Z2Vejoiq6kUXO/bH7ri8k6gWGAEK',NULL,NULL,'Activo',NULL,1),('CAJERA01','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',NULL,NULL,'Activo',NULL,6),('V-10222260','$2y$10$unuP/wDedYEuyttORmCP1OsFSNcDWr592z8.bueO/QrAfnK1661pK',NULL,NULL,'Activo',NULL,6),('V-30000000','$2y$10$kjObt53Pyp/ER.t1waca8OtHUzIyHhocrQ4RlwrHx3c86XyHhIVEW',NULL,NULL,'Activo',NULL,6),('V-30155522','$2y$10$8gtNPaU4Z.XsYIfXyhWDWucm/8brKqedW5JiERPqW5TBVbP.OM4Km',NULL,NULL,'Activo',NULL,6),('V-30753799','$2y$10$8sDiwAJVbDy3cbDkoc5pDe51328ewJn6pNWbMGhbLEEkcVQlGZXiG',NULL,NULL,'Activo','xv9hsu',3),('V-7418869','$2y$10$w8Iro9yz4e7srRCj9eOXOeptgr1bmqqGQHyanUANJf0A8UICyqpAm',NULL,NULL,'Activo',NULL,6);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-24 19:52:36
