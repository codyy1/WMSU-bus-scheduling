<?php
include __DIR__ . '/db_connect.php'; 

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header('Location: index.php');
    exit();
}


$route_message = '';
if (isset($_POST['add_route'])) {
    $route_name = trim($_POST['route_name'] ?? '');
    $start_location = trim($_POST['start_location'] ?? '');
    $end_location = trim($_POST['end_location'] ?? '');
    if ($route_name && $start_location && $end_location) {
        $stmt = $conn->prepare("INSERT INTO Routes (RouteName, StartLocation, EndLocation) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $route_name, $start_location, $end_location);
        if ($stmt->execute()) {
            $route_message = "Route added successfully!";
        } else {
            $route_message = "Error adding route: " . $conn->error;
        }
        $stmt->close();
    } else {
        $route_message = "Please fill in all route fields.";
    }
}


$stop_message = '';
if (isset($_POST['add_stop'])) {
    $stop_name = trim($_POST['stop_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');
    if ($stop_name && $description) {
        $stmt = $conn->prepare("INSERT INTO Stops (StopName, Description, Latitude, Longitude) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdd", $stop_name, $description, $latitude, $longitude);
        if ($stmt->execute()) {
            $stop_message = "Stop added successfully!";
        } else {
            $stop_message = "Error adding stop: " . $conn->error;
        }
        $stmt->close();
    } else {
        $stop_message = "Please fill in all stop fields.";
    }
}


$all_routes = $conn->query("SELECT * FROM Routes ORDER BY RouteName");
$all_stops = $conn->query("SELECT * FROM Stops ORDER BY StopName");

?>

<head>
    <meta charset="UTF-8">
    <title>Manage Routes - WMSU Transport</title>
    <link rel="stylesheet" href="../user/styles/styles.css">
</head>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
    <h1>Route & Stop Management</h1>
    <div class="card" style="margin-bottom:2rem;">
        <h2>Add New Route</h2>
        <?php if ($route_message): ?><div class="alert-success"><?php echo htmlspecialchars($route_message); ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-row">
                <label for="route_name">Route Name</label>
                <input type="text" id="route_name" name="route_name" required placeholder="e.g. WMSU Main Gate to City Hall">
            </div>
            <div class="form-row">
                <label for="start_location">Start Location</label>
                <input type="text" id="start_location" name="start_location" required placeholder="e.g. WMSU Main Gate">
            </div>
            <div class="form-row">
                <label for="end_location">End Location</label>
                <input type="text" id="end_location" name="end_location" required placeholder="e.g. City Hall Bus Terminal">
            </div>
            <button type="submit" name="add_route" class="btn">Save Route</button>
        </form>
    </div>

    <div class="card" style="margin-bottom:2rem;">
        <h2>Add New Stop Location</h2>
        <?php if ($stop_message): ?><div class="alert-success"><?php echo htmlspecialchars($stop_message); ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-row">
                <label for="stop_name">Stop Name</label>
                <input type="text" id="stop_name" name="stop_name" required placeholder="e.g. Grandstop Drop Off">
            </div>
            <div class="form-row">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" required placeholder="Short description">
            </div>
            <div class="form-row">
                <label for="latitude">Latitude</label>
                <input type="number" step="any" id="latitude" name="latitude" placeholder="Optional">
            </div>
            <div class="form-row">
                <label for="longitude">Longitude</label>
                <input type="number" step="any" id="longitude" name="longitude" placeholder="Optional">
            </div>
            <button type="submit" name="add_stop" class="btn">Save Stop</button>
        </form>
    </div>

    <div class="card" style="margin-bottom:2rem;">
        <h2>Existing Routes</h2>
        <table>
            <thead>
                <tr>
                    <th>Route Name</th>
                    <th>Start Location</th>
                    <th>End Location</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $all_routes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['RouteName']); ?></td>
                    <td><?php echo htmlspecialchars($row['StartLocation']); ?></td>
                    <td><?php echo htmlspecialchars($row['EndLocation']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Existing Stops</h2>
        <table>
            <thead>
                <tr>
                    <th>Stop Name</th>
                    <th>Description</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $all_stops->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['StopName']); ?></td>
                    <td><?php echo htmlspecialchars($row['Description']); ?></td>
                    <td><?php echo htmlspecialchars($row['Latitude']); ?></td>
                    <td><?php echo htmlspecialchars($row['Longitude']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
