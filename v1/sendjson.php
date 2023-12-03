<?php
function sendJson($status, $message, $data = null)
{
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code($status);

    $response = array('status' => $status, 'message' => $message, 'data' => $data);

    echo json_encode($response);
    exit;
}
