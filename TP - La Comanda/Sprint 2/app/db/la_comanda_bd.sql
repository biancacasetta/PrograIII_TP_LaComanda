-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2022 at 06:33 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `la_comanda_bd`
--

-- --------------------------------------------------------

--
-- Table structure for table `comandas`
--

CREATE TABLE `comandas` (
  `codigo` varchar(5) NOT NULL,
  `codigoMesa` int(5) NOT NULL,
  `nombreCliente` varchar(60) NOT NULL,
  `imagen` varchar(150) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `comandas`
--

INSERT INTO `comandas` (`codigo`, `codigoMesa`, `nombreCliente`, `imagen`, `cuenta`, `estado`) VALUES
('TQJE8', 10000, 'Agustina', 'Media/Comandas/TQJE8-10000-Agustina.jpg', NULL, 'Pendiente');

-- --------------------------------------------------------

--
-- Table structure for table `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `nombreApellido` varchar(200) NOT NULL,
  `perfil` varchar(50) NOT NULL,
  `esSocio` varchar(2) NOT NULL,
  `fechaAlta` date NOT NULL,
  `fechaBaja` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `empleados`
--

INSERT INTO `empleados` (`id`, `clave`, `nombreApellido`, `perfil`, `esSocio`, `fechaAlta`, `fechaBaja`) VALUES
(100000, '$2a$12$4umYIvV8XOnQkUnWTiC7P.NXvWtjqRvCnZKrUoVUz9yV4ZTTSdsFS', 'Bianca Casetta', 'Socio', 'Sí', '2022-11-26', NULL),
(100001, '$2y$10$yoaIHIir3mXo9WkKg5jrmuo4YrZYIUwLVpv7uN08LSsdCVItOJkDu', 'Valeria Montes', 'Bartender', 'No', '2022-11-26', NULL),
(100002, '$2y$10$ZGm92rzNR1jkxXpzK78jR.vpc.T00aUlYboNW604nQlEeA0h.DZ82', 'Facundo Torres', 'Cocinero', 'No', '2022-11-26', NULL),
(100003, '$2y$10$cFZDIj3o18KuuL8tgwp5cuWD2y66r3/Cc/f1uoeojUFO1RJPjbIcC', 'Mariano Pavone', 'Cervecero', 'No', '2022-11-26', NULL),
(100004, '$2y$10$2DDWPCd3yFO6bbEqjv0hnuh7wzp4DXqNUf8OdeagpA6hx8B/NOYLa', 'Julieta Verdi', 'Mozo', 'No', '2022-11-26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` int(11) NOT NULL,
  `perfil` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `nombre`, `precio`, `perfil`) VALUES
(100, 'Milanesa a caballo', 1300, 'Cocinero'),
(101, 'Hamburguesa de garbanzos', 1200, 'Cocinero'),
(102, 'Quilmes 340ml', 200, 'Cervecero'),
(103, 'Ravioles de espinaca', 1200, 'Cocinero'),
(104, 'Gin Tonic', 800, 'Bartender'),
(105, 'Daikiri', 1000, 'Bartender'),
(106, 'Corona', 400, 'Cervecero'),
(107, 'Negroni', 950, 'Bartender'),
(108, 'Tortilla de papa', 1000, 'Cocinero');

-- --------------------------------------------------------

--
-- Table structure for table `mesas`
--

CREATE TABLE `mesas` (
  `codigo` int(5) NOT NULL,
  `idEmpleado` int(10) DEFAULT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mesas`
--

INSERT INTO `mesas` (`codigo`, `idEmpleado`, `estado`) VALUES
(10000, 100004, 'Cerrada'),
(10001, NULL, 'Cerrada'),
(10002, NULL, 'Cerrada'),
(10003, NULL, 'Cerrada'),
(10004, NULL, 'Cerrada'),
(10005, NULL, 'Cerrada');

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `codigoComanda` varchar(5) NOT NULL,
  `idItem` int(11) NOT NULL,
  `idEmpleado` int(11) DEFAULT NULL,
  `estado` varchar(50) NOT NULL,
  `duracion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pedidos`
--

INSERT INTO `pedidos` (`id`, `codigoComanda`, `idItem`, `idEmpleado`, `estado`, `duracion`) VALUES
(1001, 'TQJE8', 100, NULL, 'Pendiente', NULL),
(1003, 'TQJE8', 106, NULL, 'Pendiente', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comandas`
--
ALTER TABLE `comandas`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `codigoMesa` (`codigoMesa`);

--
-- Indexes for table `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `idEmpleado` (`idEmpleado`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `codigoComanda` (`codigoComanda`),
  ADD KEY `idItem` (`idItem`),
  ADD KEY `idEmpleado` (`idEmpleado`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100005;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `mesas`
--
ALTER TABLE `mesas`
  MODIFY `codigo` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10006;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1004;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comandas`
--
ALTER TABLE `comandas`
  ADD CONSTRAINT `comandas_ibfk_1` FOREIGN KEY (`codigoMesa`) REFERENCES `mesas` (`codigo`),
  ADD CONSTRAINT `comandas_ibfk_2` FOREIGN KEY (`codigoMesa`) REFERENCES `mesas` (`codigo`);

--
-- Constraints for table `mesas`
--
ALTER TABLE `mesas`
  ADD CONSTRAINT `mesas_ibfk_1` FOREIGN KEY (`idEmpleado`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `mesas_ibfk_2` FOREIGN KEY (`idEmpleado`) REFERENCES `empleados` (`id`);

--
-- Constraints for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`codigoComanda`) REFERENCES `comandas` (`codigo`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`idItem`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`idEmpleado`) REFERENCES `empleados` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
