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
//findthe university they are in
$universityID = $conn->prepare("
    SELECT University_ID FROM Users WHERE UID = ?
");
$rsoEvents = [];
//TODO: Get events from rso they belong too
$events = $conn->prepare("
    SELECT E.Event_ID, E.Name, E.Catagory, E.Description, E.Time, E.Date,E.EventType, E.Lname, E.Phone, E.Email, E.RSOID
    FROM Events E
    JOIN RSO_Members RM ON E.RSOID = RM.RSOID
    WHERE RM.UID = ?
    AND E.Time > NOW()
    ORDER BY E.Time ASC
");
$events->bind_param("i", $UID);
$events->execute();
$eventsResult = $events->get_result();
while ($row = $eventsResult->fetch_assoc()) {
    $rsoEvents[] = $row;
}
$events->close();
//get university events
$universityEvents = [];
$uEvents = $conn->prepare("
    SELECT E.Event_ID, E.Name, E.Catagory, E.Description, E.Time, E.Date,E.EventType, E.Lname, E.Phone, E.Email, E.RSOID
    FROM Events E
    JOIN RSO ON RSO.RSOID = E.RSOID
    WHERE RSO.University_ID = (SELECT University_ID FROM Users WHERE UID = ?)
    AND E.Time > NOW()
    ORDER BY E.Time ASC
");
$uEvents->bind_param("i", $UID);
$uEvents->execute();
$uEventsResult = $uEvents->get_result();
while ($row = $uEventsResult->fetch_assoc()) {
    $universityEvents[] = $row;
}
$uEvents->close();
//get public events
$publicEvents = [];
$pEvents = $conn->prepare("
    SELECT E.Event_ID, E.Name, E.Catagory, E.Description, E.Time, E.Date,E.EventType, E.Lname, E.Phone, E.Email, E.RSOID
    FROM Events E
    WHERE E.EventType = 'Public'
    AND E.Time > NOW()
    ORDER BY E.Time ASC
");
$pEvents->execute();
$pEventsResult = $pEvents->get_result();
while ($row = $pEventsResult->fetch_assoc()) {
    $publicEvents[] = $row;
}
$pEvents->close();

$conn->close();
// Return the data as JSON  
echo json_encode([
    "joinedRSOs" => $joinedRSOs,
    "rsoEvents" => $rsoEvents,
    "universityEvents" => $universityEvents,
    "publicEvents" => $publicEvents
]);
?>
