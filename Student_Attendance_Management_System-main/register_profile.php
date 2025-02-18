<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost';  // Database host for localhost
$username = 'root';   // Database username for localhost
$password = '';       // Database password for localhost
$database = 'student_attendance';  // Your database name

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$id = $name = $email = $department = '';
$success_message = $error_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department = $_POST['department'];

    // Validate inputs
    if (empty($id) || empty($name) || empty($email) || empty($department)) {
        $error_message = "All fields are required.";
    } else {
        // Insert new record into attendance_record table
        $attendance_sql = "INSERT INTO attendance_record (student_id, student_name, attribute1, attribute2, attribute3) VALUES (?, ?, 0, 0, 0)";
        if ($attendance_stmt = $conn->prepare($attendance_sql)) {
            $attendance_stmt->bind_param("ss", $id, $name);
            if ($attendance_stmt->execute()) {
                $success_message = "New student registered successfully!";
            } else {
                $error_message = "Error registering student: " . $attendance_stmt->error;
            }
            $attendance_stmt->close();
        } else {
            $error_message = "Error preparing insert statement: " . $conn->error;
        }
    }
}

// Handle "Update Database" button click
if (isset($_POST['update_database'])) {
    $update_sql = "INSERT INTO attendance_record (student_id, student_name, attribute1, attribute2, attribute3)
                   SELECT id, name, 0, 0, 0 FROM attendance
                   ON DUPLICATE KEY UPDATE student_name = VALUES(student_name)";
    if ($conn->query($update_sql) === TRUE) {
        $success_message = "Database updated successfully!";
    } else {
        $error_message = "Error updating database: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Profile</title>
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
            max-width: 400px;
            animation: fadeIn 0.5s ease-in-out;
        }

        h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            color: #333;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #555;
        }

        input, select, button {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
            outline: none;
        }

        button {
            background: #6a11cb;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background: #2575fc;
            transform: translateY(-2px);
        }

        a {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #6a11cb;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Messages */
        .success-message {
            color: #28a745;
            text-align: center;
            margin-bottom: 1rem;
        }

        .error-message {
            color: #dc3545;
            text-align: center;
            margin-bottom: 1rem;
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

        .button-container {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register Profile</h1>
        <?php if (!empty($success_message)) : ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($error_message)) : ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="id">Student ID:</label>
            <input type="text" name="id" id="id" value="<?php echo htmlspecialchars($id); ?>" required>

            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="department">Department:</label>
            <select name="department" id="department" required>
                <option value="">--Select Department--</option>
                <option value="Computer Science" <?php echo ($department == 'Computer Science' ? 'selected' : ''); ?>>Computer Science</option>
                <option value="Electrical Engineering" <?php echo ($department == 'Electrical Engineering' ? 'selected' : ''); ?>>Electrical Engineering</option>
                <option value="Mechanical Engineering" <?php echo ($department == 'Mechanical Engineering' ? 'selected' : ''); ?>>Mechanical Engineering</option>
            </select>

            <button type="submit">Submit</button>
            <div class="button-container">
                <button type="submit" name="update_database">Update Database</button>
            </div>
        </form>
        
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>