--
-- Migración: Crear tabla tasa_cambio para persistencia global
-- Ejecutar en phpMyAdmin o mysql:
--   source C:/xampp/htdocs/src/databases/migracion_tasa_cambio.sql
--

CREATE TABLE IF NOT EXISTS `tasa_cambio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tasa` decimal(12,2) NOT NULL DEFAULT 60.00,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fuente` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar registro inicial si no existe
INSERT INTO `tasa_cambio` (`id`, `tasa`, `fecha_actualizacion`, `fuente`)
SELECT 1, 60.00, NOW(), 'default'
WHERE NOT EXISTS (SELECT 1 FROM `tasa_cambio` WHERE `id` = 1);