<?php

include 'db_connection.php';

session_start(); // ca sa urmareasca util pe mai multe pag


//CSRF (Cross-Site Request Forgery) - nu te lasa sa accesezi pagina fara sa faci formularul de login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['login'])){
        $username = htmlspecialchars(trim($_POST['username'])); //htmlspecial.. previne atacurile XSS - converteste caractere speciale (ex <,>, etc) in entitati HTML
        $password = htmlspecialchars(trim($_POST['password']));

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username); //S=STRING (leaga username-ul de interogare)
        $stmt->execute();
        $stmt->store_result(); //stocheaza rez

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $stored_password); //leaga rez interogarii de acele variabile
            $stmt->fetch(); // populeaza rez(cel de sus doar le leaga)
            if (password_verify($password, $stored_password)) { //verifica parolele hasuite
                $_SESSION['user_id'] = true;
                $_SESSION['username'] = $username;
                error_log("User logged in successfully. Redirecting...");
                header("Location: http://localhost/MyPHP/article.php");
                exit();
            } else {
                echo "<p>Invalid username or password.</p>";
            }
        } else {
            echo "<p>Invalid username or password.</p>";
        }
        $stmt->close(); //inchidem conexiunea la baza de date
    }

    if(isset($_POST['register'])){
        $new_username = htmlspecialchars(trim($_POST['new_username']));
        $new_password = htmlspecialchars(trim($_POST['new_password']));

        // Verifică dacă numele de utilizator există deja
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $new_username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Dacă există deja, afișează un mesaj de eroare
            echo "<p>Username already taken. Please choose another one.</p>";
        } else {
            // Dacă nu există, hash-uiește parola și inserează datele în baza de date
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $new_username, $hashed_password);

            if($stmt->execute()){
                echo "<p>User registered successfully!</p>";
            } else{
                echo "<p>Registration failed. Please try again.</p>";
            }
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
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="submit" name="login" value="Login">
    </form> 

    <hr>

    <h2>Register</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="new_username">Username:</label><br>
        <input type="text" id="new_username" name="new_username" required><br><br>
        <label for="new_password">Password:</label><br>
        <input type="password" id="new_password" name="new_password" required><br><br>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="submit" name="register" value="Register">
    </form> 


</div>
</body>
</html>