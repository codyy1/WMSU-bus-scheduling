<?php
include __DIR__ . '/db_connect.php'; 

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: index.php");
    exit();
}
$admin_id = $_SESSION['user_id'];

$message = '';


if (isset($_POST['publish_announcement'])) {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $sql = "INSERT INTO Announcements (Title, Content, CreatedBy) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $admin_id);
    if ($stmt->execute()) {
        $message = "Announcement published successfully! ✅";
    } else {
        $message = "Error publishing announcement: " . $conn->error;
    }
}


$announcements_query = "
    SELECT a.*, u.FirstName 
    FROM Announcements a 
    JOIN Users u ON a.CreatedBy = u.UserID 
    ORDER BY PublishDate DESC
";
$announcements_result = $conn->query($announcements_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Announcements - WMSU Admin</title>
    <link rel="stylesheet" href="../user/styles/styles.css">
    <style>
        .alert-success { background:#e8f5e9; padding:10px; border-radius:4px; margin-bottom:10px; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
    <h1>Announcements Management</h1>
    <?php if ($message): ?>
        <div class="alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <h2>Publish New Announcement</h2>
    <form method="POST">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Content</label>
        <textarea id="content" name="content" rows="4" required></textarea>
        
        <button type="submit" name="publish_announcement" class="btn">Publish</button>
    </form>
    
    <hr>

    <h2>Existing Announcements</h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Content Snippet</th>
                <th>Published By</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($announcements_result && $announcements_result->num_rows > 0): ?>
                <?php while ($row = $announcements_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Title']); ?></td>
                    <td><?php echo htmlspecialchars(substr($row['Content'], 0, 50)) . '...'; ?></td>
                    <td><?php echo htmlspecialchars($row['FirstName']); ?></td>
                    <td><?php echo date("Y-m-d h:i A", strtotime($row['PublishDate'])); ?></td>
                    <td>
                        <a href="edit_announcement.php?id=<?php echo $row['AnnouncementID']; ?>" class="btn" style="background-color: orange;">Edit</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">No announcements have been published yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
