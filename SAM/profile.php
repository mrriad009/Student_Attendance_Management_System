<?php
// Connect to the database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'student_attendance';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = $_GET['id'] ?? null;

if (!$student_id) {
    die("No student ID provided.");
}

// Fetch student and attendance data
$sql = "SELECT s.name, s.email, s.department, a.total_classes, a.present, a.absent, a.percentage
        FROM students s
        JOIN attendance a ON s.id = a.student_id
        WHERE s.id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($name, $email, $department, $total_classes, $present, $absent, $percentage);
    $stmt->fetch();
    $stmt->close();
} else {
    die("Error retrieving student data.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container profile-container">
        <h2>Student Profile</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>

        <h3>Attendance Details</h3>
        <p><strong>Total Classes:</strong> <?php echo htmlspecialchars($total_classes); ?></p>
        <p><strong>Present:</strong> <?php echo htmlspecialchars($present); ?></p>
        <p><strong>Absent:</strong> <?php echo htmlspecialchars($absent); ?></p>
        <p><strong>Attendance Percentage:</strong> <?php echo htmlspecialchars($percentage); ?>%</p>
    </div>
</body>
</html>

<?php $conn->close(); ?>
