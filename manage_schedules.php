<?php
include '../db_connect.php';
// Check admin login... (same as dashboard.php)

// Simple logic to display schedules for today
$today = date("Y-m-d");

$sql = "SELECT s.*, r.RouteName, v.PlateNumber
        FROM Schedules s
        JOIN Routes r ON s.RouteID = r.RouteID
        JOIN Vehicles v ON s.VehicleID = v.VehicleID
        WHERE s.DateOfService = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$schedules_result = $stmt->get_result();

// Logic for adding a new schedule (omitted for brevity, but requires: 
// 1. Fetching Route IDs, Vehicle IDs, and an input for Driver Name/Date/Status. 
// 2. An INSERT query into the Schedules table.)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Schedules - WMSU Transport</title>
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
    <h1>Manage Daily Schedules</h1>
    
    <h2>Publish New Trip</h2>
    <form method="POST" action="add_schedule_process.php"> <p>A form to select **Route, Vehicle, Driver Name, and Date** would go here.</p>
        <button type="submit" class="btn" disabled>Add Schedule (Form Omitted)</button>
    </form>

    <hr>
    
    <h2>Today's Published Schedules (<?php echo $today; ?>)</h2>
    <table>
        <thead>
            <tr>
                <th>Trip ID</th>
                <th>Route</th>
                <th>Vehicle Plate</th>
                <th>Driver</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $schedules_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['ScheduleID']; ?></td>
                <td><?php echo $row['RouteName']; ?></td>
                <td><?php echo $row['PlateNumber']; ?></td>
                <td><?php echo $row['DriverName']; ?></td>
                <td><?php echo $row['Status']; ?></td>
                <td>
                    <a href="edit_schedule.php?id=<?php echo $row['ScheduleID']; ?>" class="btn">Edit Status</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if ($schedules_result->num_rows === 0): ?>
            <tr><td colspan="6">No schedules published for today.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>