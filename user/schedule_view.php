<?php
include __DIR__ . '/../admin/db_connect.php';


$routes_result = $conn->query("SELECT RouteID, RouteName FROM Routes WHERE IsActive = TRUE");


$selected_route_id = isset($_GET['route_id']) ? $_GET['route_id'] : 
    ($routes_result->num_rows > 0 ? $routes_result->fetch_assoc()['RouteID'] : null);
$routes_result->data_seek(0); 
$schedule_rows = [];
$assignment = null;
$selected_route_name = null;
if ($selected_route_id) {
    // Load stops for the route (avoid get_result dependency)
    $sql = "SELECT rs.StopOrder, s.StopName, rs.ScheduledTime
            FROM RouteStops rs
            JOIN Stops s ON rs.StopID = s.StopID
            WHERE rs.RouteID = ?
            ORDER BY rs.StopOrder";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $selected_route_id);
    $stmt->execute();
    $stmt->bind_result($stopOrder, $stopName, $scheduledTime);
    while ($stmt->fetch()) {
        $schedule_rows[] = ['StopOrder' => $stopOrder, 'StopName' => $stopName, 'ScheduledTime' => $scheduledTime];
    }
    $stmt->close();

    // Get today's assignment (avoid get_result dependency)
    $today = date("Y-m-d");
    $assignment_sql = "SELECT s.DriverName, v.PlateNumber, s.Status
                       FROM Schedules s
                       JOIN Vehicles v ON s.VehicleID = v.VehicleID
                       WHERE s.RouteID = ? AND s.DateOfService = ? LIMIT 1";
    $assign_stmt = $conn->prepare($assignment_sql);
    $assign_stmt->bind_param("is", $selected_route_id, $today);
    $assign_stmt->execute();
    $assign_stmt->bind_result($driverName, $plateNumber, $status);
    if ($assign_stmt->fetch()) {
        $assignment = ['DriverName' => $driverName, 'PlateNumber' => $plateNumber, 'Status' => $status];
    }
    $assign_stmt->close();

    // Get route name for display
    $route_q = $conn->prepare("SELECT RouteName FROM Routes WHERE RouteID = ? LIMIT 1");
    $route_q->bind_param("i", $selected_route_id);
    $route_q->execute();
    $route_q->bind_result($rname);
    if ($route_q->fetch()) $selected_route_name = $rname;
    $route_q->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WMSU Bus Schedule</title>
    <link rel="stylesheet" href="../user/styles/styles.css">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
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
    
    <?php if ($selected_route_id && count($schedule_rows) > 0): ?>
        <h2>Route Details: 
            <?php echo $selected_route_name ? htmlspecialchars($selected_route_name) : 'Selected Route'; ?>
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
                <?php foreach ($schedule_rows as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['StopOrder']); ?></td>
                    <td><?php echo htmlspecialchars($row['StopName']); ?></td>
                    <td>**<?php echo date("h:i A", strtotime($row['ScheduledTime'])); ?>**</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php elseif ($routes_result->num_rows === 0): ?>
        <p>No routes are currently defined in the system.</p>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>