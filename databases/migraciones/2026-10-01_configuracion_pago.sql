--
-- Migracion: mover los datos de las cuentas receptoras fuera del codigo PHP
--
-- Contexto: la clase BancoReceptorModel traia una constante RECEPTORES con los
-- datos de las cuentas donde el cliente transfiere. Esos valores ya existen en
-- la tabla `bancos` (fila id_banco = 3): mismo numero de cuenta, mismo
-- telefono, misma cedula. El modelo solo duplicaba informacion y la dejaba
-- congelada en el codigo, de modo que cambiar la cuenta en la base no cambiaba
-- nada en la tienda.
--
-- Esta tabla guarda SOLO la configuracion propia de la tienda (a que cuenta
-- llega cada metodo de pago). El nombre del banco NO se guarda aqui: se
-- resuelve con un JOIN contra `bancos`, que es el unico lugar donde vive el
-- listado de bancos.
--
-- Ejecutar en phpMyAdmin, en el panel de alwaysdata, o por consola:
--   mysql -u root sistema_edward < databases/migraciones/2026-10-01_configuracion_pago.sql
--
-- MariaDB no admite ADD COLUMN IF NOT EXISTS, por eso se consultan
-- information_schema antes de alterar. Es idempotente.
--

CREATE TABLE IF NOT EXISTS `configuracion_pago` (
  `id_config`       int(11) NOT NULL AUTO_INCREMENT,
  `metodo`          varchar(30) NOT NULL,
  `id_banco`        int(11) NOT NULL,
  `numero_cuenta`   varchar(30) NOT NULL DEFAULT '',
  `tipo_cuenta`     varchar(30) NOT NULL DEFAULT '',
  `titular`         varchar(150) NOT NULL DEFAULT '',
  `cedula_rif`      varchar(20) NOT NULL DEFAULT '',
  `telefono`        varchar(20) NOT NULL DEFAULT '',
  `cedula_pago_movil` varchar(20) NOT NULL DEFAULT '',
  `estado`          enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_registro`  timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `uq_config_metodo` (`metodo`),
  KEY `idx_config_banco` (`id_banco`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `configuracion_pago`
    (`metodo`, `id_banco`, `numero_cuenta`, `tipo_cuenta`, `titular`, `cedula_rif`, `telefono`, `cedula_pago_movil`)
SELECT 'transferencia', 3, '0102-0202-1545-7896-3784', 'Corriente', 'Edward Accesorios C.A.', 'J-44445698', '', ''
WHERE NOT EXISTS (SELECT 1 FROM `configuracion_pago` WHERE `metodo` = 'transferencia');

INSERT INTO `configuracion_pago`
    (`metodo`, `id_banco`, `numero_cuenta`, `tipo_cuenta`, `titular`, `cedula_rif`, `telefono`, `cedula_pago_movil`)
SELECT 'pago_movil', 3, '', '', 'Edward Accesorios C.A.', 'J-44445698', '0414-1234567', 'V-12345678'
WHERE NOT EXISTS (SELECT 1 FROM `configuracion_pago` WHERE `metodo` = 'pago_movil');
