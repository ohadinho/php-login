-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2016 at 12:08 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `storedb`
--
CREATE DATABASE IF NOT EXISTS `storedb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `storedb`;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `check_user_login` (IN `username` VARCHAR(50), IN `password` VARCHAR(50))  BEGIN
SELECT 1
FROM user_access
WHERE UserName=`username` AND `Password`=`password`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_city` (IN `name` VARCHAR(50))  NO SQL
INSERT INTO `city`(Name)
VALUES(name)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_user` (IN `firstname` VARCHAR(50), IN `lastname` VARCHAR(50), IN `address` VARCHAR(50), IN `number` INT(11), IN `cityname` VARCHAR(50), IN `phone` VARCHAR(50), IN `username` VARCHAR(50), IN `logintoken` MEDIUMTEXT, IN `loginhash` MEDIUMTEXT)  BEGIN

SELECT 1 INTO @isExist
FROM `user_access`
WHERE `user_access`.`UserName` = username;

IF @isExist IS NULL THEN

SELECT ID INTO @cityid
FROM city
WHERE `Name` = cityname
LIMIT 1;

INSERT INTO `user`(FirstName,LastName,Address,Number,CityID,Phone)
VALUES (firstname,lastname,address,number,@cityid,phone);

SELECT LAST_INSERT_ID() INTO @UserID;

INSERT INTO `user_access`(UserID,UserName,LoginToken,LoginHash,CookieToken)
VALUES (@userid,username,logintoken,loginhash,NULL);

SELECT @UserID AS UserID;

ELSE 

     SELECT -1 AS UserID;
     
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `select_city` ()  NO SQL
SELECT ID,Name
FROM city$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `select_user` (IN `UserName` VARCHAR(50))  NO SQL
SELECT ua.UserID, ua.LoginHash, ua.LoginToken, ua.UserName, ua.CookieToken,
us.FirstName, us.LastName, us.Number, us.Phone, c.Name AS CityName
FROM `user_access` AS ua
INNER JOIN `user` AS us
ON ua.UserID = us.ID
INNER JOIN `city` AS c
ON us.CityID = c.ID
WHERE ua.UserName = UserName$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_user_cookietoken` (IN `username` VARCHAR(50), IN `cookietoken` MEDIUMTEXT)  NO SQL
UPDATE `user_access`
SET `user_access`.`CookieToken` = cookietoken
WHERE `user_access`.`UserName` = username$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Phone` varchar(50) NOT NULL,
  `CityID` int(11) NOT NULL,
  `Address` varchar(50) NOT NULL,
  `Number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_access`
--

CREATE TABLE `user_access` (
  `ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `CookieToken` mediumtext,
  `LoginToken` mediumtext,
  `LoginHash` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `CityID` (`CityID`);

--
-- Indexes for table `user_access`
--
ALTER TABLE `user_access`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserID` (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `user_access`
--
ALTER TABLE `user_access`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`CityID`) REFERENCES `city` (`ID`);

--
-- Constraints for table `user_access`
--
ALTER TABLE `user_access`
  ADD CONSTRAINT `user_access_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
