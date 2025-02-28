-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-02-2025 a las 00:28:11
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gimnasia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacion`
--

CREATE TABLE `calificacion` (
  `id` int(11) NOT NULL,
  `participante_id` int(11) NOT NULL,
  `salto` decimal(5,2) DEFAULT NULL,
  `barras` decimal(5,2) DEFAULT NULL,
  `viga` decimal(5,2) DEFAULT NULL,
  `piso` decimal(5,2) DEFAULT NULL,
  `tumbling` decimal(5,2) DEFAULT NULL,
  `arzones` decimal(5,2) DEFAULT NULL,
  `anillos` decimal(5,2) DEFAULT NULL,
  `barras_paralelas` decimal(5,2) DEFAULT NULL,
  `barra_fija` decimal(5,2) DEFAULT NULL,
  `circuitos` decimal(5,2) DEFAULT NULL,
  `panel` varchar(255) NOT NULL,
  `ronda` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacion_old`
--

CREATE TABLE `calificacion_old` (
  `id` int(11) NOT NULL,
  `participante_id` int(11) NOT NULL,
  `salto` decimal(5,2) DEFAULT NULL,
  `barras` decimal(5,2) DEFAULT NULL,
  `viga` decimal(5,2) DEFAULT NULL,
  `piso` decimal(5,2) DEFAULT NULL,
  `tumbling` decimal(5,2) DEFAULT NULL,
  `arzones` decimal(5,2) DEFAULT NULL,
  `anillos` decimal(5,2) DEFAULT NULL,
  `barras_paralelas` decimal(5,2) DEFAULT NULL,
  `barra_fija` decimal(5,2) DEFAULT NULL,
  `circuitos` decimal(5,2) DEFAULT NULL,
  `moved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `calificacion_old`
--

INSERT INTO `calificacion_old` (`id`, `participante_id`, `salto`, `barras`, `viga`, `piso`, `tumbling`, `arzones`, `anillos`, `barras_paralelas`, `barra_fija`, `circuitos`, `moved_at`) VALUES
(19, 105, 100.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-25 16:16:34'),
(20, 106, 10.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-26 19:31:18'),
(21, 107, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-26 19:31:18'),
(22, 108, 10.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-26 19:31:18'),
(23, 124, 1.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-27 23:25:52'),
(24, 142, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-28 23:57:08'),
(25, 153, 10.00, 1.00, 2.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-29 01:01:18'),
(26, 1, 10.00, 10.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-06 18:14:10'),
(27, 1533, 4.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-06 18:14:10'),
(28, 1534, 4.00, 4.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-06 18:14:10'),
(29, 1535, 6.00, 5.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-06 18:14:10'),
(30, 1536, 10.00, 10.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-06 18:14:10'),
(31, 1537, 9.00, 10.00, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-10 19:07:03'),
(32, 1538, 7.00, 9.00, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-10 19:07:03'),
(33, 1539, 10.00, 10.00, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-10 19:07:03'),
(34, 1540, 10.00, 8.00, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-10 19:07:03'),
(35, 1541, 10.00, 7.00, 7.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-10 19:07:03'),
(36, 1542, 8.00, 7.00, 7.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-12-10 19:07:03'),
(37, 1602, 1.20, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 20:38:06'),
(38, 1604, 2.30, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 20:38:06'),
(39, 1607, 3.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 20:38:06'),
(40, 1611, 9.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 20:38:06'),
(41, 1610, 6.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 20:38:06'),
(44, 1623, 1.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 21:53:23'),
(45, 1622, 3.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 21:53:23'),
(46, 1621, 3.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 21:53:23'),
(47, 1620, 4.00, 3.00, 3.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-16 21:53:23'),
(48, 1624, 3.00, 9.00, 10.00, 10.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-17 19:13:42'),
(49, 1634, 10.00, 10.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-18 23:11:57'),
(50, 1635, 10.00, 10.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-18 23:11:57'),
(51, 1637, 8.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-18 23:11:57'),
(52, 1638, 9.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-18 23:11:57'),
(56, 1668, 8.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-19 00:07:35'),
(57, 1669, 8.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-19 00:07:35'),
(58, 1668, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-20 21:32:22'),
(59, 1668, 9.00, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-20 21:37:29'),
(60, 1668, 2.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-02-20 21:38:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacion_ronda`
--

CREATE TABLE `calificacion_ronda` (
  `id` int(11) NOT NULL,
  `participante_id` int(11) NOT NULL,
  `salto` decimal(5,2) DEFAULT NULL,
  `barras` decimal(5,2) DEFAULT NULL,
  `viga` decimal(5,2) DEFAULT NULL,
  `piso` decimal(5,2) DEFAULT NULL,
  `tumbling` decimal(5,2) DEFAULT NULL,
  `arzones` decimal(5,2) DEFAULT NULL,
  `anillos` decimal(5,2) DEFAULT NULL,
  `barras_paralelas` decimal(5,2) DEFAULT NULL,
  `barra_fija` decimal(5,2) DEFAULT NULL,
  `circuitos` decimal(5,2) DEFAULT NULL,
  `panel` varchar(255) NOT NULL,
  `ronda` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificacion_ronda`
--

INSERT INTO `calificacion_ronda` (`id`, `participante_id`, `salto`, `barras`, `viga`, `piso`, `tumbling`, `arzones`, `anillos`, `barras_paralelas`, `barra_fija`, `circuitos`, `panel`, `ronda`) VALUES
(39, 1673, 2.00, 8.00, 8.00, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, '1', 2),
(40, 1671, 3.00, 8.00, 8.00, 8.00, NULL, NULL, NULL, NULL, NULL, NULL, '1', 2),
(42, 1668, 8.55, 9.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '1', 1),
(43, 1669, 9.00, 5.00, 9.00, 9.00, NULL, NULL, NULL, NULL, NULL, NULL, '1', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canciones`
--

CREATE TABLE `canciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `canciones`
--

INSERT INTO `canciones` (`id`, `nombre`, `ruta`) VALUES
(1, 'canción 1', 'sonido.mp3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canciones_predeterminadas`
--

CREATE TABLE `canciones_predeterminadas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `canciones_predeterminadas`
--

INSERT INTO `canciones_predeterminadas` (`id`, `nombre`, `ruta`) VALUES
(1, 'Canción 1', 'sonido.mp3'),
(2, 'sonido2', 'sonido2.mp3\r\n');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `ano_1` int(4) NOT NULL,
  `ano_2` int(4) NOT NULL,
  `nivel` varchar(255) NOT NULL,
  `forma` varchar(255) NOT NULL,
  `rama` int(1) NOT NULL DEFAULT 1,
  `aparato_salto` tinyint(1) DEFAULT 0,
  `aparato_barras` tinyint(1) DEFAULT 0,
  `aparato_viga` tinyint(1) DEFAULT 0,
  `aparato_piso` tinyint(1) DEFAULT 0,
  `aparato_tumbling` tinyint(1) DEFAULT 0,
  `aparato_arzones` tinyint(1) DEFAULT 0,
  `aparato_anillos` tinyint(1) DEFAULT 0,
  `aparato_barras_paralelas` tinyint(1) DEFAULT 0,
  `aparato_barra_fija` tinyint(1) DEFAULT 0,
  `aparato_circuitos` tinyint(1) DEFAULT 0,
  `max` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `categoria`, `ano_1`, `ano_2`, `nivel`, `forma`, `rama`, `aparato_salto`, `aparato_barras`, `aparato_viga`, `aparato_piso`, `aparato_tumbling`, `aparato_arzones`, `aparato_anillos`, `aparato_barras_paralelas`, `aparato_barra_fija`, `aparato_circuitos`, `max`) VALUES
(74, '2000-2000', 2000, 2000, 'N3', 'Sumatoria', 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 10),
(2040, '2001-2001', 2001, 2001, 'N3', 'Sumatoria', 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 10),
(2041, '2002-2002', 2002, 2002, 'N2', 'Sumatoria', 2, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 10),
(2042, '2003-2003', 2003, 2003, 'N1', 'Sumatoria', 2, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `club`
--

CREATE TABLE `club` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `sufijo` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `club`
--

INSERT INTO `club` (`id`, `nombre`, `sufijo`, `img`) VALUES
(98, 'Club Deportivo Alfa', 'CDA', 'lego.jpg'),
(99, 'Club Atlético Beta', 'CAB', 'lego.jpg'),
(100, 'Club Social Gamma', 'CSG', 'lego.jpg'),
(101, 'Club de Fútbol Delta', 'CFD', 'lego.jpg'),
(102, 'Club Deportivo Épsilon', 'CDE', 'lego.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `calificaciones_abiertas` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `calificaciones_abiertas`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_lugares_unificada`
--

CREATE TABLE `configuracion_lugares_unificada` (
  `id` int(11) NOT NULL,
  `lugar` int(11) NOT NULL,
  `rango_min` decimal(3,2) NOT NULL,
  `rango_max` decimal(4,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `configuracion_lugares_unificada`
--

INSERT INTO `configuracion_lugares_unificada` (`id`, `lugar`, `rango_min`, `rango_max`) VALUES
(1, 1, 9.00, 10.00),
(2, 2, 7.00, 8.99),
(3, 3, 0.00, 6.99);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participante`
--

CREATE TABLE `participante` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `rama` varchar(255) NOT NULL,
  `ano_nacimiento` year(4) NOT NULL,
  `club_id` varchar(255) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `cancion_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `participante`
--

INSERT INTO `participante` (`id`, `nombre`, `rama`, `ano_nacimiento`, `club_id`, `categoria_id`, `cancion_id`) VALUES
(1668, 'Juan Pérez', '1', '2000', '98', 74, NULL),
(1669, 'María López', '1', '2000', '99', 74, NULL),
(1670, 'Carlos Ramírez', '1', '2000', '100', 74, NULL),
(1671, 'Ana Torres', '1', '2000', '101', 74, NULL),
(1672, 'Luis Gómez', '1', '2000', '101', 74, NULL),
(1673, 'Cesar', '1', '2000', '100', 74, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `participante_id` (`participante_id`);

--
-- Indices de la tabla `calificacion_old`
--
ALTER TABLE `calificacion_old`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `calificacion_ronda`
--
ALTER TABLE `calificacion_ronda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `participante_id` (`participante_id`);

--
-- Indices de la tabla `canciones`
--
ALTER TABLE `canciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `canciones_predeterminadas`
--
ALTER TABLE `canciones_predeterminadas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `club`
--
ALTER TABLE `club`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracion_lugares_unificada`
--
ALTER TABLE `configuracion_lugares_unificada`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `participante`
--
ALTER TABLE `participante`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificacion`
--
ALTER TABLE `calificacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `calificacion_old`
--
ALTER TABLE `calificacion_old`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `calificacion_ronda`
--
ALTER TABLE `calificacion_ronda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `canciones`
--
ALTER TABLE `canciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `canciones_predeterminadas`
--
ALTER TABLE `canciones_predeterminadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2043;

--
-- AUTO_INCREMENT de la tabla `club`
--
ALTER TABLE `club`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `configuracion_lugares_unificada`
--
ALTER TABLE `configuracion_lugares_unificada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `participante`
--
ALTER TABLE `participante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1674;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD CONSTRAINT `calificacion_ibfk_1` FOREIGN KEY (`participante_id`) REFERENCES `participante` (`id`);

--
-- Filtros para la tabla `calificacion_ronda`
--
ALTER TABLE `calificacion_ronda`
  ADD CONSTRAINT `calificacion_ronda_ibfk_1` FOREIGN KEY (`participante_id`) REFERENCES `participante` (`id`);

--
-- Filtros para la tabla `participante`
--
ALTER TABLE `participante`
  ADD CONSTRAINT `participante_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
