<?php
include '../db_connect.php'; 
// 1. Check Admin Authentication (Redirect if not logged in)
$admin_id = $_SESSION['user_id'];

// 2. Handle POST request for publishing a new announcement
$message = '';
if (isset($_POST['publish_announcement'])) {
    // Get Title and Content
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    $sql = "INSERT INTO Announcements (Title, Content, CreatedBy) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $admin_id);
    if ($stmt->execute()) {
        $message = "Announcement published successfully!";
    } else {
        $message = "Error publishing announcement.";
    }
}

// 3. Fetch all Announcements for display
$announcements_query = "SELECT a.*, u.FirstName FROM Announcements a JOIN Users u ON a.CreatedBy = u.UserID ORDER BY PublishDate DESC";
$announcements_result = $conn->query($announcements_query);
?>

<div class="container">
    <h1>Announcements Management</h1>
    <?php if ($message): ?><div class="alert-success"><?php echo $message; ?></div><?php endif; ?>

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
                <th>Published By</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $announcements_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['Title']; ?></td>
                <td><?php echo $row['FirstName']; ?></td>
                <td><?php echo date("Y-m-d h:i A", strtotime($row['PublishDate'])); ?></td>
                <td>
                    <a href="edit_announcement.php?id=<?php echo $row['AnnouncementID']; ?>" class="btn">Edit</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
