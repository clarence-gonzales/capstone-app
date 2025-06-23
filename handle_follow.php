<?php
session_start();
include 'connectdb.php'; // Your database connection file

header('Content-Type: application/json'); // Respond with JSON

$response = ['success' => false, 'message' => '', 'buttonText' => 'Follow', 'status' => 0];

// Ensure a user is logged in
if (!isset($_SESSION['unique_id'])) {
    $response['message'] = 'User not logged in.';
    echo json_encode($response);
    exit();
}

$current_user_id = $_SESSION['unique_id']; // Using unique_id consistently
$target_user_id = isset($_POST['target_user_id']) ? intval($_POST['target_user_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : ''; // 'follow', 'unfollow', or 'accept_follow'

// Prevent a user from performing actions on themselves
if ($current_user_id === $target_user_id) {
    $response['message'] = 'You cannot perform this action on yourself.';
    echo json_encode($response);
    exit();
}

if ($target_user_id <= 0) {
    $response['message'] = 'Invalid target user ID.';
    echo json_encode($response);
    exit();
}

if ($action === 'follow') {
    // Check if current user is already following target user (or has a pending request)
    $stmt = $conn->prepare("SELECT status FROM follow_users WHERE follower_id = ? AND following_id = ?");
    $stmt->bind_param("ii", $current_user_id, $target_user_id);
    $stmt->execute();
    $stmt->bind_result($existing_status);
    $stmt->fetch();
    $stmt->close();

    if ($existing_status !== null) {
        // A relationship already exists
        $response['success'] = true; // No error, just no change in state
        if ($existing_status == 1) {
            $response['message'] = 'You are already following this user.';
            $response['buttonText'] = 'Following';
            $response['status'] = 1;
        } else { // $existing_status == 0
            $response['message'] = 'Follow request already sent (pending).';
            $response['buttonText'] = 'Pending';
            $response['status'] = 0;
        }
    } else {
        // No existing relationship, create a new pending follow request (status 0)
        $conn->begin_transaction();
        try {
            $stmt_insert = $conn->prepare("INSERT INTO follow_users (follower_id, following_id, status) VALUES (?, ?, 0)");
            $stmt_insert->bind_param("ii", $current_user_id, $target_user_id);
            $stmt_insert->execute();
            $stmt_insert->close();

            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Follow request sent.';
            $response['buttonText'] = 'Pending';
            $response['status'] = 0;

        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $response['message'] = 'Database error (follow): ' . $e->getMessage();
        }
    }

} elseif ($action === 'accept_follow') {
    // This action means the CURRENT USER is accepting a follow request from the TARGET USER.
    // So, the TARGET USER is the 'follower_id' in the pending request, and CURRENT USER is the 'following_id'.

    $conn->begin_transaction();
    try {
        // 1. Update the status of the INCOMING PENDING request from target_user_id to current_user_id
        $stmt_update_incoming_request = $conn->prepare("UPDATE follow_users SET status = 1 WHERE follower_id = ? AND following_id = ? AND status = 0");
        $stmt_update_incoming_request->bind_param("ii", $target_user_id, $current_user_id);
        $stmt_update_incoming_request->execute();
        $rows_affected = $stmt_update_incoming_request->affected_rows;
        $stmt_update_incoming_request->close();

        if ($rows_affected > 0) { // If an pending request was found and updated
            // 2. Now, ensure CURRENT USER is also FOLLOWING the TARGET USER with status 1 (mutual)
            $stmt_check_outgoing_follow = $conn->prepare("SELECT id FROM follow_users WHERE follower_id = ? AND following_id = ?");
            $stmt_check_outgoing_follow->bind_param("ii", $current_user_id, $target_user_id);
            $stmt_check_outgoing_follow->execute();
            $stmt_check_outgoing_follow->store_result();

            if ($stmt_check_outgoing_follow->num_rows == 0) {
                // Current user was not already following target, so insert the new mutual follow
                $stmt_insert_outgoing_follow = $conn->prepare("INSERT INTO follow_users (follower_id, following_id, status) VALUES (?, ?, 1)");
                $stmt_insert_outgoing_follow->bind_param("ii", $current_user_id, $target_user_id);
                $stmt_insert_outgoing_follow->execute();
                $stmt_insert_outgoing_follow->close();
            } else {
                // Current user was already following target (maybe they unfollowed and re-followed, or status needed update)
                // Ensure their existing follow entry is also set to mutual (status 1)
                $stmt_update_outgoing_follow = $conn->prepare("UPDATE follow_users SET status = 1 WHERE follower_id = ? AND following_id = ?");
                $stmt_update_outgoing_follow->bind_param("ii", $current_user_id, $target_user_id);
                $stmt_update_outgoing_follow->execute();
                $stmt_update_outgoing_follow->close();
            }
            $stmt_check_outgoing_follow->close();

            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Follow request accepted. You are now following each other.';
            $response['buttonText'] = 'Following';
            $response['status'] = 1;

        } else {
            $conn->rollback();
            $response['message'] = 'No pending follow request from this user to accept, or it was already accepted/rejected.';
        }

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $response['message'] = 'Database error (accept follow): ' . $e->getMessage();
    }

} elseif ($action === 'unfollow') {
    // This action means the CURRENT USER is unfollowing the TARGET USER.
    $conn->begin_transaction();
    try {
        // Delete the follow relationship from current_user_id to target_user_id
        $stmt_delete = $conn->prepare("DELETE FROM follow_users WHERE follower_id = ? AND following_id = ?");
        $stmt_delete->bind_param("ii", $current_user_id, $target_user_id);
        $stmt_delete->execute();
        $rows_deleted = $stmt_delete->affected_rows;
        $stmt_delete->close();

        if ($rows_deleted > 0) {
            // If current user was following target, check if target user was following current user
            // If they were, revert their relationship status from mutual (1) to non-mutual (0)
            $stmt_revert_mutual = $conn->prepare("UPDATE follow_users SET status = 0 WHERE follower_id = ? AND following_id = ? AND status = 1");
            $stmt_revert_mutual->bind_param("ii", $target_user_id, $current_user_id);
            $stmt_revert_mutual->execute();
            $stmt_revert_mutual->close();
        }

        $conn->commit();
        $response['success'] = true;
        $response['message'] = 'User unfollowed successfully.';
        $response['buttonText'] = 'Follow';
        $response['status'] = 0;

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $response['message'] = 'Database error (unfollow): ' . $e->getMessage();
    }

} else {
    $response['message'] = 'Invalid action.';
}

echo json_encode($response);
$conn->close();
?>