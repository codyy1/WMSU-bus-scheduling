<?php

include __DIR__ . '/db_connect.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $wmsuid = $_POST['wmsuid'];
    $password = $_POST['password']; 

    $sql = "SELECT UserID, UserType, PasswordHash FROM Users WHERE WMSUID = ? AND UserType = 'Admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $wmsuid);
    $stmt->execute();
    $stmt->bind_result($userId, $userType, $passwordHash);

    if ($stmt->fetch()) {
        if ($password === $passwordHash) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_type'] = $userType;
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid WMSU ID or Password.";
        }
    } else {
        $message = "Invalid WMSU ID or Password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - WMSU Transport</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
<header>
</header>
<div class="container" style="max-width: 400px; margin-top: 100px;">
    <h2>WMSU Transport Admin Login</h2>
    <?php if ($message): ?><p style="color:red;"><?php echo $message; ?></p><?php endif; ?>
    <form method="POST" class="login-form">
        <div class="form-row">
            <label for="wmsuid">WMSU ID</label>
            <input type="text" id="wmsuid" name="wmsuid" required>
        </div>

        <div class="form-row">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-row form-actions">
            <button type="submit" class="btn">Login</button>
        </div>
    </form>
</div>
</body>
</html>
