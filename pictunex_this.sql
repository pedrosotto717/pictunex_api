-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-11-2020 a las 12:32:45
-- Versión del servidor: 10.1.37-MariaDB
-- Versión de PHP: 7.0.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pictunex`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `images`
--

CREATE TABLE `images` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'name Related to Image',
  `keywords` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `categories` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `nickname` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `src` text COLLATE utf8_spanish_ci NOT NULL,
  `CREATION_DATE` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Store the name, route and category of the images';

--
-- Volcado de datos para la tabla `images`
--

INSERT INTO `images` (`id`, `name`, `keywords`, `categories`, `nickname`, `src`, `CREATION_DATE`) VALUES
(1, '5', 'alce,bosque', 'animals,nature', 'pedrosotto717', '/server/public/img/5_1605265303_pictunex.jpg', '2020-11-13 07:01:43'),
(2, '1', 'animal, turpial', 'nature', 'jhon_0', '/server/public/img/1_1605265713_pictunex.jpg', '2020-11-13 07:08:34'),
(3, '2', 'lago,bosque', 'scenery', 'jhon_0', '/server/public/img/2_1605265753_pictunex.jpg', '2020-11-13 07:09:14'),
(4, '9', 'tortuga', 'animals', 'jhon_0', '/server/public/img/9_1605265790_pictunex.jpg', '2020-11-13 07:09:51'),
(5, 'bench', 'sillas', 'scenery,fantasy', 'jhon_0', '/server/public/img/bench_1605265860_pictunex.jpg', '2020-11-13 07:11:01'),
(6, 'canoa_en_el_atardecar', 'lago,pesca', 'nature,fantasy', 'jhon_0', '/server/public/img/canoa_en_el_atardecar_1605265896_pictunex.jpg', '2020-11-13 07:11:36'),
(7, 'cascada', 'bosque,agua', 'scenery,nature', 'jhon_0', '/server/public/img/cascada_1605265947_pictunex.jpg', '2020-11-13 07:12:27'),
(8, 'elecho', 'ramas,hojas', 'nature', 'jhon_0', '/server/public/img/elecho_1605265999_pictunex.jpg', '2020-11-13 07:13:20'),
(9, 'montana-lago', 'nieve,agua', 'nature', 'jhon_0', '/server/public/img/montana-lago_1605266247_pictunex.jpg', '2020-11-13 07:17:27'),
(10, 'quimica_1604276672_pictunex', 'quimica', 'science', 'jhon_0', '/server/public/img/quimica_1604276672_pictunex_1605266301_pictunex.jpg', '2020-11-13 07:18:21'),
(11, 'city', 'city,new york', 'architecture,industry ', 'pedrosotto717', '/server/public/img/city_1605266404_pictunex.jpg', '2020-11-13 07:20:04'),
(12, 'andrea', 'teacher', 'science', 'pedrosotto717', '/server/public/img/andrea_1605266439_pictunex.jpg', '2020-11-13 07:20:40'),
(13, 'astronauta', 'space,station', 'technology', 'pedrosotto717', '/server/public/img/astronauta_1605266517_pictunex.jpg', '2020-11-13 07:21:58'),
(14, 'bombilla', 'edison,luz,energia', 'technology,science', 'pedrosotto717', '/server/public/img/bombilla_1605266551_pictunex.jpg', '2020-11-13 07:22:31'),
(15, 'casa_solariega', 'casa,house,comunity', 'architecture', 'pedrosotto717', '/server/public/img/casa_solariega_1605266637_pictunex.jpg', '2020-11-13 07:23:57'),
(16, 'cientificos', 'science,investigation', 'technology,science', 'pedrosotto717', '/server/public/img/cientificos_1605266715_pictunex.jpg', '2020-11-13 07:25:16'),
(17, 'code', 'javaScript,programming', 'animals,scenery,nature,fantasy,technology,fashion,industry ', 'pedrosotto717', '/server/public/img/code_1605266848_pictunex.jpg', '2020-11-13 07:27:29'),
(18, 'colibri', 'bird,pexels', 'animals,fantasy', 'pedrosotto717', '/server/public/img/colibri_1605266892_pictunex.jpg', '2020-11-13 07:28:13'),
(19, 'estudios', 'work', 'technology', 'pedrosotto717', '/server/public/img/estudios_1605266924_pictunex.jpg', '2020-11-13 07:28:44'),
(20, 'globo', 'covid 19,coronavirus', 'technology,science', 'pedrosotto717', '/server/public/img/globo_1605266958_pictunex.jpg', '2020-11-13 07:29:19'),
(21, 'tendencia', 'chica,girl', 'fashion', 'pedrosotto717', '/server/public/img/tendencia_1605266990_pictunex.jpg', '2020-11-13 07:29:50'),
(22, 'paris', 'city,london,londres', 'architecture', 'pedrosotto717', '/server/public/img/paris_1605267017_pictunex.jpg', '2020-11-13 07:30:17'),
(23, 'perro', 'dog', 'animals', 'pedrosotto717', '/server/public/img/perro_1605267045_pictunex.jpg', '2020-11-13 07:30:45'),
(24, 'james', 'nubes,cielo', 'scenery,nature', 'pedrosotto717', '/server/public/img/james_1605267098_pictunex.jpg', '2020-11-13 07:31:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `first_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `last_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `username` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `user_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `ico` text COLLATE utf8_spanish_ci,
  `DATE_RECORD` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `password`, `user_id`, `ico`, `DATE_RECORD`) VALUES
(1, 'pedro', 'sotto', 'pedrosotto717', '$2y$10$EbLhJprYPPLBxToo.agG/enjN8cGNWEf903Psl76c/vCt/h1pqeka', 'cb1b8f49e92e4926513ccf41216ee00d20c7d6fe133f9e8908779e0de1c1667e', NULL, '2020-11-13 10:54:31'),
(2, 'jhon', 'Doe', 'jhon_0', '$2y$10$Tb75mpB9/gMioTffGHioSuk.VsGYj41oMeXGfROM.oPCamrZOXqU6', '8034ee13ab9720aad6acbaa1e05f26f94be71b7fc9d081ff06b99661012dbda3', NULL, '2020-11-13 11:02:36');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nickname` (`nickname`);
ALTER TABLE `images` ADD FULLTEXT KEY `search` (`name`,`keywords`,`categories`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `images`
--
ALTER TABLE `images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
