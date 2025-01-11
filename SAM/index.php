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

// Handle search form submission
$student_id = '';
if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Student Attendance Management System</h1>

        <form method="POST" action="">
            <input type="text" name="student_id" placeholder="Enter Student ID" value="<?php echo htmlspecialchars($student_id); ?>" required>
            <button type="submit">Search</button>
        </form>

        <?php
        if ($student_id) {
            // Query for student data
            $sql = "SELECT id, name FROM students WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $student_id);
                $stmt->execute();
                $stmt->bind_result($id, $name);
                if ($stmt->fetch()) {
                    echo "<p><strong>Student Found:</strong> <a href='profile.php?id=" . htmlspecialchars($id) . "'>" . htmlspecialchars($name) . "</a></p>";
                } else {
                    echo "<p>No student found with ID: " . htmlspecialchars($student_id) . "</p>";
                }
                $stmt->close();
            }
        }
        ?>

    </div>
</body>
</html>

<?php $conn->close(); ?>
