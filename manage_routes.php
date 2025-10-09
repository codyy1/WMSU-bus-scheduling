<?php
include '../db_connect.php'; 
// 1. Check Admin Authentication (Redirect if not logged in)

// 2. Handle POST request for adding a new Route
if (isset($_POST['add_route'])) {
    // Get RouteName, StartLocation, EndLocation
    // Run: INSERT INTO Routes (RouteName, StartLocation, EndLocation) VALUES (?, ?, ?);
    // Set success message
}

// 3. Handle POST request for adding a new Stop
if (isset($_POST['add_stop'])) {
    // Get StopName, Description, Latitude, Longitude
    // Run: INSERT INTO Stops (StopName, Description, Latitude, Longitude) VALUES (?, ?, ?, ?);
    // Set success message
}

// 4. Handle POST request for defining Route Timetable (RouteStops)
// This is complex: you would typically use JavaScript to allow an admin to add multiple stops dynamically, then submit an array of StopID, StopOrder, and ScheduledTime for a single RouteID.

// 5. Fetch Data for Display
$all_routes = $conn->query("SELECT * FROM Routes ORDER BY RouteName");
$all_stops = $conn->query("SELECT * FROM Stops ORDER BY StopName");

?>

<div class="container">
    <h1>Route & Stop Management</h1>
    <h2>Add New Route</h2>
    <form method="POST">
        <button type="submit" name="add_route" class="btn">Save Route</button>
    </form>
    
    <h2>Add New Stop Location</h2>
    <form method="POST">
        <button type="submit" name="add_stop" class="btn">Save Stop</button>
    </form>
    
    <hr>

    <h2>Define Route Timetable (RouteStops)</h2>
    <p>Select a route and sequence its stops with their planned arrival times.</p>
    <h2>Existing Routes</h2>
    <table>
        </table>
</div>