-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 02.10.2021 klo 01:04
-- Palvelimen versio: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dinner_spinner`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `restaurants`
--

CREATE TABLE `restaurants` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL,
  `Color` varchar(50) COLLATE utf8mb4_swedish_ci NOT NULL,
  `Website` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL,
  `IsRestaurant` int(1) NOT NULL DEFAULT 0,
  `Status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Vedos taulusta `restaurants`
--

INSERT INTO `restaurants` (`ID`, `Name`, `Color`, `Website`, `IsRestaurant`, `Status`) VALUES
(1, 'Blancco', 'Grey', 'http://www.ravintolablancco.com/lounas-ravintolat/pasila/', 1, 0),
(2, 'Delicatessen', 'Green', 'https://delicatessen.fi/lounaslistat/lime-park/', 1, 1),
(3, 'Factory', 'Navy', 'https://ravintolafactory.com/lounasravintolat/ravintolat/helsinki-vallila/', 1, 1),
(4, 'Mala', 'Teal', 'https://mala.fi/noutolounas/', 1, 1),
(5, 'Mero-Himal', 'Tomato', 'https://merohimal.fi/lunch/', 1, 1),
(6, 'Picnic', 'Orange', 'https://www.picnic.fi/tuotteet', 1, 1),
(7, 'South China', 'Red', 'https://www.foodora.fi/restaurant/s8ik/south-china', 1, 1),
(8, 'Vaunu', 'Brown', 'https://jk-kitchen.fi/vaunu/', 1, 1),
(9, '91.1', 'Blue', 'http://www.ravintola911.fi/kumpulantien-lounaslista/', 1, 1),
(10, 'Kala', 'Blue', '', 0, 1),
(11, 'Pasta', 'Tomato', '', 0, 1),
(12, 'Sushi', 'Red', '', 0, 1),
(13, 'Salaatti', 'Green', '', 0, 1),
(14, 'Tikka masala', 'Orange', '', 0, 1),
(15, 'Burgeri', 'Burlywood', '', 0, 1),
(16, 'Joku Triplan ravintola', 'Purple', 'https://malloftripla.fi/kahvilat-ravintolat/', 1, 1),
(17, 'Kasvisvaihtoehto', 'Darkolivegreen', '', 0, 1),
(18, 'Pihvi', 'Brown', '', 0, 1),
(19, 'Pizza', 'Mediumvioletred', '', 0, 1),
(20, 'Kebab', 'Steelblue', '', 0, 1),
(21, 'Tortilla/Burrito', 'Teal', '', 0, 1),
(22, 'Kana', 'Crimson', '', 0, 1),
(23, 'Joku muu', 'Purple', '', 0, 1),
(24, 'Nuudeli', 'Goldenrod', '', 0, 1),
(25, 'Keitto', 'Royalblue', '', 0, 1),
(26, 'Lihavaihtoehto', 'Lightcoral', '', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
