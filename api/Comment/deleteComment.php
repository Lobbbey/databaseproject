<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read JSON input
$rawInput = file_get_contents('php://input');
$inData = json_decode($rawInput, true);
$CommentID = isset($inData["Comment_ID"]) ? intval($inData["Comment_ID"]) : 0;
if ($CommentID <= 0) {
    echo json_encode(["error" => "Invalid or missing EventID."]);
    exit();
}

$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}
$deletedComment = null;
$findComment = $conn->prepare("
    Select Event_ID, User_ID FROM Comments WHERE CommentID = ?");
$findComment->bind_param("i", $CommentID);
$findComment->execute();
$findCommentResult = $findComment->get_result();

$comment = $findCommentResult->fetch_assoc();
if ($comment) {
    $eventID = $comment["Event_ID"];
    $userID = $comment["User_ID"];
} else {
    echo json_encode(["error" => "Comment not found."]);
    exit();
}
$findComment->close();

$deleteComment = $conn->prepare("
    DELETE FROM Comments WHERE CommentID = ?");
$deleteComment->bind_param("i", $CommentID);
$deleteComment->execute();
$deleteComment->close();

echo json_encode([
    "Event_ID" => $eventID,
    "User_ID" => $userID,
    "success" => true
]);
$conn->close();
