<?php
// verify.php
$page_title = 'Verify Email';
require_once __DIR__ . '/includes/header.php';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/users_model.php';

$conn = $GLOBALS['conn'];
$token = $_GET['token'] ?? '';

if (verifyEmail($token, $conn)) {
    echo "<p>Email verified! <a href='login.php'>Login</a></p>";
} else {
    echo "<p>Invalid token. <a href='register.php'>Register again</a></p>";
}

require_once __DIR__ . '/includes/footer.php';
?>