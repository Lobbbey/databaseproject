<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get data
$inData = json_decode(file_get_contents('php://input'), true);

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
$RSOID = $inData["RSOID"] ?? null;

$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["result" => "Database connection failed"]);
    exit();
}

// Optional: check if location exists (foreign key)
$checkLocation = $conn->prepare("SELECT Name FROM Locations WHERE Name = ?");
$checkLocation->bind_param("s", $Lname);
$checkLocation->execute();
$checkLocation->store_result();
if ($checkLocation->num_rows === 0) {
    echo json_encode(["result" => "Invalid location name"]);
    exit();
}
$checkLocation->close();

// Prepare insert
$query = $conn->prepare("
    INSERT INTO Events 
    (Name, Catagory, Description, Time, Date, EventType, Lname, Phone, Email, RSOID)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$query->bind_param(
    "sssssssssi",
    $Name, $Catagory, $Description, $Time, $Date,
    $EventType, $Lname, $Phone, $Email, $RSOID
);

if ($query->execute()) {
    echo json_encode(["result" => "success"]);
} else {
    echo json_encode(["result" => "Error: " . $query->error]);
}

$query->close();
$conn->close();
?>
