<?php
include '../db_connect.php';
// Check if admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - WMSU Transport</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_routes.php">Routes & Stops</a>
        <a href="manage_schedules.php">Schedules</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>
<div class="container">
    <h1>Welcome, WMSU Transport Admin!</h1>
    
    <h2>Quick Links</h2>
    <p>Manage the core data and publish schedules for students and staff.</p>
    <div>
        <a href="manage_routes.php" class="btn">Manage Routes & Stops</a>
        <a href="manage_schedules.php" class="btn">Create/Manage Schedules</a>
    </div>

    <h2>System Overview</h2>
    <p>Total Registered Users: 
        <?php 
            $result = $conn->query("SELECT COUNT(*) AS total FROM Users WHERE UserType IN ('Student', 'Staff')");
            echo $result->fetch_assoc()['total']; 
        ?>
    </p>
    <p>Total Active Vehicles: 
        <?php 
            $result = $conn->query("SELECT COUNT(*) AS total FROM Vehicles WHERE Status = 'Operational'");
            echo $result->fetch_assoc()['total']; 
        ?>
    </p>
    
</div>
</body>
</html>