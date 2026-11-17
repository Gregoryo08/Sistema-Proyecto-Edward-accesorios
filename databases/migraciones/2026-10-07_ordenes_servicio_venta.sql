--
-- Migracion: tablas del modulo Chequeo de Ordenes (ordenes de trabajo)
--
-- Contexto: el modulo `chequeo_orden` (controlador, vista y JS) ya usaba
-- la tabla `ordenes` y la clase `App\Sistema\models\orden`, pero ni la tabla
-- ni la clase existian. La pantalla exigia el permiso "Administrar Chequeo
-- Orden" y, aunque un rol lo tuviera, reventaba en el require del modal o en
-- la clase inexistente. La clase `servicio_venta` inserta en `servicio_venta`,
-- tabla que tampoco existia: se crea aqui tambien para que "Procesar" pueda
-- facturar.
--
-- Modelado normalizado (1FN/2FN/3FN):
-- - `ordenes` guarda la cabecera de la orden de trabajo. El cliente se refiere
--   con FK a `persona` (misma relacion que `servicio_tecnico`), la marca con FK
--   a `marcas`. Los totales NO se guardan: se calculan con un JOIN a los
--   repuestos, asi no hay datos derivados duplicados.
-- - `orden_repuestos` guarda cada repuesto/servicio como fila (nada de JSON):
--   cantidad y precio dependen de la fila, el nombre del item depende de su
--   propia clave. La eliminacion de una orden borra sus repuestos (CASCADE).
-- - `servicio_venta` guarda el control de cobro generado por "Procesar".
--
-- Ejecutar en phpMyAdmin, en el panel de alwaysdata, o por consola:
--   mysql -u root sistema_edward < databases/migraciones/2026-10-07_ordenes_servicio_venta.sql
--
-- Idempotente: se puede ejecutar mas de una vez sin romper nada.
--

CREATE TABLE IF NOT EXISTS `ordenes` (
  `id_orden`         int(11)      NOT NULL AUTO_INCREMENT,
  `cedula_persona`   varchar(20)  DEFAULT NULL,
  `id_marca`         int(11)      DEFAULT NULL,
  `equipo`           varchar(150) NOT NULL DEFAULT '',
  `imei`             varchar(40)  NOT NULL DEFAULT '',
  `horas_estimadas`  time         DEFAULT NULL,
  `estado`           tinyint(1)   NOT NULL DEFAULT 0,
  `fecha_registro`   datetime     NOT NULL DEFAULT current_timestamp(),
  `observaciones`    text         DEFAULT NULL,
  `monto_reparacion` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha_entrega`    datetime     DEFAULT NULL,
  PRIMARY KEY (`id_orden`),
  KEY `idx_orden_estado` (`estado`),
  KEY `idx_orden_persona` (`cedula_persona`),
  KEY `idx_orden_marca` (`id_marca`),
  CONSTRAINT `fk_orden_persona` FOREIGN KEY (`cedula_persona`) REFERENCES `persona` (`cedula_persona`) ON DELETE SET NULL,
  CONSTRAINT `fk_orden_marca` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id_marca`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orden_repuestos` (
  `id`              int(11)      NOT NULL AUTO_INCREMENT,
  `id_orden`        int(11)      NOT NULL,
  `nombre`          varchar(150) NOT NULL DEFAULT '',
  `cantidad`        int(11)      NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_detalle_orden` (`id_orden`),
  CONSTRAINT `fk_detalle_orden` FOREIGN KEY (`id_orden`) REFERENCES `ordenes` (`id_orden`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `servicio_venta` (
  `id_servicio_venta` int(11)      NOT NULL AUTO_INCREMENT,
  `fecha`             datetime     NOT NULL DEFAULT current_timestamp(),
  `direccion`         varchar(200) NOT NULL DEFAULT '',
  `metodo_pago`       varchar(50)  NOT NULL DEFAULT '',
  `referencia_pago`   varchar(100) NOT NULL DEFAULT '',
  `total`             decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_servicio_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;