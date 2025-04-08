<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read JSON input
$rawInput = file_get_contents('php://input');
$inData = json_decode($rawInput, true);
$EventID = isset($inData["EventID"]) ? intval($inData["EventID"]) : 0;
if ($EventID <= 0) {
    echo json_encode(["error" => "Invalid or missing EventID."]);
    exit();
}

$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

$eventComments = [];
$commentQuery = $conn->prepare("
    SELECT * FROM Comments WHERE Event_ID = ?");
$commentQuery->bind_param("i", $EventID);
$commentQuery->execute();
$commentResult = $commentQuery->get_result();
if ($commentResult->num_rows === 0) {
    echo json_encode(["error" => "No comments found."]);
    exit();
}
while ($row = $commentResult->fetch_assoc()) {
    $eventComments[] = $row;
}
$commentQuery->close();

echo json_encodeevent("comments" => $eventComments);
$conn->close();
?>
