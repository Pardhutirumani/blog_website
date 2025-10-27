<?php
// Database connection
$myconn = mysqli_connect('localhost', 'root', '', 'blog');
if (!$myconn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize message variable
$message = "";

// Handle new post submission
if (isset($_POST['submit'])) {
    $post = trim($_POST['post']);
    if (!empty($post)) {
        // Insert post into the database
        $stmt = $myconn->prepare("INSERT INTO blog_post (post, status) VALUES (?, 'pending')");
        $stmt->bind_param("s", $post);

        if ($stmt->execute()) {
            // ✅ Success message
            $message = "<div class='success'>✅ Post submitted successfully! Waiting for admin approval.</div>";
        } else {
            $message = "<div class='error'>❌ Something went wrong. Please try again.</div>";
        }

        $stmt->close();
    } else {
        $message = "<div class='error'>⚠️ Please enter a post before submitting.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit a Post</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 40px; }
        h2 { color: #333; text-align: center; }
        form { background: #fff; padding: 20px; border-radius: 8px; width: 500px; margin: 20px auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        textarea { width: 100%; height: 120px; margin-bottom: 15px; padding: 10px; font-family: Arial; font-size: 14px; border: 1px solid #ccc; border-radius: 5px; }
        button { background: #007BFF; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; }
        button:hover { background: #0056b3; }
        .success, .error {
            width: 500px;
            margin: 10px auto;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <h2>Submit a New Post</h2>

    <!-- ✅ Display message here -->
    <?php if (!empty($message)) echo $message; ?>

    <form method="POST" action="index.html">
        <textarea name="post" placeholder="Write your post here..." required></textarea>
        <button type="submit" name="submit">Submit</button>
    </form>

</body>
</html>
