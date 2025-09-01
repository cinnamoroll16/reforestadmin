<?php
$servername = "localhost";
$username   = "root";   // XAMPP default
$password   = "";       // XAMPP default is empty
$database   = "mydb";   // your DB name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
