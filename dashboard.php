<?php
// dashboard.php
// Role-based dashboard

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/session_check.php';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/users_model.php';
require_once __DIR__ . '/models/bookings_model.php';
require_once __DIR__ . '/models/progress_model.php';

$conn = $GLOBALS['conn'];
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch data based on role
$bookings = [];
$progress = [];
if ($role === ROLE_STUDENT) {
    $bookings = getBookingsByStudent($userId, $conn);
    $progress = getStudentProgress($userId, $conn);
} elseif ($role === ROLE_INSTRUCTOR) {
    // Get instr_id
    $stmt = $conn->prepare("SELECT instr_id FROM instructors WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $instrId = $stmt->get_result()->fetch_assoc()['instr_id'] ?? 0;
    $bookings = getBookingsByInstructor($instrId, $conn);
} elseif ($role === ROLE_ADMIN) {
    // Stats
    $stats = [
        'users' => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
        'revenue' => $conn->query("SELECT SUM(amount) FROM payments")->fetch_row()[0] ?? 0,
    ];
}

logActivity('dashboard_view', $_SESSION['email']);
?>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> (<?php echo ucfirst($role); ?>)</h1>
    <?php if ($role === ROLE_STUDENT): ?>
    <h2>Upcoming Bookings</h2>
    <table>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
        </tr>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td><?php echo $b['date']; ?></td>
            <td><?php echo $b['time']; ?></td>
            <td><?php echo $b['status']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="./bookings.php">Book Lesson</a>
    <?php elseif ($role === ROLE_INSTRUCTOR): ?>
    <h2>Your Schedule</h2>
    <table>
        <tr>
            <th>Date</th>
            <th>Student</th>
            <th>Status</th>
        </tr>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td><?php echo $b['date']; ?></td>
            <td><?php echo $b['student_name']; ?></td>
            <td><?php echo $b['status']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <h2>Admin Stats</h2>
    <p>Total Users: <?php echo $stats['users']; ?></p>
    <p>Revenue: $<?php echo number_format($stats['revenue'], 2); ?></p>
    <a href="./bookings.php">Manage</a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>