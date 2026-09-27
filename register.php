<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? $_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($full_name) || empty($email) || empty($password)) {
        header("Location: register.html?error=" . urlencode("Please fill in all required fields."));
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.html?error=" . urlencode("Please enter a valid email address."));
        exit;
    }

    if (!empty($confirm_password) && $password !== $confirm_password) {
        header("Location: register.html?error=" . urlencode("Passcodes do not match."));
        exit;
    }

    try {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);

        if ($stmt->fetch()) {
            header("Location: register.html?error=" . urlencode("An account with this email already exists."));
            exit;
        }

        // Securely hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Save user to existing users table (full_name, email, password)
        $insert = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (:full_name, :email, :password)");
        $insert->execute([
            ':full_name' => $full_name,
            ':email' => $email,
            ':password' => $hashed_password
        ]);

        // Redirect to login page upon success
        header("Location: login.html?success=" . urlencode("Account created successfully. Please sign in."));
        exit;

    } catch (PDOException $e) {
        header("Location: register.html?error=" . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
} else {
    // If accessed via GET, redirect to register.html
    header("Location: register.html");
    exit;
}
?>
