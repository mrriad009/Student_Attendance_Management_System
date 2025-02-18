<?php
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: admin_panel.php");
    exit();
}

// Ensure that the user is logged in and has a valid role (CR/Professor)
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['professor', 'CR'])) {
    // Redirect to login page if not logged in or not authorized
    header("Location: login.php");
    exit();
}

// Retrieve user info from the session
$user = $_SESSION['user'];
$username = "Welcome, " . $user['role']; // Custom welcome message based on the role

// Database connection
include 'db_connect.php';

// Initialize variables
$students = [];

// Fetch students from the database
$sql = "SELECT id, name FROM students";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
} else {
    echo "Error fetching student data: " . $conn->error;
}

// Process form submission to mark attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_date = $_POST['class_date'];
    $status = $_POST['status'];
    $student_ids = isset($_POST['student_ids']) ? $_POST['student_ids'] : [];

    foreach ($student_ids as $student_id) {
        // Fetch total classes for the student
        $sql_total_classes = "SELECT total_classes, present, absent FROM attendance_record WHERE student_id = ?";
        $stmt = $conn->prepare($sql_total_classes);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->bind_result($total_classes, $present, $absent);
        $stmt->fetch();
        $stmt->close();

        // Initialize present and absent if no record is found
        if ($total_classes === null) {
            $total_classes = 0;
            $present = 0;
            $absent = 0;
        }

        // Increment total classes
        $total_classes += 1;

        // Update attendance records dynamically
        if ($status == 'Present') {
            $present += 1;
        } elseif ($status == 'Absent') {
            $absent += 1;
        }

        // Calculate the percentage dynamically
        $percentage = ($present / $total_classes) * 100;

        // Check if an attendance record already exists for the student on the given date
        $sql_check = "SELECT id FROM attendance_record WHERE student_id = ? AND class_date = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("is", $student_id, $class_date);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update existing record
            $stmt->close();
            $stmt = $conn->prepare("UPDATE attendance_record SET status = ?, total_classes = ?, present = ?, absent = ?, percentage = ? WHERE student_id = ? AND class_date = ?");
            $stmt->bind_param("siiiisi", $status, $total_classes, $present, $absent, $percentage, $student_id, $class_date);
        } else {
            // Insert new record
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO attendance_record (student_id, class_date, status, total_classes, present, absent, percentage) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issiiii", $student_id, $class_date, $status, $total_classes, $present, $absent, $percentage);
        }

        if (!$stmt->execute()) {
            echo "Error marking attendance for student ID $student_id: " . $conn->error;
        }
        $stmt->close();
    }

    echo "Attendance marked successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
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
            position: relative;
        }

        h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            color: #333;
        }

        .top-right-button {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        /* Add your styles for managing attendance here */
    </style>
</head>
<body>
    <div class="container">
        <a href="view_attendance.php" class="top-right-button"><button>View Records</button></a>
        <h1><?php echo $username; ?> - Manage Attendance</h1>
    
        <form method="POST" action="">
            <label for="class_date">Class Date:</label>
            <input type="date" name="class_date" required><br><br>

            <label for="status">Attendance Status:</label>
            <select name="status" required>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Excused">Excused</option>
            </select><br><br>

            <label for="student_ids">Select Students:</label><br>
            <input type="checkbox" id="select_all" onclick="toggleSelectAll()"> Select All<br>
            <?php foreach ($students as $student): ?>
                <input type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>"> <?php echo $student['name']; ?><br>
            <?php endforeach; ?><br>

            <button type="submit">Submit Attendance</button>
        </form>

        <script>
            function toggleSelectAll() {
                var checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
                var selectAllCheckbox = document.getElementById('select_all');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            }
        </script>

        <h3>Attendance Records</h3>
        <table border="1">
            <tr>
                <th>Student Name</th>
                <th>Class Date</th>
                <th>Status</th>
            </tr>
            <?php
            $attendance_sql = "SELECT students.name, attendance_record.class_date, attendance_record.status 
                            FROM attendance_record 
                            JOIN students ON attendance_record.student_id = students.id";
            $attendance_result = $conn->query($attendance_sql);
            while ($attendance_row = $attendance_result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($attendance_row['name']) . "</td><td>" . htmlspecialchars($attendance_row['class_date']) . "</td><td>" . htmlspecialchars($attendance_row['status']) . "</td></tr>";
            }
            ?>
        </table>

        <br>
        <a href="view_attendance.php"><button>View All Attendance Records</button></a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
