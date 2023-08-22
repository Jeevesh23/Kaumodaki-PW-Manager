--
-- Database: `PM_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `Credentials`
--
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `Credentials` (
  `User_ID` int(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Username` varchar(256),
  `Password` varchar(1024),
  `Salt` varchar(256),
  `Secret_Key` varchar(64),
  `IV` binary(16)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
