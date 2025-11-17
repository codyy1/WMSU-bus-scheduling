<?php

include __DIR__ . '/../admin/db_connect.php';


$announcements_query = "SELECT a.*, u.FirstName, u.LastName FROM Announcements a JOIN Users u ON a.CreatedBy = u.UserID ORDER BY PublishDate DESC";
$announcements_result = $conn->query($announcements_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WMSU Bus Announcements</title>
    <link rel="stylesheet" href="../user/styles/styles.css">
    <style>
        .announcement-card { border-radius:8px; padding:16px; margin-bottom:16px; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
        .announcement-meta { font-size:0.9rem; color:#666; margin-top:10px; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
    <h1>Official WMSU Bus Announcements</h1>

    <?php if ($announcements_result && $announcements_result->num_rows > 0): ?>
        <?php while ($row = $announcements_result->fetch_assoc()): ?>
            <div class="announcement-card">
                <h3><?php echo htmlspecialchars($row['Title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($row['Content'])); ?></p>
                <div class="announcement-meta">
                    Published: <?php echo date("F j, Y, g:i A", strtotime($row['PublishDate'])); ?>
                    by <?php echo htmlspecialchars(trim($row['FirstName'] . ' ' . $row['LastName'])); ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>There are no current announcements from the WMSU Transport Office. Please check the schedules page for trip information.</p>
    <?php endif; ?>

</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
