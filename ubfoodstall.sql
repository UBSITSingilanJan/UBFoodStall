-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2026 at 05:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ubfoodstall`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `Customer_ID` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` enum('BSIT','SON','SOE','Pogi Ako') NOT NULL,
  `year_level` tinyint(1) NOT NULL,
  `user_type` enum('Student','Instructor','Staff','') NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`Customer_ID`, `firstname`, `lastname`, `email`, `department`, `year_level`, `user_type`, `password`, `role`) VALUES
(8, 'Janelle', 'Sambahon', 'janellesambahon@gmail.com', 'BSIT', 3, 'Student', '123', 'Customer'),
(9, 'Marc', 'Soledad', 'marcsoledad@gmail.com', 'BSIT', 3, 'Student', '123', 'Customer'),
(10, 'Chesney', 'Tecla', 'chesneytecla@gmail.com', 'SON', 3, 'Instructor', '123', 'Customer');

-- --------------------------------------------------------

--
-- Table structure for table `food_stall`
--

CREATE TABLE `food_stall` (
  `Stall_ID` int(11) NOT NULL,
  `Vendor_ID` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `status` enum('Open','Closed','','') NOT NULL,
  `stallname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_stall`
--

INSERT INTO `food_stall` (`Stall_ID`, `Vendor_ID`, `location`, `status`, `stallname`) VALUES
(3, 9, 'Building A 1st Floor', 'Open', 'Chicken Masarap');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `Product_ID` int(11) NOT NULL,
  `Stall_ID` int(11) NOT NULL,
  `productname` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Available','Not Available','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `Review_ID` int(11) NOT NULL,
  `Customer_ID` int(11) DEFAULT NULL,
  `Stall_ID` int(11) NOT NULL,
  `Vendor_ID` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `reviews` varchar(1000) NOT NULL,
  `reviewDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `Vendor_ID` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`Vendor_ID`, `firstname`, `lastname`, `contact`, `email`, `password`, `role`) VALUES
(9, 'Maria', 'Perido', '09876754324', 'cristinaperido@gmail.com', '123', 'Vendor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`Customer_ID`);

--
-- Indexes for table `food_stall`
--
ALTER TABLE `food_stall`
  ADD PRIMARY KEY (`Stall_ID`),
  ADD KEY `Vendor_ID` (`Vendor_ID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`Product_ID`),
  ADD KEY `Stall_ID` (`Stall_ID`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`Review_ID`),
  ADD KEY `Customer_ID` (`Customer_ID`,`Stall_ID`,`Vendor_ID`),
  ADD KEY `Stall_ID` (`Stall_ID`),
  ADD KEY `Vendor_ID` (`Vendor_ID`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`Vendor_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `Customer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `food_stall`
--
ALTER TABLE `food_stall`
  MODIFY `Stall_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `Product_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `Review_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `Vendor_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `food_stall`
--
ALTER TABLE `food_stall`
  ADD CONSTRAINT `food_stall_ibfk_1` FOREIGN KEY (`Vendor_ID`) REFERENCES `vendor` (`Vendor_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`Stall_ID`) REFERENCES `food_stall` (`Stall_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`Stall_ID`) REFERENCES `food_stall` (`Stall_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_3` FOREIGN KEY (`Vendor_ID`) REFERENCES `vendor` (`Vendor_ID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
