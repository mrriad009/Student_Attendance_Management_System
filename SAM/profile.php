<?php
// Connect to the database
$host = 'localhost'; // Database host
$username = 'root'; // Database username
$password = ''; // Database password
$database = 'student_attendance'; // Database name

$conn = new mysqli($host, $username, $password, $database);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if student ID is passed in the URL (e.g., profile.php?id=1)
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Prepare the query to get student profile and attendance data
    $sql = "SELECT s.name, s.email, s.department, a.total_classes, a.present, a.absent, a.percentage
            FROM students s
            JOIN attendance a ON s.id = a.student_id
            WHERE s.id = ?";

    // Prepare and bind the statement to prevent SQL injection
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $student_id); // Bind the student ID as an integer
        $stmt->execute(); // Execute the query

        // Bind result variables
        $stmt->bind_result($name, $email, $department, $total_classes, $present, $absent, $percentage);

        // Fetch the result
        if ($stmt->fetch()) {
            // Display student profile and attendance information
            echo "<h2>Student Profile</h2>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
            echo "<p><strong>Department:</strong> " . htmlspecialchars($department) . "</p>";
            echo "<h3>Attendance</h3>";
            echo "<p><strong>Total Classes:</strong> " . htmlspecialchars($total_classes) . "</p>";
            echo "<p><strong>Present:</strong> " . htmlspecialchars($present) . "</p>";
            echo "<p><strong>Absent:</strong> " . htmlspecialchars($absent) . "</p>";
            echo "<p><strong>Attendance Percentage:</strong> " . htmlspecialchars($percentage) . "%</p>";
        } else {
            echo "<p>No student found with ID: " . htmlspecialchars($student_id) . "</p>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<p>Error in preparing the SQL query.</p>";
    }
} else {
    echo "<p>No student ID provided.</p>";
}

// Close the database connection
$conn->close();
?>
