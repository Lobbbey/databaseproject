<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read JSON input
$rawInput = file_get_contents('php://input');
$inData = json_decode($rawInput, true);
$CommentID = isset($inData["Comment_ID"]) ? intval($inData["Comment_ID"]) : 0;
$CommentText = isset($inData["Comment_Text"]) ? $inData["Comment_Text"] : null;
if ($CommentID <= 0 || $CommentText === null) {
    echo json_encode(["error" => "Invalid or missing Comment_ID or Comment_Text."]);
    exit();
}

$conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

// Update the comment text and timestamp
$editComment = $conn->prepare("
    UPDATE Comments 
    SET CommentText = ?, Timestamp = NOW() 
    WHERE CommentID = ?
");
$editComment->bind_param("si", $CommentText, $CommentID);
$editComment->execute();
$editComment->close();

// Fetch the updated comment details
$findComment = $conn->prepare("
    SELECT Event_ID, User_ID 
    FROM Comments 
    WHERE CommentID = ?
");
$findComment->bind_param("i", $CommentID);
$findComment->execute();
$findCommentResult = $findComment->get_result();
$comment = $findCommentResult->fetch_assoc();
if ($comment) {
    echo json_encode([
        "CommentText" => $CommentText,
        "User_ID" => $comment["User_ID"],
        "success" => true
    ]);
} else {
    echo json_encode(["error" => "Comment not found."]);
}
$findComment->close();
$conn->close();
?>