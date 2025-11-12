<?php
// register.php
ob_start();
$page_title = 'Register';
require_once __DIR__ . '/includes/header.php';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/users_model.php';
require_once __DIR__ . '/includes/session_check.php';

$conn = $GLOBALS['conn'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    if (strlen($name) >= 2 && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6 && in_array($role, [ROLE_STUDENT, ROLE_INSTRUCTOR])) {
        $userId = createUser($name, $email, $password, $role, $conn);
        if ($userId) {
            $success = 'Registered! Check email for verification.';
            logActivity('register_success', $email);
        } else {
            $error = 'Email exists.';
        }
    } else {
        $error = 'Invalid input.';
    }
}
?>

<div class="container">
    <h2>Register</h2>
    <?php if (isset($error)): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
    <?php if (isset($success)): ?><p class="success"><?php echo $success; ?></p><?php endif; ?>
    <form method="POST">
        Name: <input type="text" name="name" required minlength="2"><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required minlength="6"><br>
        Role: <select name="role" required>
            <option value="">Select</option>
            <option value="<?php echo ROLE_STUDENT; ?>">Student</option>
            <option value="<?php echo ROLE_INSTRUCTOR; ?>">Instructor</option>
        </select><br>
        <button type="submit">Register</button>
    </form>
    <a href="login.php">Login</a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>