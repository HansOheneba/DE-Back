<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwtHandler.php';
require_once __DIR__ . '/sendJson.php';

$name = '';
$username = '';
$email = '';
$password = '';
$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    
    $name = $data->name;
    $username = $data->username;
    $email = $data->email;
    $password = $data->password;

    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users(name, username, email, password) VALUES(:name, :username, :email, :password)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hash_password);

    if ($stmt->execute()) {
        $userData = [
            'userID' => $conn->lastInsertId(), 
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'dateJoined' => date('Y-m-d H:i:s'),
        ];

        $token = encodeToken($userData);

        sendJson(201, 'You have successfully registered and logged in.', ['token' => $token]);
    } else {
        sendJson(500, 'Something going wrong.');
    }
}

sendJson(405, 'Invalid Request Method. HTTP method should be POST');
?>
