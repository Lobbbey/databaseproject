<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Parse input
$inData = json_decode(file_get_contents('php://input'), true);
$UID = isset($inData["UID"]) ? intval($inData["UID"]) : 0;

if ($UID <= 0) {
    echo json_encode(["error" => "Invalid UID"]);
    exit();
}

// Connect to DB
$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Fetch RSO events where user is a member
$sql = "
    SELECT E.*
    FROM Events E
    JOIN RSO_Members RM ON E.RSOID = RM.RSOID
    WHERE E.EventType = 'RSO' AND RM.UID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $UID);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$stmt->close();
$conn->close();

// Output wrapped result
echo json_encode(["rEvents" => $events]);
?>
