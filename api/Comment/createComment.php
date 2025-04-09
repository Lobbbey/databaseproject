<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read JSON input
$rawInput = file_get_contents('php://input');
$inData = json_decode($rawInput, true);

$EventID = isset($inData["Event_ID"]) ? intval($inData["Event_ID"]) : 0;
$UserID = isset($inData["User_ID"]) ? intval($inData["User_ID"]) : 0;
$CommentText = isset($inData["Comment_Text"]) ? $inData["Comment_Text"] : null;

if ($EventID <= 0 || $UserID <= 0 || $CommentText === null) {
    echo json_encode(["error" => "Invalid or missing Event_ID, User_ID, or Comment_Text."]);
    exit();
}

// Connect to the database
$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

// Insert the comment into the database
$insertComment = $conn->prepare("
    INSERT INTO Comments (Event_ID, User_ID, CommentText, Timestamp)
    VALUES (?, ?, ?, NOW())
");
$insertComment->bind_param("iis", $EventID, $UserID, $CommentText);
if ($insertComment->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Failed to add comment."]);
}
$insertComment->close();
$conn->close();
?>