<?php

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 
}

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $title = $_POST['title'];
    $content = $_POST['content'];
    

    if (empty($title) || empty($content)) {
        echo "Title and content are required!";
    } else {
        $title = $conn->real_escape_string($title);
        $content = $conn->real_escape_string($content);

        $sql = "INSERT INTO articles (title, content) VALUES ('$title', '$content')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Article posted successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Article</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Post a New Article</h1>
    <form action="post_article.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>
        
        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="10" required></textarea><br><br>
        
        <input type="submit" value="Submit">
    </form>
</div>
    <br>
    <a href="article.php">Back to Article</a>
</body>
</html>

<?php
$conn->close(); 
?>
