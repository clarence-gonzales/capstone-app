<?php
include 'connectdb.php';
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$username = $_SESSION['username'];
$post_id = $_POST['post_id'];
$comment = $_POST['comment'];

$stmt = $conn->prepare("INSERT INTO post_comments (post_id, username, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $post_id, $username, $comment);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Comment added']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add comment']);
}
?>