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

$userName = null;
$userQuery = $conn->prepare("SELECT Name FROM Users WHERE UID = ?");
$userQuery->bind_param("i", $UID);
$userQuery->execute();
$userResult = $userQuery->get_result();

$userName = $userResult->fetch_assoc()["Name"] ?? null;
$userQuery->close();
echo json_encode(["Name" => $userName]);
$conn->close();