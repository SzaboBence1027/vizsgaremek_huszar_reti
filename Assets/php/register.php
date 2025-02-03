<?php
header('Content-Type: application/json');
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Get input values
    $lastname = $data['lastname'] ?? '';
    $firstname = $data['firstname'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($lastname) || empty($firstname)|| empty($email) || empty($password)) {
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }

    // Insert the new user into the database
    $stmt = $pdo->prepare('INSERT INTO users (lastname,firstname, email, password) VALUES (:lastname,:firstname, :email, :password)');
    $stmt->execute([
        'lastname' => $lastname,
        'firstname'=>$firstname,
        'email' => $email,
        'password' => $hashed_password
    ]);

    echo json_encode(['message' => 'User registered successfully']);
}
?>