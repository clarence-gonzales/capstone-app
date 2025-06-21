<?php
session_start();
include 'connectdb.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$username = $_SESSION['username'];
$postId = $_POST['post_id'];

$stmt = $conn->prepare("SELECT id FROM likes WHERE post_id = ? AND username = ?");
$stmt->bind_param("is", $postId, $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Unlike the post
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND username = ?");
    $stmt->bind_param("is", $postId, $username);
    $stmt->execute();

    // Decrease the like count
    $stmt = $conn->prepare("UPDATE posts SET like_count = like_count - 1 WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();

    echo json_encode(['success' => true, 'action' => 'unliked']);
} else {
    // Like the post
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO likes (post_id, username) VALUES (?, ?)");
    $stmt->bind_param("is", $postId, $username);
    $stmt->execute();

    // Increase the like count
    $stmt = $conn->prepare("UPDATE posts SET like_count = like_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();

    echo json_encode(['success' => true, 'action' => 'liked']);
}
?>