  </div><!-- /.page-wrapper -->

  <!-- Unified Footer -->
  <footer class="site-footer">
    <div class="container">
      <img src="css/img/v4d.png" alt="v4d Esports" class="footer-logo">

      <a href="https://discord.gg/Gqm7wYr7X" target="_blank" rel="noopener noreferrer" class="footer-discord-btn">
        <svg width="16" height="12" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M15.245 1.187A14.7 14.7 0 0 0 11.61 0c-.163.295-.354.692-.485 1.007a13.59 13.59 0 0 0-4.25 0A10.63 10.63 0 0 0 6.386 0a14.74 14.74 0 0 0-3.638 1.19C.39 4.573-.24 7.867.075 11.115c1.526 1.147 3.007 1.843 4.463 2.299a11.1 11.1 0 0 0 .954-1.594 9.63 9.63 0 0 1-1.502-.739c.126-.094.249-.192.368-.293 2.894 1.367 6.034 1.367 8.894 0 .12.101.243.199.368.293a9.62 9.62 0 0 1-1.505.74 11.09 11.09 0 0 0 .954 1.593c1.457-.456 2.94-1.152 4.465-2.3.368-3.775-.623-7.037-2.29-9.927ZM6.009 9.097c-.917 0-1.672-.864-1.672-1.924s.737-1.926 1.672-1.926c.934 0 1.688.865 1.672 1.926 0 1.06-.738 1.924-1.672 1.924Zm6.177 0c-.917 0-1.672-.864-1.672-1.924s.737-1.926 1.672-1.926c.934 0 1.688.865 1.672 1.926 0 1.06-.738 1.924-1.672 1.924Z" fill="currentColor"/>
        </svg>
        Join our Discord
      </a>

      <p class="footer-text">
        &copy; <?= date('Y') ?> <span>v4d Esports</span>. All rights reserved. Built for champions.
      </p>

      <div class="footer-divider"></div>

      <div class="dev-credit-inner">
        <span class="dev-credit-label">Developed by</span>
        <a href="https://sazedur.space/" target="_blank" rel="noopener noreferrer" class="dev-credit-link">
          <span class="dev-credit-avatar">⚡</span>
          <span class="dev-credit-name">sazi</span>
          <span class="dev-credit-arrow">↗</span>
        </a>
      </div>
    </div>
  </footer>

  <style>
  /* Merged footer */
  .site-footer {
    background: #050505;
  }

  .footer-discord-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    border-radius: 50px;
    background: linear-gradient(135deg, #5865F2, #4752C4);
    color: #fff;
    font-family: 'Poppins', sans-serif;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-decoration: none;
    margin: 0 auto 16px;
    box-shadow: 0 4px 18px rgba(88,101,242,0.35);
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
  }

  .footer-discord-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(88,101,242,0.55);
    background: linear-gradient(135deg, #6875f5, #5865F2);
  }

  .footer-divider {
    width: 60px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(245,166,35,0.3), transparent);
    margin: 16px auto;
  }

  .dev-credit-inner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding-bottom: 4px;
  }

  .dev-credit-label {
    font-family: 'Poppins', sans-serif;
    font-size: 0.75rem;
    color: rgba(255,255,255,0.25);
    letter-spacing: 1.5px;
    text-transform: uppercase;
    font-weight: 400;
  }

  .dev-credit-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px 4px 8px;
    border-radius: 50px;
    border: 1px solid rgba(245,166,35,0.2);
    background: rgba(245,166,35,0.05);
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    position: relative;
    overflow: hidden;
  }

  .dev-credit-link::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(245,166,35,0.15), rgba(255,215,0,0.08));
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: inherit;
  }

  .dev-credit-link:hover::before { opacity: 1; }

  .dev-credit-link:hover {
    border-color: rgba(245,166,35,0.55);
    transform: translateY(-1px);
    box-shadow: 0 4px 18px rgba(245,166,35,0.18);
  }

  .dev-credit-avatar {
    font-size: 0.85rem;
    line-height: 1;
    filter: drop-shadow(0 0 4px rgba(245,166,35,0.8));
  }

  .dev-credit-name {
    font-family: 'Orbitron', 'Poppins', sans-serif;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 2px;
    background: linear-gradient(135deg, #F5A623, #FFD700);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-transform: uppercase;
  }

  .dev-credit-arrow {
    font-size: 0.72rem;
    color: rgba(245,166,35,0.45);
    transition: transform 0.3s ease, color 0.3s ease;
    -webkit-text-fill-color: rgba(245,166,35,0.45);
  }

  .dev-credit-link:hover .dev-credit-arrow {
    transform: translate(2px, -2px);
    color: #FFD700;
    -webkit-text-fill-color: #FFD700;
  }
  </style>

  <!-- Shared JS -->
  <script src="js/main.js"></script>

  <!-- Page-specific scripts -->
  <?php if (isset($extra_js)): foreach ($extra_js as $js): ?>
  <script src="js/<?= $js ?>"></script>
  <?php endforeach; endif; ?>

  <!-- Inline scripts -->
  <?php if (isset($footer_scripts)) echo $footer_scripts; ?>
</body>
</html>
