<?php

session_start();

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $article_id = $_POST['article_id'];
    $name = htmlspecialchars(trim($_POST['name']));
    $comment = htmlspecialchars(trim($_POST['comment']));

    if (empty($name) || empty($comment)) {
        die("Name and comment cannot be empty");
    }

    $sql = "INSERT INTO comments (article_id, name, comment, created_at, approved) VALUES (?, ?, ?, NOW(), 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $article_id, $name, $comment);

    if ($stmt->execute()) {
        header("Location: article.php?id=" . $article_id . "&message=Comment submitted for approval");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
