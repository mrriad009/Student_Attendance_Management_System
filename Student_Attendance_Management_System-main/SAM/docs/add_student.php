<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $class = $_POST['class'];

    $stmt = $conn->prepare("INSERT INTO students (name, class) VALUES (?, ?)");
    $stmt->bind_param('ss', $name, $class);

    if ($stmt->execute()) {
        echo "<p>Student added successfully! <a href='dashboard.php'>Go back</a></p>";
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Add New Student</h1>
    </header>
    <main>
        <form action="add_student.php" method="post">
            <label for="name">Student Name:</label>
            <input type="text" name="name" required>
            <label for="class">Class:</label>
            <input type="text" name="class" required>
            <button type="submit">Add Student</button>
        </form>
    </main>
</body>
</html>
