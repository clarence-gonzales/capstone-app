<?php
session_start();
include "connectdb.php";

if (isset($_SESSION['user_details'])) {
    $outgoing_id = trim($_POST['outgoing_id']);
    $incoming_id = trim($_POST['incoming_id']);
    $output = "";

    $stmt = $conn->prepare("SELECT *, profile_picture 
    FROM messagess m 
    LEFT JOIN users u ON m.outgoing_msg_id = u.username 
    WHERE (outgoing_msg_id = ? AND incoming_msg_id = ?) 
    OR (outgoing_msg_id = ? AND incoming_msg_id = ?)
    ORDER BY msg_id ASC");

    if ($stmt) {
        $stmt->bind_param("iiii", $outgoing_id, $incoming_id, $incoming_id, $outgoing_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['outgoing_msg_id'] == $outgoing_id) {
                    $output .= '<div class="chat outgoing">
                                  <div class="details">
                                    <p>' . htmlspecialchars($row['msg']) . '</p>
                                  </div>
                                </div>';
                } else {
                    $profilePic = !empty($row['profile_picture']) ? htmlspecialchars($row['profile_picture']) : 'images/default.jpg';
                    $output .= '<div class="chat incoming">
                                  <img src="' . $profilePic . '" alt="">
                                  <div class="details">
                                    <p>' . htmlspecialchars($row['msg']) . '</p>
                                  </div>
                                </div>';
                }
            }
            echo $output;
        }
        $stmt->close();
    } else {
        error_log("Error preparing statement: " . $conn->error);
        echo "An error occurred. Please try again later.";
    }
} else {
    header("Location:../login.php");
}

?>