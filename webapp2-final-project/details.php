<?php
// Include the configuration file
require 'config.php';

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// DSN and PDO options
$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $user, $password, $options);

    // Check if a post ID is provided
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        // Query to fetch the post details by ID
        $sql_query = "SELECT * FROM `posts` WHERE id = :id";
        $stm = $pdo->prepare($sql_query);
        $stm->execute([':id' => $id]);
        $post = $stm->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            // Display the post details
            $post_title = htmlspecialchars($post['title']);
            $post_body = htmlspecialchars($post['body']);
        } else {
            // Display an error message if the post is not found
            $post_title = "Post not found";
            $post_body = "No post found with the ID of {$id}.";
        }
    } else {
        // Display an error message if no post ID is provided
        $post_title = "No post ID provided!";
        $post_body = "No post ID was provided in the request.";
    }
} catch (PDOException $e) {
    // Handle PDO exceptions
    $post_title = "Database error";
    $post_body = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Details</title>
  <link rel="stylesheet" href="styles/details.css">
</head>
<body>
  <div class="container">
    <h1><?php echo $post_title; ?></h1>
    <div id="postDetails">
        <p><?php echo $post_body; ?></p>
    </div>
    <button id="back" onclick="goBack()"> &LeftArrow; Back to Posts</button>
  </div>
  <script>
    // JavaScript function to go back to the posts page
    function goBack() {
      window.history.back();
    }
  </script>
</body>
</html>