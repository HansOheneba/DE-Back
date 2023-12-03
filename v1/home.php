<?php
require "./vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$secret_key = "12345678"; 

$jwt = null;

$headers = apache_request_headers();

if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    $token = explode(" ", $authHeader);

    if (count($token) == 2) {
        $jwt = $token[1];
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Invalid token"));
        exit();
    }
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Token not provided"));
    exit();
}

if ($jwt) {
    try {
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

        http_response_code(200);
        echo json_encode(array("message" => "Token is valid", "data" => $decoded->data));
        // Perform your desired function here using $decoded->data

    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(array("message" => "Token is invalid", "error" => $e->getMessage()));
    }
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Token not provided"));
}
?>
