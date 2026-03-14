<?php
$admin_current = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="admin-sidebar">
  <div class="admin-sidebar-logo">
    <img src="/css/img/v4d.png" alt="v4d" class="admin-sidebar-logo-img">
    <span>v4d Admin</span>
  </div>

  <nav class="admin-sidebar-nav">
    <a href="/admin/index.php" class="sidebar-link <?= $admin_current === 'index' ? 'active' : '' ?>">
      <span>📊</span> Dashboard
    </a>
    <a href="/admin/players.php" class="sidebar-link <?= $admin_current === 'players' ? 'active' : '' ?>">
      <span>👥</span> Players
    </a>
    <a href="/admin/tournaments.php" class="sidebar-link <?= $admin_current === 'tournaments' ? 'active' : '' ?>">
      <span>🏆</span> Tournaments
    </a>
    <a href="/admin/stats.php" class="sidebar-link <?= $admin_current === 'stats' ? 'active' : '' ?>">
      <span>✏️</span> Update Stats
    </a>
    <div class="sidebar-divider"></div>
    <a href="/" target="_blank" class="sidebar-link">
      <span>🌐</span> View Site
    </a>
    <a href="/admin/logout.php" class="sidebar-link sidebar-logout">
      <span>🚪</span> Logout
    </a>
  </nav>
</aside>
