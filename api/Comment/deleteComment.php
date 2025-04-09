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
    Select Event_ID, User_ID FROM Comments WHERE Comment_ID = ?");
$findComment->bind_param("i", $CommentID);
$findComment->execute();
$findCommentResult = $findComment->get_result();

$comment = $findCommentResult->fetch_assoc()["Event_ID", "User_ID"] ?? null;
$findComment->close();
echo json_encode(["comment" => $comment]);

$deleteComment = $conn->prepare("
    DELETE FROM Comments WHERE Comment_ID = ?");
$deleteComment->bind_param("i", $CommentID);
$deleteComment->execute();
$deleteComment->close();
$conn->close();
