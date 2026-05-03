-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 01:43 PM
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
  `quantity` int(11) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `size`) VALUES
(14, 6, 11, 3, 'S'),
(35, 11, 10, 10, 'S'),
(36, 11, 10, 1, 'M'),
(37, 13, 10, 10, 'S');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `full_name`, `address`, `total_amount`, `payment_method`, `status`, `created_at`, `completed_at`) VALUES
(10, 2, 'Kashmir Espinosa', 'marigondon | Phone: 09333993708', 2049.00, 'COD', 'Completed', '2026-01-26 12:34:16', NULL),
(11, 8, 'Kashmir Espinosa', 'asdas | Phone: 09123456789', 1295.00, 'COD', 'Completed', '2026-01-27 03:39:29', NULL),
(12, 2, 'Kashmir Espinosa', 'dsdadada | Phone: 09872342344', 20040.00, 'GCASH (Ref: 2131)', 'Completed', '2026-01-27 04:21:53', NULL),
(13, 8, 'Kashmir Espinosa', 'asdasdsadsadasd | Phone: 09123456789', 20040.00, 'COD', 'Pending', '2026-02-08 11:31:37', NULL),
(14, 8, 'Kashmir Espinosa', '232323sqd | Phone: 09123456789', 20040.00, 'COD', 'Pending', '2026-02-08 11:33:43', NULL),
(15, 8, 'Kashmir Espinosa', 'asdsadsa | Phone: 09123456789', 20040.00, 'COD', 'Completed', '2026-02-08 11:35:24', NULL),
(16, 8, 'Kashmir Espinosa', 'Marigondon Crossing', 20000.00, 'GCASH (Ref: 1242)', 'Shipped', '2026-03-22 15:38:50', NULL),
(19, 8, 'Kashmir Espinosa', 'qweqweqweqewqeqweqweqwe', 2049.00, 'COD', 'Cancelled', '2026-03-31 11:58:21', NULL),
(20, 8, 'Kashmir Espinosa', 'Marigondon Crossing', 299.00, 'COD', 'Completed', '2026-04-07 06:41:04', '2026-05-03 18:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `size`, `price`) VALUES
(1, 10, 10, 1, 'XXL', 1999.00),
(2, 11, 2, 5, 'L', 249.00),
(3, 12, 11, 10, 'L', 1999.00),
(4, 13, 11, 10, 'XXL', 1999.00),
(5, 14, 11, 10, 'XXL', 1999.00),
(6, 15, 11, 10, 'L', 1999.00),
(7, 16, 11, 10, 'XXL', 1999.00),
(8, 19, 11, 1, 'S', 1999.00),
(9, 20, 1, 1, 'S', 249.00);

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
  `category` varchar(50) DEFAULT NULL,
  `stock_s` int(11) DEFAULT 0,
  `stock_m` int(11) DEFAULT 0,
  `stock_l` int(11) DEFAULT 0,
  `stock_xl` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `size`, `color`, `image`, `category`, `stock_s`, `stock_m`, `stock_l`, `stock_xl`) VALUES
