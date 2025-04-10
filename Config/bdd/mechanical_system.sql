-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-04-2025 a las 23:58:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mechanical_system`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `addresses`
--

CREATE TABLE `addresses` (
  `id` int(10) UNSIGNED NOT NULL,
  `number` varchar(20) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `addresses`
--

INSERT INTO `addresses` (`id`, `number`, `street`, `city`, `state`, `zip`, `created_at`, `updated_at`) VALUES
(1, '6225B', 'Alameda Avenue', 'El Paso', 'Texas', '79905', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
(2, '910', 'Highland Ave', 'National City', 'California', '91950', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
(3, '', '39744                                                                     Year                  2017', 'Nogales', 'Arizona', '85621', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(4, '', '8101251                                              Year            2011\nMfg In                    ', 'Nogales', 'Arizona', '85621', '2025-04-09 21:11:45', '2025-04-09 21:11:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificates`
--

CREATE TABLE `certificates` (
  `cert_number` varchar(20) NOT NULL,
  `vin` varchar(50) DEFAULT NULL,
  `address_id` int(10) UNSIGNED DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `mfg_in` varchar(100) DEFAULT NULL,
  `make` varchar(100) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `license_plate` varchar(50) DEFAULT NULL,
  `odometer` int(10) UNSIGNED DEFAULT NULL,
  `inspector_name` varchar(100) DEFAULT NULL,
  `test_date` date DEFAULT NULL,
  `expires` date DEFAULT NULL,
  `source_file` varchar(255) DEFAULT NULL,
  `zip_file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `certificates`
--

INSERT INTO `certificates` (`cert_number`, `vin`, `address_id`, `phone`, `year`, `mfg_in`, `make`, `owner_name`, `model`, `license_plate`, `odometer`, `inspector_name`, `test_date`, `expires`, `source_file`, `zip_file_path`, `created_at`, `updated_at`) VALUES
('MEN2-00000286', '1FTYR2CM6HKA39744', 3, '(619) 845-1299', 2017, 'UNITED ST', 'Ford', 'GABRIEL ARMANDO GOMEZ SAMPERIO', 'Transit                         License Plate           62 NONE                  Odometer', '62 NONE', 175530, '72 Francisco Leyva', '2024-12-23', NULL, 'MEN2-00000286.txt', 'uploads/certificates/MEN2-00000286.zip', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
('MEN2-00000309', '4GCHTDFE1B8101251', 4, '(619) 845-1299', 2011, 'UNITED ST', 'Chevrolet', 'IMPORTAUTOS G', 'Colorado                      License Plate           62 NONE                  Odometer', '62 NONE', 139005, '72 Francisco Leyva', '2024-12-26', NULL, 'MEN2-00000309.txt', 'uploads/certificates/MEN2-00000309.zip', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
('MEN3-00000429', '3N1CPSCU7KL499683', 1, '(619) 845-1299', 2019, 'MEXICO', 'Owner', 'JOSE ALFREDO REYES ESQUIVEL', 'License Plate          84NONE                 Odometer', '84NONE                 O', 49814, '77 Gabriela Alvalez', '2024-12-26', '2025-03-26', 'MEN3-00000429.txt', 'uploads/certificates/MEN3-00000429.zip', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
('MEN3-00002192', '1C6RR7YT4HS572930', 1, '(619) 845-1299', 2017, 'UNITED STATES', 'Owner', 'JOSE ALFREDO REYES ESQUIVEL', 'License Plate           84 NONE                  Odometer', '84 NONE', 112186, '77 Gabriela Alvarez', '2025-04-09', '2025-07-09', 'MEN3-00002192.txt', 'uploads/certificates/MEN3-00002192.zip', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
('MEN3-00002193', '3GKALPEX8KL392827', 1, '(619) 845-1299', 2019, 'MEXICO', 'Owner', 'JOSE ALFREDO REYES ESQUIVEL', 'License Plate      84 NONE          Odometer', '84 NONE', 135981, '77 Gabriela Alvarez', '2025-04-09', '2025-07-09', 'MEN3-00002193.txt', 'uploads/certificates/MEN3-00002193.zip', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
('MEN3-00002194', '2GNAXJEV1J6352305', 1, '(619) 845-1299', 2018, 'CANADA', 'Owner', 'JOSE ALFREDO REYES ESQUIVEL', 'License Plate       84 NONE           Odometer', '84 NONE', 125464, '77 Gabriela Alvarez', '2025-04-09', '2025-07-09', 'MEN3-00002194.txt', 'uploads/certificates/MEN3-00002194.zip', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
('MEN6-00000103', 'STFAZSCN6GX005154', 1, '(619) 845-1299', 2016, 'UNITED STATES', 'Owner', 'JOSE ALFREDO REYES ESQUIVEL', 'License Plate      84 NONE          Odometer', '84 NONE', 158379, '12 Clara Mier', '2025-04-09', '2025-07-09', 'MEN6-00000103.txt', 'uploads/certificates/MEN6-00000103.zip', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
('MET1-00005713', '4GKKNXLSOLZ117582', 1, '(619) 845-1299', 2020, 'UNITED STATES', 'GMC', 'AGENCIA ADUANAL INTERNACIONAL DE COMERCIO EXTERIOR LG', 'Acadia                        License Plate           K2 NONE                 Odometer', 'K2 NONE                 O', 76232, '03 Sara Corona', '2025-04-09', NULL, 'MET1-00005713.txt', 'uploads/certificates/MET1-00005713.zip', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
('MET2-00002495', '4GKKNXLSOLZ117582', 1, '(619) 845-1299', 2020, 'UNITED STATES', 'GMC', 'AGENCIA ADUANAL INTERNACIONAL DE COMERCIO EXTERIOR LG', 'Acadia                        License Plate           K2 NONE                 Odometer', 'K2 NONE                 O', 76232, '39 Ricardo Rodriguez', '2025-04-09', NULL, 'MET2-00002495.txt', 'uploads/certificates/MET2-00002495.zip', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
('MET5-00004169', '3GCPWEEDXLG426123', 1, '(619) 845-1299', 2020, 'MEXICO', 'Chevrolet', 'MIGUEL ANGEL LIRA GAYTAN', 'Silverado 1500                License Plate           K2 NONE                  Odometer', 'K2 NONE                  O', 99040, '20 Saul Corona', '2025-04-09', NULL, 'MET5-00004169.txt', 'uploads/certificates/MET5-00004169.zip', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
('MET5-00004170', '2GKALREK8F6437632', 1, '(619) 845-1299', 2015, 'CANADA', 'GMC', 'ALEJANDRA YARELI CANO IBARRA', 'Terrain                        License Plate           K4 NONE                 Odometer', 'K4 NONE                 O', 142608, '20 Saul Corona', '2025-04-09', NULL, 'MET5-00004170.txt', 'uploads/certificates/MET5-00004170.zip', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
('MET7-00002007', '4C4RDJDG6IC255146', 2, '(619) 845-1299', 2018, 'UNITED STATES', 'Dodge', 'FABST COMERCILIZADORA S', 'Durango                     License Plate           32 NONE                 Odometer', '32 NONE', 167672, '76 Juan M Wence', '2025-04-09', NULL, 'MET7-00002007.txt', 'uploads/certificates/MET7-00002007.zip', '2025-04-09 21:05:07', '2025-04-09 21:05:07'),
('MET8-00000954', NULL, 2, '(619) 845-1299', 2018, 'UNITED STATES', 'Nissan', 'NIC PRODUCTS MEXICO', 'Rogue                         License Plate           J2 NONE                   Odometer', 'J2 NONE                   O', 83140, '12 Christian Tovar', '2025-04-09', NULL, 'MET8-00000954.txt', 'uploads/certificates/MET8-00000954.zip', '2025-04-09 21:05:07', '2025-04-09 21:05:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `import_logs`
--

CREATE TABLE `import_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `records_imported` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `records_failed` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `imported_by` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('success','partial','failed') NOT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `import_logs`
--

INSERT INTO `import_logs` (`id`, `file_name`, `records_imported`, `records_failed`, `imported_by`, `status`, `error_message`, `created_at`) VALUES
(1, 'import_json_20250409222210', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:22:10'),
(2, 'import_json_20250409222527', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:25:27'),
(3, 'import_json_20250409222553', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:25:53'),
(4, 'import_json_20250409222626', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:26:26'),
(5, 'import_json_20250409222648', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:26:48'),
(6, 'import_json_20250409222855', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:28:55'),
(7, 'import_json_20250409222956', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:29:56'),
(8, 'import_json_20250409223632', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:36:32'),
(9, 'import_json_20250409223816', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:38:16'),
(10, 'import_json_20250409223824', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:38:24'),
(11, 'import_json_20250409223903', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:39:03'),
(12, 'import_json_20250409224719', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:47:19'),
(13, 'import_json_20250409224751', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:47:51'),
(14, 'import_json_20250409225534', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:55:34'),
(15, 'import_json_20250409225715', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 20:57:15'),
(16, 'import_json_20250409230042', 0, 10, NULL, 'partial', 'SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near \'? AND street = ? AND city = ? AND state = ? AND zip = ?\' at line 2 | ', '2025-04-09 21:00:42'),
(17, 'import_json_20250409230507', 10, 0, NULL, 'success', '', '2025-04-09 21:05:07'),
(18, 'import_json_20250409230620', 10, 0, NULL, 'success', '', '2025-04-09 21:06:20'),
(19, 'import_json_20250409231145', 13, 0, NULL, 'success', '', '2025-04-09 21:11:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monitoring_results`
--

CREATE TABLE `monitoring_results` (
  `id` int(10) UNSIGNED NOT NULL,
  `cert_number` varchar(20) NOT NULL,
  `monitor_type` varchar(50) NOT NULL,
  `result` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `monitoring_results`
--

INSERT INTO `monitoring_results` (`id`, `cert_number`, `monitor_type`, `result`, `created_at`, `updated_at`) VALUES
(45, 'MEN2-00000286', 'misfire_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(46, 'MEN2-00000286', 'comprehensive_monitor_catalyst', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(47, 'MEN2-00000286', 'catalyst_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(48, 'MEN2-00000286', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(49, 'MEN2-00000309', 'fuel_system_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(50, 'MEN2-00000309', 'catalyst_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(51, 'MEN2-00000309', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(52, 'MEN3-00000429', 'fuel_system_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(53, 'MEN3-00000429', 'o2_sensor_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(54, 'MEN3-00002192', 'fuel_system_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(55, 'MEN3-00002192', 'o2_sensor_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(56, 'MEN3-00002192', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(57, 'MEN3-00002193', 'fuel_system_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(58, 'MEN3-00002193', 'o2_sensor_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(59, 'MEN3-00002193', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(60, 'MEN3-00002194', 'fuel_system_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(61, 'MEN3-00002194', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(62, 'MET1-00005713', 'fuel_system_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(63, 'MET1-00005713', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(64, 'MET2-00002495', 'fuel_system_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(65, 'MET2-00002495', 'catalyst_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(66, 'MET2-00002495', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(67, 'MET5-00004169', 'fuel_system_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(68, 'MET5-00004169', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(69, 'MET5-00004170', 'catalyst_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(70, 'MET5-00004170', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(71, 'MET7-00002007', 'fuel_system_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(72, 'MET7-00002007', 'catalyst_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(73, 'MET7-00002007', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(74, 'MET8-00000954', 'fuel_system_monitor', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45'),
(75, 'MET8-00000954', 'overall_test_result', 'PASS', '2025-04-09 21:11:45', '2025-04-09 21:11:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administrador del sistema con acceso completo', '2025-04-09 20:03:53', '2025-04-09 20:03:53'),
(2, 'manager', 'Gerente con acceso a la mayoría de funciones', '2025-04-09 20:03:53', '2025-04-09 20:03:53'),
(3, 'capturista', 'Usuario para captura de datos', '2025-04-09 20:03:53', '2025-04-09 20:03:53'),
(4, 'viewer', 'Usuario con permisos de solo lectura', '2025-04-09 20:03:53', '2025-04-09 20:03:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `correo`, `clave`, `first_name`, `last_name`, `phone`, `active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'OscarAdmin', 'arzateoscar33@gmail.com', '$2y$10$2g7El3lblMTOatfeY09a3.1mmWZ14xuXTiO.BalFFRLuzCLSrFGFO', 'Oscar', 'Arzate', '6644913156', 1, NULL, '2025-04-09 20:08:18', '2025-04-09 20:08:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `perfil` int(11) DEFAULT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombres`, `apellidos`, `correo`, `clave`, `perfil`, `estado`) VALUES
(1, 'Oscar', 'Arzate', 'arzateoscar33@gmail.com', '$2y$10$K7TF5gPdl06ezGYKvCweq.1NUwbFc3o4JlPCsT6zpDdbqo2PdqLdu', NULL, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_addresses_state_city` (`state`,`city`);

--
-- Indices de la tabla `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`cert_number`),
  ADD KEY `idx_certificates_vin` (`vin`),
  ADD KEY `idx_certificates_owner` (`owner_name`),
  ADD KEY `idx_certificates_test_date` (`test_date`),
  ADD KEY `idx_certificates_address` (`address_id`);

--
-- Indices de la tabla `import_logs`
--
ALTER TABLE `import_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_imports_user_idx` (`imported_by`);

--
-- Indices de la tabla `monitoring_results`
--
ALTER TABLE `monitoring_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_monitoring_cert_type` (`cert_number`,`monitor_type`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`correo`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`);

--
-- Indices de la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `fk_user_roles_role_id_idx` (`role_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `import_logs`
--
ALTER TABLE `import_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `monitoring_results`
--
ALTER TABLE `monitoring_results`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `fk_certificates_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `import_logs`
--
ALTER TABLE `import_logs`
  ADD CONSTRAINT `fk_imports_user` FOREIGN KEY (`imported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `monitoring_results`
--
ALTER TABLE `monitoring_results`
  ADD CONSTRAINT `fk_monitoring_cert_number` FOREIGN KEY (`cert_number`) REFERENCES `certificates` (`cert_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_user_roles_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_roles_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
