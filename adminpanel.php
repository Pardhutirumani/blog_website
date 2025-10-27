<?php
session_start();
// Uncomment these two lines once you have a login system
// if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }

$myconn = mysqli_connect('localhost', 'root', '', 'blog');
if (!$myconn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle actions securely
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'accept' || $action === 'reject') {
        $status = ($action === 'accept') ? 'accepted' : 'rejected';
        $stmt = $myconn->prepare("UPDATE blog_post SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'delete') {
        $stmt = $myconn->prepare("DELETE FROM blog_post WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: adminpanel.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial; background-color: #f4f4f4; padding: 30px; }
        h2 { color: #333; }
        .post { background: white; padding: 20px; margin-bottom: 15px; border-left: 5px solid #007BFF; border-radius: 5px; }
        .status { font-weight: bold; color: gray; }
        .actions { margin-top: 10px; }
        .actions a {
            text-decoration: none; padding: 5px 10px; margin-right: 10px;
            border-radius: 3px; color: white;
        }
        .accept { background: seagreen; }
        .reject { background: crimson; }
        .delete { background: darkgray; }
    </style>
</head>
<body>

<h2>Admin Panel</h2>

<?php
// LIFO order (latest first)
$result = mysqli_query($myconn, "SELECT * FROM blog_post ORDER BY id DESC");

while ($row = mysqli_fetch_assoc($result)) {
    $postId = htmlspecialchars($row['id']);
    $status = htmlspecialchars($row['status']);
    $postText = htmlspecialchars($row['post']);
    $shortPost = (strlen($postText) > 150) ? substr($postText, 0, 150) . '...' : $postText;

    echo "<div class='post'>";
    echo "<p><strong>ID:</strong> {$postId}</p>";
    echo "<p><strong>Status:</strong> {$status}</p>";
    echo "<p>{$shortPost}</p>";

    echo "<div class='actions'>";
    if ($status === 'pending') {
        echo "<a class='accept' href='admin.php?action=accept&id={$postId}'>Accept</a>";
        echo "<a class='reject' href='admin.php?action=reject&id={$postId}'>Reject</a>";
    }
    echo "<a class='delete' href='admin.php?action=delete&id={$postId}' onclick=\"return confirm('Delete this post?')\">Delete</a>";
    echo "</div></div>";
}

mysqli_close($myconn);
?>
</body>
</html>
