<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "my_database_php";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);    
}

?>