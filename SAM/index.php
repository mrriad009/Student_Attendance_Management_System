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
$search_results = []; // Initialize search results array
if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Query to search for student by ID
    $sql = "SELECT s.id, s.name, s.email, s.department, a.total_classes, a.present, a.absent, a.percentage
            FROM students s
            JOIN attendance a ON s.id = a.student_id
            WHERE s.id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->bind_result($id, $name, $email, $department, $total_classes, $present, $absent, $percentage);
        if ($stmt->fetch()) {
            $search_results = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'department' => $department,
                'total_classes' => $total_classes,
                'present' => $present,
                'absent' => $absent,
                'percentage' => $percentage
            ];
        } else {
            echo "<p class='error-message'>No student found with ID: " . htmlspecialchars($student_id) . "</p>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Management</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .search-results {
            margin-top: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .search-results table {
            width: 100%;
            border-collapse: collapse;
        }
        .search-results th, .search-results td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .search-results th {
            background-color: #f2f2f2;
        }
        .view-profile-btn {
            background: #6a11cb;
            color: #fff;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .view-profile-btn:hover {
            background: #2575fc;
        }
    </style>
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

        <!-- Display Search Results -->
        <?php if (!empty($search_results)) : ?>
            <div class="search-results">
                <h3>Search Results</h3>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Attendance (%)</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <td><?php echo htmlspecialchars($search_results['id']); ?></td>
                        <td><?php echo htmlspecialchars($search_results['name']); ?></td>
                        <td><?php echo htmlspecialchars($search_results['department']); ?></td>
                        <td><?php echo htmlspecialchars($search_results['percentage']); ?>%</td>
                        <td>
                            <a href="profile.php?id=<?php echo htmlspecialchars($search_results['id']); ?>" class="view-profile-btn">View Profile</a>
                        </td>
                    </tr>
                </table>
            </div>
        <?php endif; ?>

        <!-- Department Filter Form -->
        <form method="POST" action="">
            <label for="department"><h3>Select Department: </h3></label>
            <select name="department" id="department" onchange="this.form.submit()">
                <option value="">--Select Department--</option>
                <option value="Computer Science" <?php echo ($department == 'Computer Science' ? 'selected' : ''); ?>>Computer Science</option>
                <option value="Electrical Engineering" <?php echo ($department == 'Electrical Engineering' ? 'selected' : ''); ?>>Electrical Engineering</option>
                <option value="Mechanical Engineering" <?php echo ($department == 'Mechanical Engineering' ? 'selected' : ''); ?>>Mechanical Engineering</option>
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