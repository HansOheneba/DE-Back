<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwtHandler.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST'):

    $data = json_decode(file_get_contents('php://input'));

    if (
        !isset($data->email) ||
        !isset($data->password) ||
        empty(trim($data->email)) ||
        empty(trim($data->password))
    ):
        sendJson(
            422,
            'Please fill all the required fields, none of the fields should be empty.',
            ['required_fields' => ['email', 'password']]
        );
    endif;

    $dbService = new DatabaseService();
    $connection = $dbService->getConnection();

    $email = trim($data->email);
    $password = trim($data->password);

    if (strlen($password) < 8):
        sendJson(422, 'Your password must be at least 8 characters long!');
    endif;
    $sql = "SELECT * FROM users WHERE email=:email";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row === false) {
        sendJson(404, 'User not found! (email is not registered)');
    }

    if (!password_verify($password, $row['password'])) {
        sendJson(401, 'Incorrect Password!');
    }

    // User credentials are valid, generate a JWT token
    $userData = [
        'userID' => $row['userID'],
        'username' => $row['username'],
        'name' => $row['name'],
        'email' => $row['email'],
        'dateJoined' => date('Y-m-d H:i:s', strtotime($row['dateJoined'])),
    ];

    $activityType = 'logged_in';
    $stmtActivity = $connection->prepare("INSERT INTO user_activity (userID, activityType, timestamp) VALUES (:userID, :activityType, NOW())");
    $stmtActivity->bindParam(':userID', $userData['userID']);
    $stmtActivity->bindParam(':activityType', $activityType);
    $stmtActivity->execute();


    $token = encodeToken($userData);

    sendJson(200, 'Logged in Successfully', [
        'token' => $token
    ]);

endif;

sendJson(405, 'Invalid Request Method. HTTP method should be POST');
?>