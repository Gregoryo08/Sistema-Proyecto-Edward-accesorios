--
-- Migracion: ligar cada unidad de telefono con la entrada que la ingreso.
--
-- Contexto: el modulo Entradas de Productos (fase 20.9) ya permite dar entrada a
-- telefonos capturando IMEI. Sin una columna que diga de que entrada salio cada
-- unidad, no se puede modificar ni eliminar una entrada de telefonos sin
-- descuadrar el inventario, y por eso esas dos acciones quedaron bloqueadas.
-- Esta migracion agrega `id_entrada` (nullable) para poder revertir con
-- exactitud. Es aditiva: el codigo viejo sigue funcionando porque ignora la
-- columna, y las unidades anteriores quedan con NULL.
--
-- Nombre del indice y de la restriccion: `fk_unidad_entrada`.
-- ON DELETE SET NULL: si alguna vez se borra una entrada por fuera de este
-- modulo, la unidad no se destruye, solo pierde el vinculo.
--
-- Ejecutar en phpMyAdmin, en el panel de alwaysdata, o por consola:
--   mysql -u root sistema_edward < databases/migraciones/2026-10-13_unidades_telefonos_entrada.sql
--
-- MariaDB no admite ADD COLUMN IF NOT EXISTS, por eso se consulta
-- information_schema antes de cada alteracion. Idempotente: se puede ejecutar
-- mas de una vez sin romper nada.
--

SET @db = DATABASE();

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `unidades_telefonos` ADD COLUMN `id_entrada` INT(11) NULL DEFAULT NULL AFTER `id_producto`',
        'SELECT ''id_entrada ya existe'' AS aviso'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'unidades_telefonos'
      AND COLUMN_NAME = 'id_entrada'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `unidades_telefonos` ADD INDEX `fk_unidad_entrada` (`id_entrada`)',
        'SELECT ''indice fk_unidad_entrada ya existe'' AS aviso'
    )
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'unidades_telefonos'
      AND INDEX_NAME = 'fk_unidad_entrada'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `unidades_telefonos` ADD CONSTRAINT `fk_unidad_entrada` FOREIGN KEY (`id_entrada`) REFERENCES `entradas_productos` (`id_entrada`) ON DELETE SET NULL ON UPDATE CASCADE',
        'SELECT ''fk fk_unidad_entrada ya existe'' AS aviso'
    )
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @db
      AND TABLE_NAME = 'unidades_telefonos'
      AND CONSTRAINT_NAME = 'fk_unidad_entrada'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
