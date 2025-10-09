<?php
include '../db_connect.php';

// User authentication check (if logged in, optional for viewing public schedules)

// 1. Fetch all routes
$routes_result = $conn->query("SELECT RouteID, RouteName FROM Routes WHERE IsActive = TRUE");

// 2. Fetch the detailed schedule for the first route found (or a selected one)
$selected_route_id = isset($_GET['route_id']) ? $_GET['route_id'] : 
    ($routes_result->num_rows > 0 ? $routes_result->fetch_assoc()['RouteID'] : null);
$routes_result->data_seek(0); // Reset pointer for dropdown display

$schedule_details = null;
if ($selected_route_id) {
    // Get the static stops and times for the selected route
    $sql = "SELECT rs.StopOrder, s.StopName, rs.ScheduledTime
            FROM RouteStops rs
            JOIN Stops s ON rs.StopID = s.StopID
            WHERE rs.RouteID = ?
            ORDER BY rs.StopOrder";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $selected_route_id);
    $stmt->execute();
    $schedule_details = $stmt->get_result();

    // Get today's assignment (Vehicle/Driver/Status) for this route
    $today = date("Y-m-d");
    $assignment_sql = "SELECT s.DriverName, v.PlateNumber, s.Status
                       FROM Schedules s
                       JOIN Vehicles v ON s.VehicleID = v.VehicleID
                       WHERE s.RouteID = ? AND s.DateOfService = ?";
    $assign_stmt = $conn->prepare($assignment_sql);
    $assign_stmt->bind_param("is", $selected_route_id, $today);
    $assign_stmt->execute();
    $assignment = $assign_stmt->get_result()->fetch_assoc();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WMSU Bus Schedule</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
    <nav>
        <a href="schedule_view.php">Schedules</a>
        <a href="announcements.php">Announcements</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>
<div class="container">
    <h1>WMSU Bus Schedule - Daily View</h1>

    <form method="GET" style="margin-bottom: 20px;">
        <label for="route_id">Select Route:</label>
        <select id="route_id" name="route_id" onchange="this.form.submit()">
            <?php while ($row = $routes_result->fetch_assoc()): ?>
                <option value="<?php echo $row['RouteID']; ?>" 
                    <?php if ($row['RouteID'] == $selected_route_id) echo 'selected'; ?>>
                    <?php echo $row['RouteName']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>
    
    <?php if ($selected_route_id && $schedule_details->num_rows > 0): ?>
        <h2>Route Details: 
            <?php echo $assignment ? $assignment['RouteName'] : 'Selected Route'; ?>
        </h2>
        
        <?php if ($assignment): ?>
            <p><strong>Today's Assignment (<?php echo date("F j, Y"); ?>):</strong></p>
            <ul>
                <li>Vehicle Plate: **<?php echo $assignment['PlateNumber']; ?>**</li>
                <li>Driver: **<?php echo $assignment['DriverName']; ?>**</li>
                <li>Current Status: **<?php echo $assignment['Status']; ?>**</li>
            </ul>
        <?php else: ?>
            <p style="color:red;">**No trip assigned to this route for today.**</p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Stop Name</th>
                    <th>Scheduled Time (Static)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $schedule_details->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['StopOrder']; ?></td>
                    <td><?php echo $row['StopName']; ?></td>
                    <td>**<?php echo date("h:i A", strtotime($row['ScheduledTime'])); ?>**</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php elseif ($routes_result->num_rows === 0): ?>
        <p>No routes are currently defined in the system.</p>
    <?php endif; ?>
</div>
</body>
</html>