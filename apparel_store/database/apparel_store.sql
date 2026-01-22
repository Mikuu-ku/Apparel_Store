-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 08:43 AM
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
-- Database: `apparel_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(1, 2, 2, 2),
(2, 2, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('GCash','PayMaya','COD') DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `size`, `color`, `image`, `stock`, `category`) VALUES
(1, 'Essential Tee - Pure White', 'A clean, breathable cotton tee designed for everyday comfort. Its crisp Cloud White finish delivers a fresh, minimalist look that pairs effortlessly with any outfit.', 249.00, 'S, M, L, XL, XXL', 'White', 'white_plain.png', NULL, NULL),
(2, 'Signature Tee - Midnight Black', 'Crafted for a sharp and timeless style, this Midnight Black tee offers a smooth fit with all-day comfort. A must-have staple for a modern wardrobe.', 249.00, 'S, M, L, XL, XXL', 'Black', 'plain_black.jpg', 200, NULL),
(3, 'Core Fit Tee - Wine Maroon', 'Bold yet refined, the Wine Maroon Essential Tee adds depth and character to your casual wear. Soft fabric and a relaxed fit make it perfect for daily use.', 249.00, 'S, M, L, XL, XXL', 'Maroon', 'maroon_plain.png', 100, NULL),
(4, 'Everyday Tee - Ash Gray', 'Subtle and versatile, the Ash Gray Essential Tee blends comfort with understated style. Ideal for layering or wearing on its own.', 249.00, 'S, M, L, XL, XXL', 'Gray', 'grey_plain.png', 100, NULL),
(5, 'Essential Pants - Cloud White', 'Designed with a relaxed silhouette, these Cloud White pants offer comfort and a clean aesthetic. Perfect for casual days or elevated streetwear looks.', 399.00, 'S, M, L, XL, XXL', 'White', 'white_pants.jpg', 100, NULL),
(6, 'Essential Pants – Midnight Black', 'Sleek and versatile, the Midnight Black Essential Pants provide a modern fit with effortless style. Easy to pair with tees, hoodies, or sneakers.', 499.00, 'S, M, L, XL, XXL', 'Black', 'black_pants.jpg', NULL, NULL),
(7, 'Essential Pants – Wine Maroon', 'Stand out with confidence in the Wine Maroon Essential Pants. Soft, durable fabric ensures comfort while adding a bold touch to your outfit.', 499.00, 'S, M, L, XL, XXL', 'Maroon', 'maroon_pants.jpg', 129, NULL),
(8, 'Essential Pants – Ash Gray', 'Balanced and timeless, the Ash Gray Essential Pants are built for everyday movement. A reliable piece for both comfort and style.', 499.00, 'S, M, L, XL, XXL', 'Gray', 'gray_pants.jpg', 100, NULL),
(9, 'Essential Hoodie – Cloud White', 'This Cloud White hoodie delivers warmth and softness with a clean, modern finish. Perfect for layering or wearing on its own in cooler weather.', 1999.00, 'S, M, L, XL, XXL', 'White', 'white_hoodie.png', 100, NULL),
(10, 'Essential Hoodie – Midnight Black', 'A classic essential, the Midnight Black hoodie offers a relaxed fit and premium comfort. Designed for effortless streetwear appeal.', 1999.00, 'S, M, L, XL, XXL', 'Black', 'hoodie_black.jpg', NULL, NULL),
(11, 'Essential Hoodie – Wine Maroon', 'Rich in tone and comfort, the Wine Maroon Essential Hoodie adds warmth and personality to your look. Ideal for casual wear and everyday layering.', 1999.00, 'S, M, L, XL, XXL', 'Maroon', 'maroon_hoodie.jpg', 100, NULL),
(12, 'Essential Hoodie – Ash Gray', 'Minimal and versatile, the Ash Gray Essential Hoodie combines comfort with a neutral aesthetic. A go-to piece for any season.', 1999.00, 'S, M, L, XL, XXL', 'Gray', 'gray_hoodie.jpg', 1000, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `contact_no` int(11) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `contact_no`, `role`) VALUES
(2, 'Kashmir', 'Espinosa', 'kashmirespinosa@gmail.com', '$2y$10$r7o2KBPVtKvTdddWkVpoBOOgaOV/LPuYy6nx19cKGrPCtLuK204Kq', 2147483647, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
