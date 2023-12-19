<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: access, Authorization');
header('Access-Control-Allow-Methods: DELETE');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwtHandler.php';
require_once __DIR__ . '/sendJson.php';


function getUserIDFromToken() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decodedData = decodeToken($token);
        return $decodedData->userID;
    }
    return null;
}

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $userID = getUserIDFromToken();

    if (!$userID) {
        sendJson(401, 'Unauthorized');
        exit();
    }

    $stmtDelete = $conn->prepare("DELETE FROM users WHERE userID = :userID");
    $stmtDelete->bindParam(':userID', $userID);

    if ($stmtDelete->execute()) {

        sendJson(200, 'User deleted successfully.');
    } else {
        sendJson(500, 'Something went wrong.');
    }
} else {
    sendJson(405, 'Invalid Request Method. HTTP method should be DELETE');
}
?>
