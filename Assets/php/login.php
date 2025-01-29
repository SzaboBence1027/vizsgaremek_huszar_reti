<?php

session_start();
$data = json_decode(file_get_contents('php://input'), true);

require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Get input values
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }

    // Fetch user by email
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }

    // Set session variables
    $_SESSION['user_id'] = $user['userid'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_name'] = $user['firstname'];
    $_SESSION['logged_in'] = true;

    // Successful login
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'logged_in' => true,
        'user_id' => $_SESSION['user_id'],
        'email' => $_SESSION['email']
    ]);
}
?>