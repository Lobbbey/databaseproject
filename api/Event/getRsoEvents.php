<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read JSON input
$rawInput = file_get_contents('php://input');
$inData = json_decode($rawInput, true);
$UID = isset($inData["UID"]) ? intval($inData["UID"]) : 0;
if ($UID <= 0) {
    echo json_encode(["error" => "Invalid or missing UID."]);
}

$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

// Get RSOs the user is already in
$joinedRSOs = [];
$joined = $conn->prepare("
    SELECT RSO.RSOID, RSO.Name, RSO.Description
    FROM RSO
    JOIN RSO_Members RM ON RSO.RSOID = RM.RSOID
    WHERE RM.UID = ?
");
$joined->bind_param("i", $UID);
$joined->execute();
$joinedResult = $joined->get_result();
while ($row = $joinedResult->fetch_assoc()) {
    $joinedRSOs[] = $row;
}
$joined->close();

$rsoEvents = [];
$events = $conn->prepare("
    SELECT E.EventID, E.Name, E.Description, E.StartTime, E.EndTime, E.Location, E.RSOID
    FROM Events E
    JOIN RSO_Members RM ON E.RSOID = RM.RSOID
    WHERE RM.UID = ?
    AND E.StartTime > NOW()
    ORDER BY E.StartTime ASC
");
$events->bind_param("i", $UID);
$events->execute();
$eventsResult = $events->get_result();
while ($row = $eventsResult->fetch_assoc()) {
    $rsoEvents[] = $row;
}
$events->close();

$conn->close();
// Return the data as JSON  
echo json_encode([
    "joinedRSOs" => $joinedRSOs,
    "rsoEvents" => $rsoEvents
]);
?>
