<?php
// Setup for JSON output and error reporting
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read JSON input
$inData = json_decode(file_get_contents('php://input'), true);
$UID = isset($inData["UID"]) ? intval($inData["UID"]) : 0;

if ($UID <= 0) {
    echo json_encode(["error" => "Invalid or missing UID."]);
    exit();
}

// Connect to DB
$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

// Get user's University_ID
$userQuery = $conn->prepare("SELECT University_ID FROM Users WHERE UID = ?");
$userQuery->bind_param("i", $UID);
$userQuery->execute();
$userResult = $userQuery->get_result();
if ($userResult->num_rows === 0) {
    echo json_encode(["error" => "User not found."]);
    exit();
}
$universityID = $userResult->fetch_assoc()["University_ID"];
$userQuery->close();

// Get RSOs the user is already in
$joinedRSOs = [];
$joined = $conn->prepare("
    SELECT RSO.RSOID, RSO.Name, RSO.Description
    FROM RSOs RSO
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

// Get RSOs at the same university that user is NOT in
$availableRSOs = [];
$available = $conn->prepare("
    SELECT RSOID, Name, Description
    FROM RSO
    WHERE University_ID = ?
    AND RSOID NOT IN (SELECT RSOID FROM RSO_Members WHERE UID = ?)
");
$available->bind_param("ii", $universityID, $UID);
$available->execute();
$availableResult = $available->get_result();
while ($row = $availableResult->fetch_assoc()) {
    $availableRSOs[] = $row;
}
$available->close();

// Final output
echo json_encode([
    "joined_rsos" => $joinedRSOs,
    "available_rsos" => $availableRSOs
]);

$conn->close();
?>
