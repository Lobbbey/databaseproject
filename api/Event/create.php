<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read JSON input
$inData = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required = ["Name", "Catagory", "Description", "Time", "Date", "EventType", "Location", "Phone", "Email"];
foreach ($required as $field) {
    if (empty($inData[$field])) {
        echo json_encode(["result" => "Missing field: $field"]);
        exit();
    }
}

// Assign input values
$UID = $inData["UID"];
$Name = $inData["Name"];
$Catagory = $inData["Catagory"];
$Description = $inData["Description"];
$Time = $inData["Time"];
$Date = $inData["Date"];
$EventType = $inData["EventType"];
$Lname = $inData["Location"];
$Phone = $inData["Phone"];
$Email = $inData["Email"];
$RSOID = isset($inData["RSOID"]) ? $inData["RSOID"] : null;

// Connect to DB
$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["result" => "DB connection failed"]);
    exit();
}

// Check that location exists
$checkLoc = $conn->prepare("SELECT Name FROM Locations WHERE Name = ?");
$checkLoc->bind_param("s", $Lname);
$checkLoc->execute();
$checkLoc->store_result();
if ($checkLoc->num_rows === 0) {
    echo json_encode(["result" => "Invalid location"]);
    exit();
}
$checkLoc->close();

// Insert Event
$stmt = $conn->prepare("
    INSERT INTO Events 
    (Name, Catagory, Description, Time, Date, EventType, Lname, Phone, Email, RSOID)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("sssssssssi", 
    $Name, $Catagory, $Description, $Time, $Date, 
    $EventType, $Lname, $Phone, $Email, $RSOID
);

if ($stmt->execute()) {
    echo json_encode(["result" => "success"]);
} else {
    echo json_encode(["result" => "MySQL error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
