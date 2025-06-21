<?php
session_start();

include 'connectdb.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); 
    exit();
}

header('Content-Type: application/json');

$username = $_SESSION['username'];
$comment = trim($_POST['comment']);
$postId = intval($_POST['post_id']);

if (empty($postId) || empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input: post_id or comment is missing.']);
    exit();
}

// Insert the comment into the database
$stmt = $conn->prepare("INSERT INTO comments (post_id, username, comment) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit();
}
$stmt->bind_param("iss", $postId, $username, $comment);

if ($stmt->execute()) {
    $commentId = $stmt->insert_id;

    $stmt = $conn->prepare("SELECT c.comment, c.created_at, u.firstname, u.lastname, u.profile_picture 
                            FROM comments c 
                            JOIN users u ON c.username = u.username 
                            WHERE c.id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("i", $commentId);
    $stmt->execute();
    $stmt->bind_result($commentText, $commentCreatedAt, $commentFirstName, $commentLastName, $commentProfilePicture);

    if ($stmt->fetch()) {
      $commentProfilePicture = $commentProfilePicture ?: 'images/default.jpg';
        echo json_encode([
            'success' => true,
            'comment' => [
                'text' => htmlspecialchars($commentText),
                'createdAt' => $commentCreatedAt,
                'firstName' => $commentFirstName ?: 'Unknown',
                'lastName' => $commentLastName ?: 'Unknown',
                'profilePicture' => $commentProfilePicture
            ]
        ]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Comment saved successfully']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving comment: ' . $stmt->error]);
}
$stmt->close();
?>
