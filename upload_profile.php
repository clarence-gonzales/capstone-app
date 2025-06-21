<?php
session_start();
include 'connectdb.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$username = $_SESSION['username'];

if ($_FILES['profile_picture']['error'] == 0) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = $username . "_" . time() . "." . pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE username = ?");
        $stmt->bind_param("ss", $targetFile, $username);
        $stmt->execute();
        $stmt->close();

        echo json_encode(["success" => true, "newImageUrl" => $targetFile]);
    } else {
        echo json_encode(["success" => false, "message" => "Upload failed"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No file uploaded"]);
}
?>
