<?php
session_start();
session_destroy();
header('Location: /v4d/admin/login.php');
exit;
