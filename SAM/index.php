<?php
// Database connection
$host = 'localhost';  // Database host for localhost
$username = 'root';   // Database username for localhost
$password = '';       // Database password for localhost
$database = 'student_attendance';  // Your database name

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle department filter
$department = '';
if (isset($_POST['department'])) {
    $department = $_POST['department'];
}

// Handle student search by ID
$student_id = '';  // Initialize $student_id
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
        
        <!-- Add the Register Button -->
        <a href="register.php">
            <button>Register New Student</button>
        </a>

        <!-- Student Search Form -->
        <form method="POST" action="">
            <input type="text" name="student_id" placeholder="Enter Student ID" value="<?php echo htmlspecialchars($student_id); ?>" required>
            <button type="submit">Search</button>
        </form>

        <h2> 

        </h2>
        <form method="POST" action="">
            <label for="department"><h3>Select Department: </h3></label>

            <select name="department" id="department" onchange="this.form.submit()">
                <option value="">--Select Department--</option>
                <option value="Computer Science" <?php echo ($department == 'Computer Science' ? 'selected' : ''); ?>>Computer Science</option>
                <option value="Electrical Engineering" <?php echo ($department == 'Electrical Engineering' ? 'selected' : ''); ?>>Electrical Engineering</option>
                <option value="Mechanical Engineering" <?php echo ($department == 'Mechanical Engineering' ? 'selected' : ''); ?>>Mechanical Engineering</option>
                <!-- Add more departments as needed -->
            </select>
        </form>

        <?php
        if ($department) {
            // Query to get students from the selected department, sorted by attendance percentage
            $sql = "SELECT s.id, s.name, s.email, s.department, a.total_classes, a.present, a.absent, a.percentage
                    FROM students s
                    JOIN attendance a ON s.id = a.student_id
                    WHERE s.department = ?
                    ORDER BY a.percentage DESC";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $department);
                $stmt->execute();
                $stmt->bind_result($id, $name, $email, $department, $total_classes, $present, $absent, $percentage);
                echo "<h3>Students in $department</h3>";
                echo "<table>
                        <tr>
                            <th>Name</th>
                            <th>ID</th>
                            <th>Total Classes</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Attendance (%)</th>
                            <th>Status</th>
                        </tr>";
                while ($stmt->fetch()) {
                    $status = '';
                    if ($percentage < 30) {
                        $status = 'Warning';
                    } elseif ($percentage > 90) {
                        $status = 'Impressive';
                    } else {
                        $status = 'Normal';
                    }
                    echo "<tr>
                            <td><a href='profile.php?id=" . htmlspecialchars($id) . "'>" . htmlspecialchars($name) . "</a></td>
                            <td>" . htmlspecialchars($id) . "</td>
                            <td>" . htmlspecialchars($total_classes) . "</td>
                            <td>" . htmlspecialchars($present) . "</td>
                            <td>" . htmlspecialchars($absent) . "</td>
                            <td>" . htmlspecialchars($percentage) . "%</td>
                            <td>" . htmlspecialchars($status) . "</td>
                          </tr>";
                }
                echo "</table>";
                $stmt->close();
            }
        }
        ?>

    </div>
</body>
</html>

<?php $conn->close(); ?>
