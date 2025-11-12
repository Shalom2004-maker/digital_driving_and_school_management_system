<?php
// logout.php
session_start();
require_once __DIR__ . '/includes/session_check.php';
logActivity('logout', 'User: ' . ($_SESSION['email'] ?? 'Unknown'));
session_destroy();
header('Location: login.php');
exit();
?>