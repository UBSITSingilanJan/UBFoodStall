-- UB Food Stall database
-- Import this file in phpMyAdmin (it creates the database and replaces old tables).
-- Test accounts (password for all: 123)
--   customer: janellesambahon@gmail.com, marcsoledad@gmail.com, chesneytecla@gmail.com
--   vendor:   cristinaperido@gmail.com
--   admin:    admin@gmail.com / admin123

CREATE DATABASE IF NOT EXISTS `ubfoodstall` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ubfoodstall`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `review`;
DROP TABLE IF EXISTS `product`;
DROP TABLE IF EXISTS `food_stall`;
DROP TABLE IF EXISTS `vendor`;
DROP TABLE IF EXISTS `customer`;
SET FOREIGN_KEY_CHECKS = 1;

-- Passwords are stored as hashes (password_hash), so the column must be long.
CREATE TABLE `customer` (
  `Customer_ID` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` enum('BSIT','SON','SOE','Others') NOT NULL,
  `year_level` tinyint(1) NOT NULL,
  `user_type` enum('Student','Instructor','Staff') NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(8) NOT NULL,
  PRIMARY KEY (`Customer_ID`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `vendor` (
  `Vendor_ID` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(6) NOT NULL,
  PRIMARY KEY (`Vendor_ID`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `food_stall` (
  `Stall_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Vendor_ID` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `status` enum('Open','Closed') NOT NULL DEFAULT 'Open',
  `stallname` varchar(50) NOT NULL,
  PRIMARY KEY (`Stall_ID`),
  KEY `Vendor_ID` (`Vendor_ID`),
  CONSTRAINT `food_stall_ibfk_1` FOREIGN KEY (`Vendor_ID`) REFERENCES `vendor` (`Vendor_ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `product` (
  `Product_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Stall_ID` int(11) NOT NULL,
  `productname` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `status` enum('Available','Not Available') NOT NULL DEFAULT 'Available',
  PRIMARY KEY (`Product_ID`),
  KEY `Stall_ID` (`Stall_ID`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`Stall_ID`) REFERENCES `food_stall` (`Stall_ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `review` (
  `Review_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Customer_ID` int(11) DEFAULT NULL,
  `Stall_ID` int(11) NOT NULL,
  `Vendor_ID` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `reviews` varchar(1000) NOT NULL,
  `reviewDate` date NOT NULL,
  PRIMARY KEY (`Review_ID`),
  KEY `Customer_ID` (`Customer_ID`),
  KEY `Stall_ID` (`Stall_ID`),
  KEY `Vendor_ID` (`Vendor_ID`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON UPDATE CASCADE,
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`Stall_ID`) REFERENCES `food_stall` (`Stall_ID`) ON UPDATE CASCADE,
  CONSTRAINT `review_ibfk_3` FOREIGN KEY (`Vendor_ID`) REFERENCES `vendor` (`Vendor_ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data
INSERT INTO `customer` (`Customer_ID`, `firstname`, `lastname`, `email`, `department`, `year_level`, `user_type`, `password`, `role`) VALUES
(8, 'Janelle', 'Sambahon', 'janellesambahon@gmail.com', 'BSIT', 3, 'Student', '$2y$10$nPrmaNLiGL3XvkolkzLvnOD.KvuVprcq0OlAoSLV4QMTszzm5UrjW', 'Customer'),
(9, 'Marc', 'Soledad', 'marcsoledad@gmail.com', 'BSIT', 3, 'Student', '$2y$10$nPrmaNLiGL3XvkolkzLvnOD.KvuVprcq0OlAoSLV4QMTszzm5UrjW', 'Customer'),
(10, 'Chesney', 'Tecla', 'chesneytecla@gmail.com', 'SON', 3, 'Instructor', '$2y$10$nPrmaNLiGL3XvkolkzLvnOD.KvuVprcq0OlAoSLV4QMTszzm5UrjW', 'Customer');

INSERT INTO `vendor` (`Vendor_ID`, `firstname`, `lastname`, `contact`, `email`, `password`, `role`) VALUES
(9, 'Maria', 'Perido', '09876754324', 'cristinaperido@gmail.com', '$2y$10$nPrmaNLiGL3XvkolkzLvnOD.KvuVprcq0OlAoSLV4QMTszzm5UrjW', 'Vendor');

INSERT INTO `food_stall` (`Stall_ID`, `Vendor_ID`, `location`, `status`, `stallname`) VALUES
(3, 9, 'Building A 1st Floor', 'Open', 'Chicken Masarap');

INSERT INTO `product` (`Stall_ID`, `productname`, `price`, `stock`, `status`) VALUES
(3, 'Fried Chicken (1pc)', 45.00, 30, 'Available'),
(3, 'Chicken Wings (3pcs)', 60.00, 8, 'Available'),
(3, 'Java Rice', 15.00, 50, 'Available'),
(3, 'Iced Tea', 20.00, 0, 'Not Available');
