<?php
include 'connectdb.php';

$postId = $_GET['post_id'] ?? null;
if (!$postId) {
  echo json_encode([]);
  exit();
}

$comments = [];

$stmt = $conn -> prepare("SELECT c.comment, c.created_at, u.firstname, u.lastname, u.profile_picture FROM comments c JOIN users u ON c.username = u.username WHERE c.post_id = ? ORDER BY c.created_at ASC");
$stmt -> bind_param("i", $postId);

if ($stmt) {
  $stmt -> execute();
  $stmt -> bind_result($commentText, $commentCreatedAt, $commentFirstName, $commentLastName, $commentProfilePicture);

  while ($stmt -> fetch()) {
    $comments[] = [
      'text' => $commentText,
      'createdAt' => $commentCreatedAt,
      'firstName' => $commentFirstName ?? 'Unknown',
      'lastName' => $commentLastName ?? 'Unknown',
      'profilePicture' => $commentProfilePicture ?? 'images/default.jpg'
    ];
  }
  $stmt -> close();
}

header('Content-Type: application/json');
echo json_encode($comments);
exit();

?>