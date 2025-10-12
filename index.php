<?php
// include the central DB connection (located in admin folder)
include __DIR__ . '/../admin/db_connect.php';

$message = '';

// Check if user is already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'Admin') {
    // Already logged in as student/staff, redirect to schedule view
    header("Location: schedule_view.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $wmsuid = $_POST['wmsuid'] ?? '';
    $password = $_POST['password'] ?? '';

    // Look for a user with the provided WMSU ID who is NOT an admin
    $sql = "SELECT UserID, UserType, PasswordHash FROM Users WHERE WMSUID = ? AND UserType != 'Admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $wmsuid);
    $stmt->execute();
    $stmt->bind_result($userId, $userType, $passwordHash);

    if ($stmt->fetch()) {
        // Found the user, verify password
        if ($password === $passwordHash) { // In production, use password_verify()
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_type'] = $userType;
            $stmt->close();
            
            // Redirect to schedule view after successful login
            header("Location: schedule_view.php");
            exit();
        } else {
            $message = "Invalid WMSU ID or Password";
        }
    } else {
        $message = "Invalid WMSU ID or Password";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login - WMSU Transport</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
<div class="container" style="max-width: 400px; margin-top: 100px;">
    <h2>WMSU Transport User Portal</h2>
    <?php if ($message): ?>
        <p style="color:red;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    
    <form method="POST" action="" class="login-form">
        <div class="form-row">
            <label for="wmsuid">WMSU ID</label>
            <input type="text" id="wmsuid" name="wmsuid" required 
                   value="<?php echo isset($_POST['wmsuid']) ? htmlspecialchars($_POST['wmsuid']) : ''; ?>">
        </div>
        
        <div class="form-row">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-row">
            <button type="submit" class="btn">Login</button>
        </div>
    </form>
</div>
</body>
</html>