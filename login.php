<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$email = trim($_POST['email'] ?? $_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
if ($email === '' || $password === '') {
    header('Location: login.html?error=' . urlencode('Please enter both email and passcode.'));
    exit;
}

try {
    $statement = $conn->prepare('SELECT id, full_name, email, password FROM users WHERE email = :email LIMIT 1');
    $statement->execute([':email' => $email]);
    $user = $statement->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        header('Location: login.html?error=' . urlencode('Incorrect email or passcode.'));
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    header('Location: index.html');
    exit;
} catch (PDOException $error) {
    header('Location: login.html?error=' . urlencode('Unable to sign in right now. Please try again.'));
    exit;
}