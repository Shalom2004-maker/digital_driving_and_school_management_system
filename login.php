<?php
// login.php
ob_start();
$page_title = 'Login';
require_once __DIR__ . '/includes/header.php';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/users_model.php';
require_once __DIR__ . '/includes/session_check.php';

$conn = $GLOBALS['conn'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
        $user = authenticateUser($email, $password, $conn);
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            logActivity('login_success', $email);
            ob_end_clean();
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid credentials or unverified email.';
            logActivity('login_failed', $email);
        }
    } else {
        $error = 'Invalid input.';
    }
}
?>

<div class="container">
    <h2>Login</h2>
    <?php if (isset($error)): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
    <form method="POST">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <a href="register.php">Register</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>