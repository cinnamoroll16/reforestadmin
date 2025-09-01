<?php
session_start();
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: signup.html");
  exit;
}

// 1) Get & sanitize input
$name  = trim($_POST['name'] ?? "");
$email = trim($_POST['email'] ?? "");
$pass  = $_POST['password'] ?? "";
$role  = trim($_POST['role'] ?? "");

// 2) Basic validation
$errors = [];

if ($name === "")                  $errors[] = "Name is required.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
if (strlen($pass) < 6)            $errors[] = "Password must be at least 6 characters.";
$allowed_roles = ["user","admin"];
if (!in_array($role, $allowed_roles, true))     $errors[] = "Please select a valid role.";

if (!empty($errors)) {
  // Show simple error page
  echo "<h3>Fix the following:</h3><ul>";
  foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>";
  echo "</ul><p><a href='signup.html'>&larr; Go back</a></p>";
  exit;
}

// 3) Check if email already exists
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
  echo "<h3>Email is already registered.</h3><p><a href='signin.html'>Sign in</a> or <a href='signup.html'>use another email</a>.</p>";
  $stmt->close();
  $conn->close();
  exit;
}
$stmt->close();

// 4) Insert user (with hashed password)
$hash = password_hash($pass, PASSWORD_DEFAULT);

$insert = "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)";
$ins = $conn->prepare($insert);
$ins->bind_param("ssss", $name, $email, $hash, $role);

if ($ins->execute()) {
  // Success → go to sign in page
  header("Location: signin.html?registered=1");
  exit;
} else {
  echo "<h3>Something went wrong while creating your account.</h3><p>Error: "
     . htmlspecialchars($conn->error)
     . "</p><p><a href='signup.html'>&larr; Try again</a></p>";
}

$ins->close();
$conn->close();
