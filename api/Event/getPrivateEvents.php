<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$inData = json_decode(file_get_contents('php://input'), true);
$UID = isset($inData["UID"]) ? intval($inData["UID"]) : 0;

$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit();
}

// Get user's university
$uniID = null;
$userQuery = $conn->prepare("SELECT University_ID FROM Users WHERE UID = ?");
$userQuery->bind_param("i", $UID);
$userQuery->execute();
$userResult = $userQuery->get_result();
if ($row = $userResult->fetch_assoc()) {
    $uniID = $row['University_ID'];
}
$userQuery->close();

// Get private events for user's university
$privateEvents = [];
if ($uniID !== null) {
    $query = $conn->prepare("SELECT * FROM Events WHERE EventType = 'Private' AND RSOID IS NULL");
    $query->execute();
    $result = $query->get_result();
    while ($row = $result->fetch_assoc()) {
        $privateEvents[] = $row;
    }
    $query->close();
}

$conn->close();
echo json_encode(["private_events" => $privateEvents]);
?>
