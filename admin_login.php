<?php

include 'db_connection.php';

session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['login'])){
        $username = htmlspecialchars(trim($_POST['username'])); 
        $password = htmlspecialchars(trim($_POST['password']));

        $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username); 
        $stmt->execute();
        $stmt->store_result(); 

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $stored_password); 
            $stmt->fetch(); 
            if (password_verify($password, $stored_password)) { 
                $_SESSION['admin'] = true;
                error_log("User logged in successfully. Redirecting...");
                header("Location: http://localhost/MyPHP/admin_page.php");
                exit();
            } else {
                echo "<p>Invalid username or password.</p>";
            }
        } else {
            echo "<p>Invalid username or password.</p>";
        }
        $stmt->close();
    }

    if(isset($_POST['register'])){
        $new_username = htmlspecialchars(trim($_POST['new_username']));
        $new_password = htmlspecialchars(trim($_POST['new_password']));

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $new_password, $hashed_password);

        if($stmt->execute()){
            echo "<p>User registered successfully!</p>";
        } else{
            echo "<p>Registration faild. Please try again.</p>";
        }
        $stmt->close();
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h2>Admin Login</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="submit" name="login" value="Login">
    </form> 

</div>
</body>
</html>