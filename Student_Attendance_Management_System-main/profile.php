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
$sql = "SELECT s.name, s.email, s.department, 
               COUNT(a.class_date) AS total_classes, 
               SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present, 
               SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent, 
               (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.class_date)) * 100 AS percentage
        FROM students s
        JOIN attendance_record a ON s.id = a.student_id
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0e0e0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .container:hover {
            transform: translateY(-10px);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        h2, h3 {
            color: #333;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        p {
            line-height: 1.6;
            color: #555;
            margin: 10px 0;
        }
        .profile-container {
            text-align: left;
        }
        .profile-container p {
            margin: 10px 0;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .profile-container strong {
            color: #000;
        }
        .buttons {
            margin-top: 20px;
        }
        .buttons a {
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }
        .buttons a:hover {
            background-color: #0056b3;
        }
    </style>
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

        <div class="buttons">
            <a href="javascript:history.back()">Back</a>
            <a href="index.php">Home</a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
