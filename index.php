<?php
include '../db_connect.php';

// Login logic for Student/Staff (similar to admin/index.php but checks for UserType != 'Admin')

// Simplified login form goes here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login - WMSU Transport</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container" style="max-width: 400px; margin-top: 100px;">
    <h2>WMSU Transport User Portal</h2>
    <form method="POST" action="">
        <label for="wmsuid">WMSU ID</label>
        <input type="text" id="wmsuid" name="wmsuid" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <button type="submit" class="btn">Login</button>
    </form>
</div>
</body>
</html>