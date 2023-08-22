--
-- Database: `PM_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `Password_Reset`
--
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `Password_Reset` (
  `User_ID` int(8) NOT NULL,
  `Username` varchar(256) DEFAULT NULL,
  `Email` varchar(256) DEFAULT NULL,
  `Reset_Key` varchar(128) DEFAULT NULL,
  `ExpDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Password_Reset`
  ADD PRIMARY KEY (`User_ID`);

ALTER TABLE `Password_Reset`
  MODIFY `User_ID` int(8) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Password_Reset`
  ADD CONSTRAINT `Password_Reset_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `Credentials` (`User_ID`);
COMMIT;
