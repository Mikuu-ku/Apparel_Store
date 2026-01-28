-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2026 at 03:38 PM
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
(14, 6, 11, 3, 'S');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `full_name`, `address`, `total_amount`, `payment_method`, `status`, `created_at`) VALUES
(1, 2, 'Kashmir Espinosa', 'Marigondon, Crossing, LLC', 1296.00, 'COD', 'Shipped', '2026-01-20 14:59:02'),
(2, 2, 'Kashmir', 'skina earth', 20489.00, 'COD', 'Pending', '2026-01-25 10:48:59'),
(3, 7, 'josh', 'mactan', 3998.00, 'COD', 'Pending', '2026-01-26 04:20:35'),
(4, 8, 'Kashmir', 'skina earth', 19990.00, 'COD', 'Pending', '2026-01-26 11:26:42'),
(5, 8, 'Kashmir', 'dsadsad', 39980.00, 'COD', 'Pending', '2026-01-26 11:31:57'),
(6, 8, 'Kashmir', 'dsadsa', 39980.00, 'COD', 'Pending', '2026-01-26 11:34:14'),
(7, 8, 'Kashmir', 'dsada', 39980.00, 'COD', 'Pending', '2026-01-26 11:35:45'),
(8, 8, 'Kashmir', 'dsadad', 3998.00, 'COD', 'Pending', '2026-01-26 11:37:17'),
(9, 8, 'Kashmir Espinosa', 'dsadsadsa | Phone: 09123456789', 2049.00, 'COD', 'Pending', '2026-01-26 12:06:39'),
(10, 2, 'Kashmir Espinosa', 'marigondon | Phone: 09333993708', 2049.00, 'COD', 'Pending', '2026-01-26 12:34:16'),
(11, 8, 'Kashmir Espinosa', 'asdas | Phone: 09123456789', 1295.00, 'COD', 'Pending', '2026-01-27 03:39:29'),
(12, 2, 'Kashmir Espinosa', 'dsdadada | Phone: 09872342344', 20040.00, 'GCASH (Ref: 2131)', 'Pending', '2026-01-27 04:21:53');

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

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 10, 10, 1, NULL),
(2, 11, 2, 5, NULL),
(3, 12, 11, 10, NULL);

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
(1, 'Essential Tee - Pure White', 'A clean, breathable cotton tee designed for everyday comfort. Its crisp Cloud White finish delivers a fresh, minimalist look that pairs effortlessly with any outfit.', 249.00, 'S, M, L, XL, XXL', 'White', 'white_tee.jpg', 'Tops', 98, 58, 0, 29),
(2, 'Signature Tee - Midnight Black', 'Crafted for a sharp and timeless style, this Midnight Black tee offers a smooth fit with all-day comfort. A must-have staple for a modern wardrobe.', 249.00, 'S, M, L, XL, XXL', 'Black', 'black_tee.jpg', 'Tops', 0, 43, 0, 0),
(3, 'Core Fit Tee - Wine Maroon', 'Bold yet refined, the Wine Maroon Essential Tee adds depth and character to your casual wear. Soft fabric and a relaxed fit make it perfect for daily use.', 249.00, 'S, M, L, XL, XXL', 'Maroon', 'maroon_tee.jpg', 'Tops', 0, 44, 11, 23),
(4, 'Everyday Tee - Ash Gray', 'Subtle and versatile, the Ash Gray Essential Tee blends comfort with understated style. Ideal for layering or wearing on its own.', 249.00, 'S, M, L, XL, XXL', 'Gray', 'gray_tee.jpg', 'Tops', 34, 65, 145, 213),
(5, 'Essential Pants - Cloud White', 'Designed with a relaxed silhouette, these Cloud White pants offer comfort and a clean aesthetic. Perfect for casual days or elevated streetwear looks.', 399.00, 'S, M, L, XL, XXL', 'White', 'white_pants.jpg', 'Bottoms', 0, 0, 0, 0),
(6, 'Essential Pants – Midnight Black', 'Sleek and versatile, the Midnight Black Essential Pants provide a modern fit with effortless style. Easy to pair with tees, hoodies, or sneakers.', 499.00, 'S, M, L, XL, XXL', 'Black', 'black_pants.jpg', 'Bottoms', 123, 435, 34, 23),
(7, 'Essential Pants – Wine Maroon', 'Stand out with confidence in the Wine Maroon Essential Pants. Soft, durable fabric ensures comfort while adding a bold touch to your outfit.', 499.00, 'S, M, L, XL, XXL', 'Maroon', 'maroon_pants.jpg', 'Bottoms', 54, 123, 143, 13),
(8, 'Essential Pants – Ash Gray', 'Balanced and timeless, the Ash Gray Essential Pants are built for everyday movement. A reliable piece for both comfort and style.', 499.00, 'S, M, L, XL, XXL', 'Gray', 'gray_pants.jpg', 'Bottoms', 0, 0, 0, 0),
(9, 'Essential Hoodie – Cloud White', 'This Cloud White hoodie delivers warmth and softness with a clean, modern finish. Perfect for layering or wearing on its own in cooler weather.', 1999.00, 'S, M, L, XL, XXL', 'White', 'white_hoodie.jpg', 'Essentials', 0, 0, 0, 0),
(10, 'Essential Hoodie – Midnight Black', 'A classic essential, the Midnight Black hoodie offers a relaxed fit and premium comfort. Designed for effortless streetwear appeal.', 1999.00, 'S, M, L, XL, XXL', 'Black', 'black_hoodie.jpg', 'Essentials', 134, 16, 33, 32),
(11, 'Essential Hoodie – Wine Maroon', 'Rich in tone and comfort, the Wine Maroon Essential Hoodie adds warmth and personality to your look. Ideal for casual wear and everyday layering.', 1999.00, 'S, M, L, XL, XXL', 'Maroon', 'maroon_hoodie.jpg', 'Essentials', 113, 90, 481, 324),
(12, 'Essential Hoodie – Ash Gray', 'Minimal and versatile, the Ash Gray Essential Hoodie combines comfort with a neutral aesthetic. A go-to piece for any season.', 1999.00, 'S, M, L, XL, XXL', 'Gray', 'gray_hoodie.jpg', 'Essentials', 0, 0, 0, 0);

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
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `contact_no`, `role`) VALUES
(2, 'Kashmir', 'Espinosa', 'kaontae@gmail.com', '$2y$10$r7o2KBPVtKvTdddWkVpoBOOgaOV/LPuYy6nx19cKGrPCtLuK204Kq', '09333993708', 'user'),
(3, 'admin', 'apparel', 'admin@gmail.com', '$2y$10$3jNHoVe5jvK3OA2r/aMGbuxelyNe2MCW8g3uYHRYdcTvAJOZjj6ke', '2147483647', 'admin'),
(4, 'Jo', 'Estiola', 'jo@gmail.com', '$2y$10$7Q072oHcUaPK3Si34XR24uqY/piqGa5BV0qCG8oRdT4x1IN.8hWGq', '2147483647', 'user'),
(5, 'lynn', 'albarida', 'lynn', '$2y$10$rAxV3UE7O/ivr9aEqZ4EpuZ/nNhaE.wbqyusY.Hf3ZlZqtw68y4Yi', '2147483647', 'user'),
(6, 'lynn', 'albarida', 'lynn@gmail.com', '$2y$10$MOlFjhItGbaBjqBFxPsveOiLLwz5R6p7XRUl1tjuflHMAadwV64hu', '2147483647', 'user'),
(7, 'josh', 'semense', 'josh@gmail.com', '$2y$10$D3lMw5gsCSmG.mju3OI6DuyuM3azNApnq3YAj7aU8KDlkqmnOmkq2', '09123234354', 'user'),
(8, 'Kashmir', 'Espinosa', 'kashmir@gmail.com', '$2y$10$rh3ndtWdVxJir54azlVbO.sGhrnV9huC3p87uuQj7Tl5y3JwRyOOq', '09123432432', 'user');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
