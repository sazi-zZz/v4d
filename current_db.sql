-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 09, 2026 at 10:13 AM
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
-- Database: `v4d`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$w2CEPShavDbxmpLEtyMKLuXMMkvz8hgos4brZi07oOGLM1I0SUAWu', '2026-03-09 06:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `bio` text DEFAULT NULL,
  `font_style` enum('techy','pixelated','modern','aesthetic') DEFAULT 'modern',
  `card_color` varchar(20) DEFAULT '#1a1a1a',
  `text_color` varchar(20) DEFAULT '#ffffff',
  `border_color` varchar(20) DEFAULT '#f5a623',
  `profile_pic` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `total_wins` int(11) DEFAULT 0,
  `total_games` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`id`, `name`, `bio`, `font_style`, `card_color`, `text_color`, `border_color`, `profile_pic`, `cover_image`, `total_wins`, `total_games`, `created_at`) VALUES
(1, 'sazi', 'The Owner', 'pixelated', '#00032e', '#5778ff', '#001e94', '69ae72b7365104.02133691.jpg', '69ae72b736bbe0.66257690.png', 69, 70, '2026-03-09 07:11:51'),
(2, 'yousuf', 'i am yousuf the youfus', 'aesthetic', '#7e0101', '#ffffff', '#f5a623', '69ae76477ca311.60970659.gif', '69ae76477cd1d0.74597791.jpg', 40, 75, '2026-03-09 07:27:03'),
(3, 'theMaze', 'maze the caze', 'modern', '#ff85fb', '#000000', '#ff00c8', '69ae785163b779.16460814.jpeg', '69ae785163e9a0.03768608.jpeg', 43, 60, '2026-03-09 07:35:45'),
(4, 'lasthope', 'win is all i know, i care nobody', 'techy', '#002e01', '#ff05f7', '#01f46a', '69ae7b37b15b72.50413347.jpg', '69ae7b37b16ca7.74394652.jpg', 44, 74, '2026-03-09 07:48:07');

-- --------------------------------------------------------

--
-- Table structure for table `tournaments`
--

CREATE TABLE `tournaments` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` longtext DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tournaments`
--

INSERT INTO `tournaments` (`id`, `name`, `description`, `banner`, `created_at`) VALUES
(1, 'Ramadan Cup', '<h1>Tournament Stats: Winner - SAZI</h1><h2><br></h2><h2><strong>Trio Wins:</strong></h2><p><br></p><p>SAZI  + catisfyig + lasthope = 3</p><p>theMaze + dokanWala + yousuf = 2</p><p>mime + lime + melon = 1</p><p><br></p><p><br></p><h2>Duo Wins:</h2><p><br></p><p>SAZI + lasthope = 4</p><p>mime + melon = 1</p><p>theMaze + yousuf = 1</p>', '69ae7530036b32.35262421.png', '2026-03-09 07:22:24');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_stats`
--

CREATE TABLE `tournament_stats` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `wins` int(11) DEFAULT 0,
  `games` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tournament_stats`
--

INSERT INTO `tournament_stats` (`id`, `tournament_id`, `player_id`, `wins`, `games`) VALUES
(1, 1, 1, 7, 7),
(3, 1, 3, 4, 7),
(4, 1, 2, 6, 7),
(5, 1, 4, 7, 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tournaments`
--
ALTER TABLE `tournaments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tournament_stats`
--
ALTER TABLE `tournament_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_tp` (`tournament_id`,`player_id`),
  ADD KEY `player_id` (`player_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tournaments`
--
ALTER TABLE `tournaments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tournament_stats`
--
ALTER TABLE `tournament_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tournament_stats`
--
ALTER TABLE `tournament_stats`
  ADD CONSTRAINT `tournament_stats_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_stats_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
