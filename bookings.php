<?php
// bookings.php
// Manage bookings (admin/instructor view)

$page_title = 'Bookings';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/session_check.php';
checkRole(ROLE_ADMIN); // Or ROLE_INSTRUCTOR

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/bookings_model.php';
require_once __DIR__ . '/controllers/booking_controller.php';

$conn = $GLOBALS['conn'];

// Handle POST (create, confirm, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $result = processNewBooking($_POST['student_id'], $_POST['instr_id'], $_POST['pkg_id'], $_POST['date'], $_POST['time'], $conn);
        echo "<script>showToast('{$result['message']}', '{$result['success'] ? 'success' : 'error'}');</script>";
    } elseif ($action === 'confirm') {
        $result = confirmBooking($_POST['booking_id'], $_POST['student_id'], $_POST['instr_id'], $_POST['lesson_type'], $conn);
        echo "<script>showToast('{$result['message']}', '{$result['success'] ? 'success' : 'error'}');</script>";
    } elseif ($action === 'delete') {
        if (deleteBooking($_POST['booking_id'], $conn)) {
            echo "<script>showToast('Deleted.', 'success');</script>";
        }
    }
}

$bookings = getBookingsByInstructor(1, $conn); // Example; dynamic
?>

<h1>Bookings</h1>
<form method="POST">
    <input type="hidden" name="action" value="create">
    Student ID: <input name="student_id" type="number" required><br>
    Instructor ID: <input name="instr_id" type="number" required><br>
    Package ID: <select name="pkg_id">
        <?php // Fetch packages ?>
    </select><br>
    Date: <input name="date" type="date" required><br>
    Time: <input name="time" type="time" required><br>
    <button type="submit">Create</button>
</form>

<table class="data-table">
    <thead><tr><th>ID</th><th>Student</th><th>Date/Time</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
        <?php foreach ($bookings as $b): ?>
            <tr>
                <td><?php echo $b['booking_id']; ?></td>
                <td><?php echo $b['student_name']; ?></td>
                <td><?php echo $b['date'] . ' ' . $b['time']; ?></td>
                <td><?php echo $b['status']; ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="confirm">
                        <input type="hidden" name="booking_id" value="<?php echo $b['booking_id']; ?>">
                        <input type="hidden" name="student_id" value="<?php echo $b['student_id']; ?>">
                        <input type="hidden" name="instr_id" value="<?php echo $b['instr_id']; ?>">
                        <input type="hidden" name="lesson_type" value="practical">
                        <button>Confirm</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="booking_id" value="<?php echo $b['booking_id']; ?>">
                        <button onclick="return confirm('Delete?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/includes/footer.php'; ?>