<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';  // Database host
$username = 'root';   // Database username
$password = '';       // Database password
$database = 'student_attendance';  // Database name

$conn = new mysqli($host, $username, $password, $database);

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connection successful!<br>"; // Debug statement
}

// Initialize variables
$student_id = '';
$search_results = [];
$department = '';
$error_message = '';
$department_results = [];

// Handle student search by ID
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Query to search for student by ID and fetch attendance details
    $sql = "SELECT s.id, s.name, s.email, s.department, 
                   COUNT(a.class_date) AS total_classes, 
                   SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present, 
                   SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent, 
                   FLOOR((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.class_date)) * 100) AS percentage
            FROM students s
            JOIN attendance_record a ON s.id = a.student_id
            WHERE s.id = ?
            GROUP BY s.id, s.name, s.email, s.department";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $email, $department, $total_classes, $present, $absent, $percentage);
            $stmt->fetch();
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
            $error_message = "No student found with ID: " . htmlspecialchars($student_id);
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing search statement: " . $conn->error;
    }
}

// Handle department filter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['department'])) {
    $department = $_POST['department'];

    // Query to get students from the selected department, sorted by attendance percentage
    $sql = "SELECT s.id, s.name, s.email, s.department, 
                   21 AS total_classes, 
                   SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present, 
                   SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent, 
                   FLOOR((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100) AS percentage
            FROM students s
            JOIN attendance_record a ON s.id = a.student_id
            WHERE s.department = ?
            GROUP BY s.id, s.name, s.email, s.department
            ORDER BY percentage DESC";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $department);
        $stmt->execute();
        $stmt->bind_result($id, $name, $email, $department, $total_classes, $present, $absent, $percentage);
        while ($stmt->fetch()) {
            $department_results[] = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'department' => $department,
                'total_classes' => $total_classes,
                'present' => $present,
                'absent' => $absent,
                'percentage' => $percentage
            ];
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing department filter statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Management</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1200px;
            margin: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }

        h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            color: #333;
        }

        /* Buttons */
        .btn {
            background: #6a11cb;
            color: #fff;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }

        .btn:hover {
            background: #2575fc;
            transform: translateY(-2px);
        }

        .admin-panel-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        /* Forms */
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        input[type="text"], select, button {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, select:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
            outline: none;
        }

        /* Search Results */
        .search-results, .department-results {
            margin-top: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        .search-results table, .department-results table {
            width: 100%;
            border-collapse: collapse;
        }

        .search-results th, .search-results td, .department-results th, .department-results td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .search-results th, .department-results th {
            background-color: #6a11cb;
            color: #fff;
        }

        .search-results tr, .department-results tr {
            transition: background-color 0.3s ease;
        }

        .search-results tr:hover, .department-results tr:hover {
            background-color: #f1f1f1;
        }

        .view-profile-btn {
            background: #6a11cb;
            color: #fff;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .view-profile-btn:hover {
            background: #2575fc;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Student Attendance Management System</h1>
        
        <!-- Add the Admin Panel Button -->
        <a href="admin_panel.php" class="btn admin-panel-btn">Admin Panel</a>
        <!-- Add the Register Button -->
        <a href="register.php" class="btn">Register New Student</a>

        <!-- Student Search Form -->
        <form method="POST" action="">
            <input type="text" name="student_id" placeholder="Enter Student ID" value="<?php echo htmlspecialchars($student_id); ?>" required>
            <button type="submit" class="btn">Search</button>
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
                        <th>Total Classes</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Attendance (%)</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <td><?php echo htmlspecialchars($search_results['id']); ?></td>
                        <td><?php echo htmlspecialchars($search_results['name']); ?></td>
                        <td><?php echo htmlspecialchars($search_results['department']); ?></td>
                        <td><?php echo htmlspecialchars($search_results['total_classes']); ?></td>
                        <td><?php echo htmlspecialchars($search_results['present']); ?></td>
                        <td><?php echo htmlspecialchars($search_results['absent']); ?></td>
                        <td><?php echo htmlspecialchars($search_results['percentage']); ?>%</td>
                        <td>
                            <a href="profile.php?id=<?php echo htmlspecialchars($search_results['id']); ?>" class="view-profile-btn">View Profile</a>
                        </td>
                    </tr>
                </table>
            </div>
        <?php elseif (isset($error_message)) : ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Department Filter Form -->
        <form method="POST" action="">
            <label for="department"><h3>Select Department: </h3></label>
            <select name="department" id="department" required>
                <option value="">--Select Department--</option>
                <option value="Computer Science">Computer Science</option>
                <option value="Electrical Engineering">Electrical Engineering</option>
                <option value="Mechanical Engineering">Mechanical Engineering</option>
            </select>
            <button type="submit" class="btn">Check Department</button>
        </form>

        <!-- Display Department Results -->
        <?php if (!empty($department_results)) : ?>
            <div class="department-results">
                <h3>Students in <?php echo htmlspecialchars($department); ?></h3>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>ID</th>
                        <th>Total Classes</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Attendance (%)</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($department_results as $student) : ?>
                        <tr>
                            <td><a href="profile.php?id=<?php echo htmlspecialchars($student['id']); ?>"><?php echo htmlspecialchars($student['name']); ?></a></td>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($student['total_classes']); ?></td>
                            <td><?php echo htmlspecialchars($student['present']); ?></td>
                            <td><?php echo htmlspecialchars($student['absent']); ?></td>
                            <td><?php echo htmlspecialchars($student['percentage']); ?>%</td>
                            <td>
                                <?php
                                if ($student['percentage'] < 30) {
                                    echo 'Warning';
                                } elseif ($student['percentage'] > 90) {
                                    echo 'Impressive';
                                } else {
                                    echo 'Normal';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>