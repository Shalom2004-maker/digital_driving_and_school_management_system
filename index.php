<?php
// index.php
// Landing page

$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <h1>Welcome to Digital Driving School</h1>
    <p>Manage lessons, bookings, and more.</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
    <a href="./login.php" class="btn">Login</a>
    <a href="./register.php" class="btn">Register</a>
    <?php else: ?>
    <a href="./dashboard.php" class="btn">Dashboard</a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>