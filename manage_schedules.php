<?php
include __DIR__ . '/db_connect.php';

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



$routes = $conn->query("SELECT RouteID, RouteName FROM Routes ORDER BY RouteName");


$schedule_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $route_id = $_POST['route_id'] ?? '';
    $vehicle_id = $_POST['vehicle_id'] ?? '';
    $driver_name = trim($_POST['driver_name'] ?? '');
    $date_of_service = $_POST['date_of_service'] ?? '';
    $status = $_POST['status'] ?? 'On Time';
    if ($route_id && $vehicle_id && $driver_name && $date_of_service && $status) {
        $stmt = $conn->prepare("INSERT INTO Schedules (RouteID, VehicleID, DriverName, DateOfService, Status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $route_id, $vehicle_id, $driver_name, $date_of_service, $status);
        if ($stmt->execute()) {
            $schedule_message = "Schedule added successfully!";
        } else {
            $schedule_message = "Error adding schedule: " . $conn->error;
        }
        $stmt->close();
    } else {
        $schedule_message = "Please fill in all schedule fields.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Schedules - WMSU Transport</title>
    <link rel="stylesheet" href="../user/styles/styles.css">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
    <h1>Manage Daily Schedules</h1>
    

    <h2>Publish New Trip</h2>
    <?php if ($schedule_message): ?><div class="alert-success"><?php echo htmlspecialchars($schedule_message); ?></div><?php endif; ?>
    <form method="POST" class="login-form" style="margin-bottom:2rem;">
        <div class="form-row">
            <label for="route_id">Route</label>
            <select id="route_id" name="route_id" required>
                <option value="">Select Route</option>
                <?php while ($r = $routes->fetch_assoc()): ?>
                    <option value="<?php echo $r['RouteID']; ?>"><?php echo htmlspecialchars($r['RouteName']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-row">
            <label for="vehicle_id">Vehicle</label>
            <select id="vehicle_id" name="vehicle_id" required>
                <option value="">Select Vehicle</option>
                <option value="bus">WMSU Bus</option>
                <option value="van">Van</option>
                <option value="jeepney">Jeepney</option>
            </select>
        </div>
        <div class="form-row">
            <label for="driver_name">Driver Name</label>
            <input type="text" id="driver_name" name="driver_name" required>
        </div>
        <div class="form-row">
            <label for="date_of_service">Date of Service</label>
            <input type="date" id="date_of_service" name="date_of_service" required value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-row">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="On Time">On Time</option>
                <option value="Delayed">Delayed</option>
                <option value="Canceled">Canceled</option>
            </select>
        </div>
        <button type="submit" name="add_schedule" class="btn">Add Schedule</button>
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
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
