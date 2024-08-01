-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2024 at 10:50 AM
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
-- Database: `restaurant3`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `type`) VALUES
(1, 'food'),
(3, 'ของหวาน');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `emp_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `location` text NOT NULL,
  `role` int(1) NOT NULL COMMENT '1=Owner\r\n2=Cashier\r\n3=Receptionist\r\n4=Kitchen',
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`emp_id`, `username`, `password`, `firstname`, `lastname`, `mail`, `location`, `role`, `status`) VALUES
(1, 'mgp02', '123', 'samdosaodkosa', 'sadsadsa', 'sadsadsad@gmail.com', 'sadasd', 1, 1),
(2, 'mgp01', '$2y$10$mCkCCksLTbMc8FvAPs7c1OW.UBFp3lMhMeIKoUSo0K9dSJW8umAFy', 'สัจจกร', 'ศิวธนภูวดล', 'pondza1087@gmail.com', 'กหฟหฟกหฟกฟหกหฟก', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `menuitems`
--

CREATE TABLE `menuitems` (
  `item_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` int(1) NOT NULL COMMENT '1=available\r\n2=unavailable',
  `unit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitems`
--

INSERT INTO `menuitems` (`item_id`, `category_id`, `name`, `description`, `price`, `image_path`, `status`, `unit_id`) VALUES
(1, 1, 'moo mixed', 'sadasd', 123.00, 'uploads/moo.jpg', 0, 1),
(2, 3, 'SALAD', 'SADSAD', 100.00, 'uploads/132_1.jpg', 0, 4),
(3, 1, 'sadasd', 'sAa', 1234.00, 'uploads/r1rxry2utuUA6UBR1JR-o.jpg', 0, 1),
(4, 1, 'saSasAassadsad', 'sadasdsad', 200.00, 'uploads/7851e4fa502fd4e7b36c63357ac5db68.jpg', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `order_details_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(1) NOT NULL COMMENT '1=กิโลกรัม\r\n2=กรัม\r\n3=ชิ้น\r\n4=แพ็ค',
  `status` int(1) NOT NULL COMMENT '1=available\r\n2=outoff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `order_status` int(1) NOT NULL COMMENT '1=paid\r\n2=not paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_time` datetime NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` int(11) NOT NULL COMMENT '1=Paid\r\n2=pending\r\n3=cancelled',
  `promotion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `promotion_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `discount` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `table_id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `table_status` int(1) NOT NULL COMMENT '1=available\r\n2=unavailable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`table_id`, `table_number`, `table_status`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 1),
(4, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `unit_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unit`
--

INSERT INTO `unit` (`unit_id`, `name`) VALUES
(1, 'กิโลกรัม'),
(2, 'กรัม'),
(3, 'ชิ้น'),
(4, 'แพ็ค');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`order_details_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `promotion_id` (`promotion_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promotion_id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`unit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menuitems`
--
ALTER TABLE `menuitems`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD CONSTRAINT `menuitems_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `menuitems_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`unit_id`);

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menuitems` (`item_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `tables` (`table_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`promotion_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
