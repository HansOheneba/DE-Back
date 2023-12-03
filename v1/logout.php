<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwtHandler.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST'):

    $headers = apache_request_headers();

    if (!isset($headers['Authorization'])) {
        sendJson(401, 'Authorization header is missing.');
    }

    $token = $headers['Authorization'];


    $dbService = new DatabaseService();
    $connection = $dbService->getConnection();
    try {
        $token = preg_replace('/^Bearer\s+/i', '', $token);

        $userData = decodeToken($token);


        $activityType = 'logged_out';
        $stmtActivity = $connection->prepare("INSERT INTO user_activity (userID, activityType, timestamp) VALUES (:userID, :activityType, NOW())");
        $stmtActivity->bindParam(':userID', $userData->{'userID'});
        $stmtActivity->bindParam(':activityType', $activityType);
        $stmtActivity->execute();

        sendJson(200, 'Logout successful.');
    } catch (Exception $e) {
        sendJson(401, 'Invalid token: ' . $e->getMessage());
    }
endif;

sendJson(405, 'Invalid Request Method. HTTP method should be POST');


