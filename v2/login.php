<?php

require 'vendor/autoload.php';
require 'config/services.php';

use Firebase\JWT\JWT;

function encodeToken($data)
{
    $key = '+hz8qMTie9xy4eZFUeaJSHhlo3fvFAIWimZYEqsO42c=';

    $token = array(
        'iss' => 'http://localhost/DE-Back/v2/login ',
        'iat' => time(),
        'exp' => time() + 3600, // 1hr
        'data' => $data
    );

    return JWT::encode($token, $key, 'HS256');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $connection = $dbService->getConnection();

    // Obtain username and password from the POST request
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Sanitize and validate input
    $username = trim($username);
    $password = trim($password);

    if (empty($username) || empty($password)) {
        echo json_encode(['error' => 'Username and password are required.']);
        exit;
    }

    $sql = "SELECT * FROM `users` WHERE `username`= ?";
    $stmt = $connection->prepare($sql);
    $stmt->execute([$username]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $data = array(
            'userID' => $user['userID'],
            'username' => $user['username'],
        );

        $token = encodeToken($data);

        echo json_encode(['token' => $token]);
    } else {

        echo json_encode(['error' => 'Invalid username or password']);
    }
} else {
  
    echo json_encode(['error' => 'Invalid request method. Use POST.']);
}
