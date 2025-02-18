<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: manage_attendance.php");  // Redirect if already logged in
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted data
    $private_code = $_POST['private_code'];

    // Check if the private code matches the predefined code for a CR/Professor
    if ($private_code === 'nubtk') {
        // Example: You can differentiate roles by the private code (for now, all users have the same role)
        $_SESSION['user'] = ['private_code' => $private_code, 'role' => 'professor']; // Role could be 'CR' or 'professor'
        header("Location: manage_attendance.php");  // Redirect to the attendance management page
        exit();
    } else {
        // Invalid private code
        $error = "Invalid private code!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($error) { echo "<p style='color:red;'>$error</p>"; } ?>
    <form method="POST" action="">
        <label for="private_code">Private Code:</label>
        <input type="password" name="private_code" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
