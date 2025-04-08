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

$sql = "
    SELECT E.*
    FROM Events E
    INNER JOIN RSO_Members RM ON E.RSOID = RM.RSOID
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

echo json_encode(["events" => $events]);
$stmt->close();
$conn->close();
?>
