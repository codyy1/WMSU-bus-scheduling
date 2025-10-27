<?php
// Shared header/navigation
// Expects session to be started by the including file
$current = basename($_SERVER['PHP_SELF']);
$isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Admin';
?>
<header>
    <nav>
        <?php if ($isAdmin): ?>
            <a href="/WMSUBUS/admin/dashboard.php" <?php if (strpos($current, 'dashboard') !== false) echo 'class="active"'; ?>>Dashboard</a>
            <a href="/WMSUBUS/admin/manage_routes.php" <?php if (strpos($current, 'manage_routes') !== false) echo 'class="active"'; ?>>Routes & Stops</a>
            <a href="/WMSUBUS/admin/manage_schedules.php" <?php if (strpos($current, 'manage_schedules') !== false) echo 'class="active"'; ?>>Schedules</a>
            <a href="/WMSUBUS/admin/announcements.php" <?php if (strpos($current, 'announcements') !== false) echo 'class="active"'; ?>>Announcements</a>
            <a href="/WMSUBUS/admin/logout.php">Logout</a>
        <?php else: ?>
            <a href="/WMSUBUS/user/schedule_view.php" <?php if (strpos($current, 'schedule_view') !== false) echo 'class=\"active\"'; ?>>Schedules</a>
            <a href="/WMSUBUS/user/announcements_user.php" <?php if (strpos($current, 'announcements_user') !== false) echo 'class=\"active\"'; ?>>Announcements</a>
            <a href="/WMSUBUS/user/logout_user.php">Logout</a>
        <?php endif; ?>
    </nav>
</header>
