-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: db_edward
-- Tiempo de generación: 07-05-2026 a las 04:15:42
-- Versión del servidor: 10.6.25-MariaDB-ubu2204
-- Versión de PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_edward_usuario`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`%` PROCEDURE `sp_actualizar_estatus_empleado` (IN `estatus` VARCHAR(100), IN `usuario` VARCHAR(20))   BEGIN
    UPDATE empleados SET perfil = estatus WHERE cedula_empleado = usuario;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acciones`
--

CREATE TABLE `acciones` (
  `id_accion` int(11) NOT NULL,
  `nombre_accion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `acciones`
--

INSERT INTO `acciones` (`id_accion`, `nombre_accion`) VALUES
(1, 'registrar'),
(2, 'consultar'),
(3, 'modificar'),
(4, 'eliminar'),
(5, 'listar'),
(6, 'control_total');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id` bigint(20) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` varchar(20) NOT NULL,
  `modulo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(12) NOT NULL,
  `nombre_modulo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `nombre_modulo`) VALUES
(3, 'Administrar Clientes'),
(4, 'Administrar Empleados'),
(5, 'Administrar Cargos'),
(6, 'Administrar Turnos'),
(8, 'Administrar Productos'),
(9, 'Administrar Proveedores'),
(17, 'Administrar Reportes'),
(18, 'Consultar Bitacora'),
(19, 'Administrar Usuarios'),
(20, 'Administrar Roles'),
(21, 'Administrar Modulos'),
(23, 'Administrar Telefono'),
(24, 'Administrar Servicio Tecnico'),
(25, 'Administrar Bancos'),
(26, 'Administrar Metodos de Pago'),
(27, 'Administrar Perfil'),
(28, 'Administrar Marcas'),
(29, 'Administrar Categoria'),
(30, 'Administrar Especialidad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_usuario`
--

CREATE TABLE `notificaciones_usuario` (
  `id` bigint(20) NOT NULL,
  `cedula_usuario` varchar(20) NOT NULL,
  `id_notificacion` int(11) DEFAULT NULL,
  `leida` tinyint(1) NOT NULL,
  `enviada` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `idRol` int(12) NOT NULL,
  `descripcion_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`idRol`, `descripcion_rol`) VALUES
(1, 'Superusuario'),
(3, 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `id_rol` int(12) NOT NULL,
  `id_modulo` int(12) NOT NULL,
  `id_accion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`id_rol`, `id_modulo`, `id_accion`) VALUES
(3, 3, 1),
(3, 3, 2),
(3, 3, 3),
(3, 3, 4),
(3, 3, 5),
(3, 3, 6),
(3, 4, 1),
(3, 4, 2),
(3, 4, 3),
(3, 4, 4),
(3, 4, 5),
(3, 4, 6),
(3, 5, 1),
(3, 5, 2),
(3, 5, 3),
(3, 5, 4),
(3, 5, 5),
(3, 5, 6),
(3, 6, 1),
(3, 6, 2),
(3, 6, 3),
(3, 6, 4),
(3, 6, 5),
(3, 6, 6),
(3, 8, 1),
(3, 8, 2),
(3, 8, 3),
(3, 8, 4),
(3, 8, 5),
(3, 8, 6),
(3, 9, 1),
(3, 9, 2),
(3, 9, 3),
(3, 9, 4),
(3, 9, 5),
(3, 9, 6),
(3, 17, 1),
(3, 17, 2),
(3, 17, 3),
(3, 17, 4),
(3, 17, 5),
(3, 17, 6),
(3, 18, 1),
(3, 18, 2),
(3, 18, 3),
(3, 18, 4),
(3, 18, 5),
(3, 18, 6),
(3, 19, 1),
(3, 19, 2),
(3, 19, 3),
(3, 19, 4),
(3, 19, 5),
(3, 19, 6),
(3, 20, 1),
(3, 20, 2),
(3, 20, 3),
(3, 20, 4),
(3, 20, 5),
(3, 20, 6),
(3, 21, 1),
(3, 21, 2),
(3, 21, 3),
(3, 21, 4),
(3, 21, 5),
(3, 21, 6),
(3, 23, 1),
(3, 23, 2),
(3, 23, 3),
(3, 23, 4),
(3, 23, 5),
(3, 23, 6),
(3, 24, 1),
(3, 24, 2),
(3, 24, 3),
(3, 24, 4),
(3, 24, 5),
(3, 24, 6),
(3, 25, 1),
(3, 25, 2),
(3, 25, 3),
(3, 25, 4),
(3, 25, 5),
(3, 25, 6),
(3, 26, 1),
(3, 26, 2),
(3, 26, 3),
(3, 26, 4),
(3, 26, 5),
(3, 26, 6),
(3, 27, 1),
(3, 27, 2),
(3, 27, 3),
(3, 27, 4),
(3, 27, 5),
(3, 27, 6),
(3, 28, 1),
(3, 28, 2),
(3, 28, 3),
(3, 28, 4),
(3, 28, 5),
(3, 28, 6),
(3, 29, 1),
(3, 29, 2),
(3, 29, 3),
(3, 29, 4),
(3, 29, 5),
(3, 29, 6),
(3, 30, 1),
(3, 30, 2),
(3, 30, 3),
(3, 30, 4),
(3, 30, 5),
(3, 30, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `cedula_usuario` varchar(20) NOT NULL,
  `clave` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `estatus` varchar(100) NOT NULL DEFAULT 'Activo',
  `codigo` varchar(6) DEFAULT NULL,
  `id_rol` int(12) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`cedula_usuario`, `clave`, `reset_token`, `reset_token_expires_at`, `estatus`, `codigo`, `id_rol`) VALUES
('administrador', '$2y$10$weEO6iVpJY4MG01Fh.sBceqacKy6CwG0nLb9i9vSHcCV5mrtJE0Cq', NULL, NULL, 'Activo', NULL, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acciones`
--
ALTER TABLE `acciones`
  ADD PRIMARY KEY (`id_accion`);

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`);

--
-- Indices de la tabla `notificaciones_usuario`
--
ALTER TABLE `notificaciones_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_notificacion` (`id_notificacion`),
  ADD KEY `cedula_usuario` (`cedula_usuario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`idRol`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`id_rol`,`id_modulo`,`id_accion`),
  ADD KEY `fk_rol_permisos_modulo` (`id_modulo`),
  ADD KEY `fk_rol_permisos_accion` (`id_accion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`cedula_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acciones`
--
ALTER TABLE `acciones`
  MODIFY `id_accion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones_usuario`
--
ALTER TABLE `notificaciones_usuario`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `idRol` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificaciones_usuario`
--
ALTER TABLE `notificaciones_usuario`
  ADD CONSTRAINT `notificaciones_usuario_ibfk_1` FOREIGN KEY (`id_notificacion`) REFERENCES `notificaciones` (`id_notificacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notificaciones_usuario_ibfk_2` FOREIGN KEY (`cedula_usuario`) REFERENCES `usuarios` (`cedula_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `fk_rol_permisos_accion` FOREIGN KEY (`id_accion`) REFERENCES `acciones` (`id_accion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rol_permisos_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rol_permisos_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`idRol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`idRol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
