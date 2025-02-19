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

// Database connection
include 'db_connect.php';

// Handle the "Update Database" button click
if (isset($_POST['update_database'])) {
    $update_sql = "INSERT INTO attendance_record (student_id, class_date, status)
                   SELECT student_id, NOW(), '0' FROM attendance";
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Database updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating database: " . $conn->error . "');</script>";
    }
}

// Fetch updated attendance records with fixed total classes (21)
$attendance_sql = "SELECT students.id, students.name, 
                          21 AS total_classes, 
                          SUM(CASE WHEN attendance_record.status = 'Present' THEN 1 ELSE 0 END) AS present, 
                          (SUM(CASE WHEN attendance_record.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100 AS percentage 
                   FROM attendance_record 
                   JOIN students ON attendance_record.student_id = students.id 
                   GROUP BY students.id, students.name";
$attendance_result = $conn->query($attendance_sql);

// Filter present students for a specific day
$filter_date = isset($_POST['filter_date']) ? $_POST['filter_date'] : null;
$present_students = [];
if ($filter_date) {
    $filter_sql = "SELECT students.id, students.name, attendance_record.class_date, attendance_record.status 
                   FROM attendance_record 
                   JOIN students ON attendance_record.student_id = students.id 
                   WHERE attendance_record.class_date = ? AND attendance_record.status = 'Present'";
    $stmt = $conn->prepare($filter_sql);
    $stmt->bind_param("s", $filter_date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $present_students[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .button-container {
            position: absolute;
            top: 40px;
            right: 350px;
        }

        .button-container button {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="button-container">
            <a href="manage_attendance.php"><button class="back-button">Back</button></a>
            <a href="index.php"><button class="home-button">Home</button></a>
            <form method="POST" action="" style="display:inline;">
                <button type="submit" name="update_database">Update Database</button>
            </form>
        </div>
        <h1>View Attendance Records</h1>
        <form method="POST" action="">
            <label for="filter_date">Select Date to View Present Students:</label>
            <input type="date" name="filter_date" required>
            <button type="submit">Filter</button>
        </form>
        <?php if ($filter_date): ?>
            <h2>Present Students on <?php echo htmlspecialchars($filter_date); ?></h2>
            <table>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Class Date</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($present_students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['class_date']); ?></td>
                        <td><?php echo htmlspecialchars($student['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        <h2>All Attendance Records</h2>
        <table>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Total Classes</th>
                <th>Present</th>
                <th>Percentage</th>
            </tr>
            <?php
            while ($attendance_row = $attendance_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($attendance_row['id']) . "</td>
                        <td>" . htmlspecialchars($attendance_row['name']) . "</td>
                        <td>" . htmlspecialchars($attendance_row['total_classes']) . "</td>
                        <td>" . htmlspecialchars($attendance_row['present']) . "</td>
                        <td>" . htmlspecialchars(number_format($attendance_row['percentage'], 2)) . "%</td>
                      </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
