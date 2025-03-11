<?php

session_start();

include 'db_connection.php';


echo '<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Articles</title>
        <link rel="stylesheet" href="styles.css">
      </head>';


if (isset($_SESSION['user_id'])) {
    echo '<a href="post_article.php" class="post-article-button">Post New Article</a>';
}


$sql_articles = "SELECT * FROM articles WHERE approved = 1"; 
$result_articles = $conn->query($sql_articles);


if ($result_articles->num_rows > 0) {
    while ($article = $result_articles->fetch_assoc()) {
        $article_id = $article['id'];
        echo "<h2>" . htmlspecialchars($article['title']) . "</h2>";
        echo "<p>" . nl2br(htmlspecialchars($article['content'])) . "</p>";


        $sql_comments = "SELECT * FROM comments WHERE article_id = ? AND approved = 1 ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql_comments);
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $result_comments = $stmt->get_result();
        
        echo "<h3>Comments</h3>";
        while ($row = $result_comments->fetch_assoc()) {
            echo "<div class='comment'>";
            echo "<p><strong>" . htmlspecialchars($row['name']) . "</strong> on " . $row['created_at'] . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($row['comment'])) . "</p>";
            echo "</div>";
        }

        if (isset($_SESSION['user_id'])) { 
            $username = $_SESSION['username'];
            echo "
            <h4>Leave a Comment</h4>
            <form action='comment.php' method='POST'>
                <input type='hidden' name='article_id' value='" . $article_id . "'>
                <input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>
                <input type='hidden' id='name' name='name'value='" . htmlspecialchars($username) . "'>
                <label for='comment'>Comment:</label><br>
                <textarea id='comment' name='comment' rows='4' required></textarea><br><br>
                <input type='submit' value='Submit'>
            </form>";
        } else {
            echo "<p>You must be logged in to leave a comment.</p>";
        }
    }
} else {
    echo "<p>No articles found.</p>";
}

$conn->close();
?>
