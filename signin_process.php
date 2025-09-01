<?php
session_start();
include "db_connect.php"; // include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    // 1) Get user by email
    $sql = "SELECT id, email, password_hash, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // 2) Verify password
        if (password_verify($pass, $row['password_hash'])) {
            // Success → save session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email']   = $row['email'];
            $_SESSION['role']    = $row['role'];

            header("Location: home.html");
            exit();
        } else {
            echo "<script>alert('Invalid Password'); window.location.href='signin.html';</script>";
        }
    } else {
        echo "<script>alert('Email not found'); window.location.href='signin.html';</script>";
    }
}
?>
