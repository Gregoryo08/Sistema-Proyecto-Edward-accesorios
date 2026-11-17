--
-- Migracion: agregar a `pedidos` las columnas que congelan la tasa al crear el pedido
-- Contexto: el commit "fix(ventas): congelar total_bs y tasa_bs al crear el pedido"
-- escribio el codigo (PedidoactualizarTotalBs) pero nunca creo las columnas.
-- Sin ellas, guardarPedido falla con "Unknown column 'total_bs' in 'field list'".
--
-- Ejecutar en phpMyAdmin, en el panel de alwaysdata, o por consola:
--   mysql -u root sistema_edward < databases/migraciones/2026-10-01_total_bs_pedidos.sql
--
-- MariaDB no admite ADD COLUMN IF NOT EXISTS, por eso se consultan
-- information_schema antes de cada alteracion. Es idempotente: se puede
-- ejecutar mas de una vez sin romper nada.
--

SET @db = DATABASE();

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `pedidos` ADD COLUMN `total_bs` DECIMAL(14,2) NULL DEFAULT NULL AFTER `total`',
        'SELECT ''total_bs ya existe'' AS aviso'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'pedidos'
      AND COLUMN_NAME = 'total_bs'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE `pedidos` ADD COLUMN `tasa_bs` DECIMAL(12,4) NULL DEFAULT NULL AFTER `total_bs`',
        'SELECT ''tasa_bs ya existe'' AS aviso'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @db
      AND TABLE_NAME = 'pedidos'
      AND COLUMN_NAME = 'tasa_bs'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
