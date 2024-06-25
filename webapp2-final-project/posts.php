<?php
require 'config.php';


// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Retrieve the user ID from the session
$user_id = $_SESSION['user_id'];

// DSN and PDO options
$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $user, $password, $options);

    // Check if a search query is provided
    if (isset($_GET['search'])) {
        $search_query = $_GET['search'];
        // Prepare a query to search posts by user_id and title
        $sql_query = "SELECT * FROM `posts` WHERE user_id = :ID AND title LIKE :search";
        $stm = $pdo->prepare($sql_query);
        $stm->execute([':ID' => $user_id, ':search' => '%' . $search_query . '%']);
    } else {
        // Query to fetch all posts by user_id
        $sql_query = "SELECT * FROM `posts` WHERE user_id = :ID";
        $stm = $pdo->prepare($sql_query);
        $stm->execute([':ID' => $user_id]);
        $search_query = ''; // Initialize search_query to avoid undefined variable warning
    }
} catch (PDOException $e) {
    // Handle PDO exceptions
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Posts</title>
  <link rel="stylesheet" href="styles/post.css">
</head>
<body>
  <div class="container">
    <div class="page-title">
      POSTS
      <form method="GET" action="posts.php">
        <div class="backDrop">
          <input type="text" id="SearchBar" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search_query); ?>" />
          <label for="search" aria-placeholder="Search"></label>
        </div>
      </form>
      <form method="POST" action="logout.php" style="display:inline;">
        <button type="submit" id="logoutBtn">Logout</button>
      </form>
    </div>
    <div class="post-container">
      <ul id="postLists">
        <?php
        // Loop through and display the posts
        while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
            echo '<li data-id="' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</li>';
        }
        ?>
      </ul>
    </div>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Back to Top &UpArrow;</button>
  </div>

  <script>
    // JavaScript to handle post clicks and scrolling
    document.addEventListener("DOMContentLoaded", function() {
        const postLists = document.getElementById("postLists");
        postLists.addEventListener("click", function(event) {
            if (event.target.tagName === "LI") {
                const id = event.target.getAttribute("data-id");
                window.location.href = `details.php?id=${id}`;
            }
        });
    });

    let mybutton = document.getElementById("myBtn");
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        mybutton.style.display = "block";
      } else {
        mybutton.style.display = "none";
      }
    }
    function topFunction() {
      window.scrollTo(0, 0);
    }
  </script>
</body>
</html>