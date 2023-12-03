<?php
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/sendJson.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

$key = '+hz8qMTie9xy4eZFUeaJSHhlo3fvFAIWimZYEqsO42c=';

function encodeToken($data)
{
    global $key;
    $token = array(
        'iss' => 'http://localhost/DE-Back/v2',
        'iat' => time(),
        'exp' => time() + 3600, // 1hr
        'data' => $data
    );
    return JWT::encode($token, $key, 'HS256');
}

function decodeToken($token)
{
    global $key;
    try {
        $decode = JWT::decode($token, new Key($key, 'HS256'));
        return $decode->data;
    } catch (ExpiredException | SignatureInvalidException $e) {
        sendJson(401, $e->getMessage());
    } catch (UnexpectedValueException | Exception $e) {
        sendJson(400, $e->getMessage());
    }
}