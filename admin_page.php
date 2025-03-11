<?php

session_start();

if (!isset($_SESSION['admin'])) { 
    header('Location: admin_login.php');
    exit();
}


include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $article_id = $_POST['article_id'];
        $sql = "UPDATE articles SET approved = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $article_id = $_POST['article_id'];
        $sql = "DELETE FROM articles WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
    }
}

$sql = "SELECT * FROM articles WHERE approved = 0";
$pending_articles = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderate Articles</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h2>Moderate Articles</h2>
    <?php while ($row = $pending_articles->fetch_assoc()): ?>
        <div class="article">
        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="article_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="submit" name="approve" value="Approve">
                <input type="submit" name="delete" value="Delete">
            </form>
        </div>
    <?php endwhile; ?>
    <br>
    <a href="article.php">Back to Article</a>
</div>
</body>
</html>

<?php $conn->close(); ?>