<?php
$inData = json_decode(file_get_contents('php://input'), true);
$UID = $inData["UID"];

$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed."]));
}

// Get user's University_ID
$userResult = $conn->prepare("SELECT University_ID FROM Users WHERE UID = ?");
$userResult->bind_param("i", $UID);
$userResult->execute();
$userRow = $userResult->get_result()->fetch_assoc();
$universityID = $userRow["University_ID"];

// Get RSOs the user is already in
$joinedRSOs = [];
$joined = $conn->prepare("SELECT R.* FROM RSOs R
    JOIN RSO_Members RM ON R.RSOID = RM.RSOID
    WHERE RM.UID = ?");
$joined->bind_param("i", $UID);
$joined->execute();
$joinedResult = $joined->get_result();
while ($row = $joinedResult->fetch_assoc()) {
    $joinedRSOs[] = $row;
}

// Get RSOs at the user's university that the user is NOT in
$availableRSOs = [];
$available = $conn->prepare("
    SELECT * FROM RSOs
    WHERE University_ID = ?
    AND RSOID NOT IN (
        SELECT RSOID FROM RSO_Members WHERE UID = ?
    )
");
$available->bind_param("ii", $universityID, $UID);
$available->execute();
$availableResult = $available->get_result();
while ($row = $availableResult->fetch_assoc()) {
    $availableRSOs[] = $row;
}

echo json_encode([
    "joined_rsos" => $joinedRSOs,
    "available_rsos" => $availableRSOs
]);

$joined->close();
$available->close();
$conn->close();
?>
