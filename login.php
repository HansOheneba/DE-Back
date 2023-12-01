<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwtHandler.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if (
        !isset($data->username) ||
        !isset($data->password) ||
        empty(trim($data->username)) ||
        empty(trim($data->password))
    ) {
        sendJson(
            422,
            'Please fill all the required fields & None of the fields should be empty.',
            ['required_fields' => ['username', 'password']]
        );
    }

    $username = trim($data->username);
    $password = trim($data->password);

    $sql = "SELECT * FROM `users` WHERE `username` = :username";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row === null) {
        sendJson(404, 'User not found! (Username is not registered)');
    } elseif (!password_verify($password, $row['password'])) {
        sendJson(401, 'Incorrect Password!');
    } else {
        $userData = isset($row['id']) ? ['id' => $row['id']] : [];
        sendJson(200, '', [
            'token' => encodeToken($userData)
        ]);
    }
    
}

sendJson(405, 'Invalid Request Method. HTTP method should be POST');
?>
