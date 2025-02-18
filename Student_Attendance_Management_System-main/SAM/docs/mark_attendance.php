<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

include 'db.php';

$students = $conn->query("SELECT * FROM students");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'];
    $status = $_POST['status'];
    $date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $studentId, $date, $status);

    if ($stmt->execute()) {
        echo "<p>Attendance marked successfully! <a href='dashboard.php'>Go back</a></p>";
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Mark Attendance</h1>
    </header>
    <main>
        <form action="mark_attendance.php" method="post">
            <label for="student_id">Select Student:</label>
            <select name="student_id" required>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <option value="<?php echo $student['id']; ?>"><?php echo $student['name']; ?> - <?php echo $student['class']; ?></option>
                <?php endwhile; ?>
            </select>
            <label for="status">Status:</label>
            <select name="status" required>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
            </select>
            <button type="submit">Mark Attendance</button>
        </form>
    </main>
</body>
</html>
