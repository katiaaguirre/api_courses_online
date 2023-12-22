-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-12-2023 a las 07:13:25
-- Versión del servidor: 8.0.35
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda_cursos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `name` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `state` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 es activo y 2 es desactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `created_at`, `updated_at`, `deleted_at`, `name`, `image`, `category_id`, `state`) VALUES
(55, '2023-12-20 22:36:06', '2023-12-20 22:38:08', NULL, 'Orquesta', 'categories/Ph8Z29pXz9mRbcrV2aV7WpbRiNwGN6AP3TfSpfcr.webp', NULL, 1),
(56, '2023-12-20 22:37:23', '2023-12-20 22:37:23', NULL, 'Salsa', NULL, 55, 1),
(57, '2023-12-20 22:37:40', '2023-12-20 22:37:40', NULL, 'Cumbia', NULL, 55, 1),
(58, '2023-12-21 05:10:59', '2023-12-21 05:12:18', NULL, 'Metales', 'categories/RDRCuH92gjHyvt01z94hKQW2P5EHQabdJw0FmC5D.webp', NULL, 1),
(59, '2023-12-21 05:11:37', '2023-12-21 11:34:54', '2023-12-21 11:34:54', 'Saxofón', NULL, 58, 1),
(60, '2023-12-21 05:11:49', '2023-12-21 11:33:52', '2023-12-21 11:33:52', 'Tuba', NULL, 58, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
