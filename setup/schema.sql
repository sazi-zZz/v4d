-- v4d Clan Statistics Management System
-- Database: v4d
-- Run: mysql -u root v4d < setup/schema.sql

CREATE DATABASE IF NOT EXISTS v4d CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE v4d;

-- Admin users
CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed default admin: username=admin, password=v4d2024
INSERT INTO admin_users (username, password_hash)
VALUES ('admin', '$2y$12$jfLQkRk/ix5wFWqHnumz5O4hTOOL3xaKLXX5wGaFLmgC6CLYvpaoe')
ON DUPLICATE KEY UPDATE username = username;

-- Players / clan members
CREATE TABLE IF NOT EXISTS players (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  bio TEXT,
  font_style ENUM('techy','pixelated','modern','aesthetic') DEFAULT 'modern',
  card_color VARCHAR(20) DEFAULT '#1a1a1a',
  text_color VARCHAR(20) DEFAULT '#ffffff',
  border_color VARCHAR(20) DEFAULT '#f5a623',
  profile_pic VARCHAR(255) DEFAULT NULL,
  cover_image VARCHAR(255) DEFAULT NULL,
  total_wins INT DEFAULT 0,
  total_games INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tournaments
CREATE TABLE IF NOT EXISTS tournaments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description LONGTEXT,
  banner VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Per-tournament per-player stats
CREATE TABLE IF NOT EXISTS tournament_stats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tournament_id INT NOT NULL,
  player_id INT NOT NULL,
  wins INT DEFAULT 0,
  games INT DEFAULT 0,
  FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
  FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_tp (tournament_id, player_id)
);
