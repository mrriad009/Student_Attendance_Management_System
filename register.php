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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$success_message = '';
$error_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department = $_POST['department'];

    // Validate inputs
    if (empty($id) || empty($name) || empty($email) || empty($department)) {
        $error_message = "All fields are required.";
    } else {
        // Check if id already exists
        $sql = "SELECT id FROM students WHERE id = ?";
        $stmt_check = $conn->prepare($sql);
        if ($stmt_check) {
            $stmt_check->bind_param("i", $id);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $error_message = "Student ID already exists.";
            } else {
                // Proceed with insertion
                $conn->begin_transaction();

                // Insert into students table
                $sql = "INSERT INTO students (id, name, email, department) VALUES (?, ?, ?, ?)";
                $stmt_insert_student = $conn->prepare($sql);
                if ($stmt_insert_student) {
                    $stmt_insert_student->bind_param("isss", $id, $name, $email, $department);

                    if ($stmt_insert_student->execute()) {
                        // Insert into attendance table
                        $sql = "INSERT INTO attendance (student_id, total_classes, present, absent, percentage) VALUES (?, 0, 0, 0, 0)";
                        $stmt_insert_attendance = $conn->prepare($sql);
                        if ($stmt_insert_attendance) {
                            $stmt_insert_attendance->bind_param("i", $id);

                            if ($stmt_insert_attendance->execute()) {
                                $conn->commit();
                                $success_message = "New student registered successfully!";
                            } else {
                                $conn->rollback();
                                $error_message = "Error inserting into attendance: " . $stmt_insert_attendance->error;
                            }
                            $stmt_insert_attendance->close();
                        } else {
                            $conn->rollback();
                            $error_message = "Error preparing attendance statement: " . $conn->error;
                        }
                    } else {
                        $conn->rollback();
                        $error_message = "Error inserting into students: " . $stmt_insert_student->error;
                    }
                    $stmt_insert_student->close();
                } else {
                    $conn->rollback();
                    $error_message = "Error preparing students statement: " . $conn->error;
                }
            }
            $stmt_check->close(); // Close the initial SELECT statement
        } else {
            $error_message = "Error preparing SELECT statement: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New Student</title>
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
        .message {
            text-align: center;
            margin-bottom: 1rem;
            padding: 0.8rem;
            border-radius: 5px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        <h1>Register New Student</h1>

        <!-- Display Success or Error Message -->
        <?php if (!empty($success_message)) : ?>
            <div class="message success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)) : ?>
            <div class="message error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="POST" action="">
            <label for="id">Student ID:</label>
            <input type="text" name="id" id="id" required>

            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="department">Department:</label>
            <select name="department" id="department" required>
                <option value="">--Select Department--</option>
                <option value="Computer Science">Computer Science</option>
                <option value="Electrical Engineering">Electrical Engineering</option>
                <option value="Mechanical Engineering">Mechanical Engineering</option>
            </select>

            <button type="submit">Register</button>
        </form>

        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>