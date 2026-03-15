<?php
require_once __DIR__ . '/../adding/db.php';
require_once __DIR__ . '/../adding/functions.php';
session_start();

if (is_player()) {
    redirect('../player.php?id=' . current_player_id());
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM players WHERE username = ?");
        $stmt->execute([$username]);
        $player = $stmt->fetch();

        if ($player && password_verify($password, $player['password_hash'])) {
            $_SESSION['player_id'] = $player['id'];
            $_SESSION['player_name'] = $player['name'];
            redirect('../player.php?id=' . $player['id']);
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Player Login — v4d Esports</title>
  <link rel="icon" type="image/png" href="../css/img/v4d.png">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/admin.css">
  <style>
    body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .login-wrap { width: 100%; max-width: 400px; padding: 24px; }
    .login-card { background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 40px 36px; box-shadow: var(--shadow-card); }
    .login-logo { height: 60px; margin: 0 auto 20px; filter: drop-shadow(0 0 12px rgba(245,166,35,0.5)); }
    .login-title { font-family: var(--font-techy); font-size: 1.4rem; text-align: center; color: var(--color-primary); margin-bottom: 6px; }
    .login-sub { text-align: center; color: var(--color-muted); font-size: 0.85rem; margin-bottom: 28px; }
  </style>
</head>
<body class="bg-grid">
  <div class="glow-orb glow-orb-1" aria-hidden="true"></div>
  <div class="glow-orb glow-orb-2" aria-hidden="true"></div>

  <div class="login-wrap">
    <div class="login-card">
      <img src="../css/img/v4d.png" alt="v4d Logo" class="login-logo">
      <h1 class="login-title">Player Login</h1>
      <p class="login-sub">Welcome back, Champion</p>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= sanitize($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= sanitize($success) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required autocomplete="username">
        </div>
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:8px;">Login →</button>
      </form>

      <p style="text-align:center; margin-top:20px; font-size:0.8rem; color:var(--color-muted);">
        <a href="../index.php" style="color:var(--color-primary);">← Back to site</a>
      </p>
    </div>
  </div>
</body>
</html>
