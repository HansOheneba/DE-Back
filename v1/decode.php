<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwtHandler.php';
require_once __DIR__ . '/sendJson.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postData = file_get_contents("php://input");


    $json_data = json_decode($postData);


    if ($json_data && isset($json_data->token)) {

        $token = $json_data->token;


        $decodedData = decodeToken($token);


        $responseData = [
            'userID' => $decodedData->userID,
            'username' => $decodedData->username,
            'name' => $decodedData->name,
            'email' => $decodedData->email,
            'dateJoined' => $decodedData->dateJoined
        ];


        header('Content-Type: application/json');

        echo json_encode($responseData);
    } else {

        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
    }
} else {

    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
