<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

include 'db.php';

// Fetch a summary of the data for the dashboard
$totalStudents = $conn->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];
$attendanceToday = $conn->query("SELECT COUNT(*) AS count FROM attendance WHERE date = CURDATE()")->fetch_assoc()['count'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    </header>
    <main>
        <section class="stats">
            <h2>Dashboard Overview</h2>
            <p><strong>Total Students:</strong> <?php echo $totalStudents; ?></p>
            <p><strong>Attendance Marked Today:</strong> <?php echo $attendanceToday; ?></p>
        </section>
        <section class="actions">
            <h2>Actions</h2>
            <ul>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="add_student.php">Add Student</a></li>
                    <li><a href="mark_attendance.php">Mark Attendance</a></li>
                    <li><a href="view_reports.php">View Reports</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </section>
    </main>
</body>
</html>
