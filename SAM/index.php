<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Search</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Student Attendance Management</h1>
        <form method="GET" action="profile.php">
            <input type="text" name="id" placeholder="Enter Student ID" required>
            <button type="submit">Search</button>
        </form>
    </div>
</body>
</html>
