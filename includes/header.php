<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="v4d Esports — PUBG Clan Statistics, Leaderboard & Tournament Tracker">
  <meta name="theme-color" content="#0a0a0a">
  <title><?= isset($page_title) ? sanitize($page_title) . ' — v4d Esports' : 'v4d Esports — PUBG Clan' ?></title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="css/img/v4d.png">

  <!-- Global Styles -->
  <link rel="stylesheet" href="css/style.css">

  <!-- Page-specific styles -->
  <?php if (isset($extra_css)): foreach ($extra_css as $css): ?>
  <link rel="stylesheet" href="css/<?= $css ?>">
  <?php endforeach; endif; ?>

  <!-- Head extras (e.g. Quill CSS) -->
  <?php if (isset($head_extras)) echo $head_extras; ?>
</head>
<body class="bg-grid">
  <!-- Ambient glow orbs -->
  <div class="glow-orb glow-orb-1" aria-hidden="true"></div>
  <div class="glow-orb glow-orb-2" aria-hidden="true"></div>

  <!-- Navigation -->
  <nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="container">
      <a href="index.php" class="navbar-brand" aria-label="v4d Esports Home">
        <img src="css/img/v4d.png" alt="v4d Logo" class="navbar-logo">
        <span class="navbar-title">V4D</span>
      </a>

      <ul class="navbar-nav" id="navbar-nav">
        <li><a href="index.php" class="<?= $current_page === 'index' ? 'active' : '' ?>">Home</a></li>
        <li><a href="leaderboard.php" class="<?= $current_page === 'leaderboard' ? 'active' : '' ?>">Leaderboard</a></li>
        <li><a href="tournaments.php" class="<?= $current_page === 'tournaments' ? 'active' : '' ?>">Tournaments</a></li>
        <?php if (is_player()): ?>
          <li><a href="player.php?id=<?= current_player_id() ?>" class="<?= ($current_page === 'player' && ($_GET['id'] ?? '') == current_player_id()) ? 'active' : '' ?>">My Profile</a></li>
          <li><a href="auth/player_logout.php" class="text-danger">Logout</a></li>
        <?php else: ?>
          <li><a href="auth/player_login.php" class="<?= $current_page === 'player_login' ? 'active' : '' ?>">Login</a></li>
        <?php endif; ?>
      </ul>

      <button class="navbar-hamburger" id="hamburger-btn" aria-label="Toggle menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>

  <div class="page-wrapper">
