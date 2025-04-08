<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit();
}

$events = [];
$query = $conn->prepare("SELECT * FROM Events WHERE EventType = 'Public'");
$query->execute();
$result = $query->get_result();

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode(["public_events" => $events]);

$query->close();
$conn->close();
?>