(1, 'Essential Tee - Pure White', 'A clean, breathable cotton tee designed for everyday comfort. Its crisp Cloud White finish delivers a fresh, minimalist look that pairs effortlessly with any outfit.', 249.00, 'S, M, L, XL, XXL', 'White', 'white_tee.jpg', 'Tshirts', 97, 58, 0, 29),
(2, 'Signature Tee - Midnight Black', 'Crafted for a sharp and timeless style, this Midnight Black tee offers a smooth fit with all-day comfort. A must-have staple for a modern wardrobe.', 249.00, 'S, M, L, XL, XXL', 'Black', 'black_tee.jpg', 'Tshirts', 0, 43, 0, 0),
(3, 'Core Fit Tee - Wine Maroon', 'Bold yet refined, the Wine Maroon Essential Tee adds depth and character to your casual wear. Soft fabric and a relaxed fit make it perfect for daily use.', 249.00, 'S, M, L, XL, XXL', 'Maroon', 'maroon_tee.jpg', 'Tshirts', 0, 44, 11, 23),
(4, 'Everyday Tee - Ash Gray', 'Subtle and versatile, the Ash Gray Essential Tee blends comfort with understated style. Ideal for layering or wearing on its own.', 249.00, 'S, M, L, XL, XXL', 'Gray', 'gray_tee.jpg', 'Tshirts', 34, 65, 145, 213),
(5, 'Essential Pants - Cloud White', 'Designed with a relaxed silhouette, these Cloud White pants offer comfort and a clean aesthetic. Perfect for casual days or elevated streetwear looks.', 399.00, 'S, M, L, XL, XXL', 'White', 'white_pants.jpg', 'Bottoms', 0, 0, 0, 0),
(6, 'Essential Pants – Midnight Black', 'Sleek and versatile, the Midnight Black Essential Pants provide a modern fit with effortless style. Easy to pair with tees, hoodies, or sneakers.', 499.00, 'S, M, L, XL, XXL', 'Black', 'black_pants.jpg', 'Bottoms', 123, 435, 34, 23),
(7, 'Essential Pants – Wine Maroon', 'Stand out with confidence in the Wine Maroon Essential Pants. Soft, durable fabric ensures comfort while adding a bold touch to your outfit.', 499.00, 'S, M, L, XL, XXL', 'Maroon', 'maroon_pants.jpg', 'Bottoms', 54, 123, 143, 13),
(8, 'Essential Pants – Ash Gray', 'Balanced and timeless, the Ash Gray Essential Pants are built for everyday movement. A reliable piece for both comfort and style.', 499.00, 'S, M, L, XL, XXL', 'Gray', 'gray_pants.jpg', 'Bottoms', 0, 0, 0, 0),
(9, 'Essential Hoodie – Cloud White', 'This Cloud White hoodie delivers warmth and softness with a clean, modern finish. Perfect for layering or wearing on its own in cooler weather.', 1999.00, 'S, M, L, XL, XXL', 'White', 'white_hoodie.jpg', 'Hoodies', 0, 0, 0, 0),
(10, 'Essential Hoodie – Midnight Black', 'A classic essential, the Midnight Black hoodie offers a relaxed fit and premium comfort. Designed for effortless streetwear appeal.', 1999.00, 'S, M, L, XL, XXL', 'Black', 'black_hoodie.jpg', 'Hoodies', 134, 16, 33, 32),
(11, 'Essential Hoodie – Wine Maroon', 'Rich in tone and comfort, the Wine Maroon Essential Hoodie adds warmth and personality to your look. Ideal for casual wear and everyday layering.', 1999.00, 'S, M, L, XL, XXL', 'Maroon', 'maroon_hoodie.jpg', 'Hoodies', 112, 90, 461, 324),
(12, 'Essential Hoodie – Ash Gray', 'Minimal and versatile, the Ash Gray Essential Hoodie combines comfort with a neutral aesthetic. A go-to piece for any season.', 1999.00, 'S, M, L, XL, XXL', 'Gray', 'gray_hoodie.jpg', 'Hoodies', 0, 0, 0, 0),
(16, 'kash', 'yow', 4234324.00, NULL, 'Grey', 'IMG-69f72f7185ef68.40958256.png', 'TSHIRTS', 1, 1, 1, 1);

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
  `contact_no` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `profile_pic` varchar(255) DEFAULT 'default_avatar.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `contact_no`, `role`, `profile_pic`) VALUES
(13, 'kashmir', 'espinosa', 'kashmirespinosa@gmail.com', '$2y$10$wxAs70F7s5.SSQ2HpdPu5eQUfllen8US.RClQYQh.7VJEIv1u7BZi', '', 'user', 'user_13_1777808452.jpg'),
(14, 'admin', 'apparel', 'admin@apparel.com', '$2y$10$6qLj1xdLV3L98Nwm7e9Vq.WSjdvxc9Re/rSpad3NaQ2zC2PcI5BgS', NULL, 'admin', 'default_avatar.jpg');

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
