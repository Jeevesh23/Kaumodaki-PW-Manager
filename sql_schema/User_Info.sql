--
-- Database: `PM_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `User_Info`
--

CREATE TABLE `User_Info` (
  `User_ID` int(8) NOT NULL,
  `Description` varchar(512) DEFAULT NULL,
  `Link` varchar(512) DEFAULT NULL,
  `Password` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `User_Info`
--
ALTER TABLE `User_Info`
  ADD PRIMARY KEY (`User_ID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `User_Info`
--
ALTER TABLE `User_Info`
  ADD CONSTRAINT `User_Info_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `Credentials` (`User_ID`);
COMMIT;

