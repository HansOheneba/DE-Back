<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once __DIR__ . '/db.php'; 
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
    if (
        !isset($data->name) ||
        !isset($data->username) ||
        !isset($data->email) ||
        !isset($data->password) ||
        empty(trim($data->name)) ||
        empty(trim($data->username)) ||
        empty(trim($data->email)) ||
        empty(trim($data->password))
    ) {
        sendJson(
            422,
            'Please fill all the required fields & None of the fields should be empty.',
            ['required_fields' => ['name', 'username', 'email', 'password']]
        );
    }

    $name = htmlspecialchars(trim($data->name));
    $username = htmlspecialchars(trim($data->username));
    $email = trim($data->email);
    $password = trim($data->password);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJson(422, 'Invalid Email Address!');
    } elseif (strlen($password) < 8) {
        sendJson(422, 'Your password must be at least 8 characters long!');
    } elseif (strlen($name) < 3) {
        sendJson(422, 'Your name must be at least 3 characters long!');
    }

    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT `email`, `username` FROM `users` WHERE `email` = :email OR `username` = :username");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false && $row['email'] == $email) {
        sendJson(422, 'This E-mail is already in use!');
    }
    if ($row !== false && $row['username'] == $username) {
        sendJson(422, 'This Username is already taken!');
    }
    

    $stmt = $conn->prepare("INSERT INTO `users`(`name`, `username`, `email`, `password`) VALUES(:name, :username, :email, :password)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hash_password);

    if ($stmt->execute()) {
        sendJson(201, 'You have successfully registered.');
    } else {
        sendJson(500, 'Something going wrong.');
    }
}

sendJson(405, 'Invalid Request Method. HTTP method should be POST');
?>
