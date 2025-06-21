<?php
// Include your database connection file
include 'connectdb.php';

// Get the data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO sendmessages (message, sender_username, receiver_username, created_at) VALUES (?, ?, ?, NOW())");

// Bind the parameters
$stmt->bind_param("sss", $data['message'], $data['sender'], $data['receiver']);

// Execute the statement
if ($stmt->execute()) {
  // Message inserted successfully
  echo json_encode(['success' => true]);
} else {
  // Error inserting message
  echo json_encode(['success' => false]);
}

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();
?>