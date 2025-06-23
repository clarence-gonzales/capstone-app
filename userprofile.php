<?php
session_start();
include 'connectdb.php'; // Your database connection file

// Ensure current user is logged in
$current_user_id = isset($_SESSION['unique_id']) ? $_SESSION['unique_id'] : null;
$current_username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Determine the username of the profile being viewed
// If no username in GET, it's the current user's own profile.
$profile_username = isset($_GET['username']) ? $_GET['username'] : $current_username;

// If profile_username is still null (e.g., not logged in and no username in GET)
if (!$profile_username) {
    echo "No profile specified and not logged in.";
    exit();
}

// Fetch details of the profile being viewed
$stmt = $conn->prepare("SELECT unique_id, firstname, lastname, profile_picture FROM users WHERE username = ?");
$stmt->bind_param("s", $profile_username);
$stmt->execute();
$stmt->bind_result($target_user_id, $firstname, $lastname, $profilePicture);
$stmt->fetch();
$stmt->close();

// Handle case where target user is not found
if (!$target_user_id) {
    echo "User not found!";
    exit();
}

// Default profile picture
$defaultProfilePicture = "images/default.jpg";
$profilePicture = $profilePicture ? $profilePicture : $defaultProfilePicture;

// --- Follow Status Logic for the Button ---
$follow_button_text = 'Follow'; // Default text
$is_own_profile = ($current_user_id === $target_user_id); // Check if viewing own profile

if ($is_own_profile) {
    $follow_button_text = 'Edit Profile'; // Button for own profile
} else if ($current_user_id) { // Only check follow status if a user is logged in and not on their own profile
    // Check if the CURRENT USER is FOLLOWING the TARGET USER
    $stmt_current_follows_target = $conn->prepare("SELECT status FROM follow_users WHERE follower_id = ? AND following_id = ?");
    $stmt_current_follows_target->bind_param("ii", $current_user_id, $target_user_id);
    $stmt_current_follows_target->execute();
    $stmt_current_follows_target->bind_result($current_follows_target_status);
    $stmt_current_follows_target->fetch();
    $stmt_current_follows_target->close();

    // Check if the TARGET USER is FOLLOWING the CURRENT USER (i.e., current user HAS A PENDING REQUEST FROM target user)
    $stmt_target_follows_current = $conn->prepare("SELECT status FROM follow_users WHERE follower_id = ? AND following_id = ?");
    $stmt_target_follows_current->bind_param("ii", $target_user_id, $current_user_id);
    $stmt_target_follows_current->execute();
    $stmt_target_follows_current->bind_result($target_follows_current_status);
    $stmt_target_follows_current->fetch();
    $stmt_target_follows_current->close();

    if ($current_follows_target_status !== null) {
        // Current user has an outgoing relationship to target user
        if ($current_follows_target_status == 1) {
            // Current user follows target, and it's mutual/accepted
            $follow_button_text = 'Following';
        } else { // $current_follows_target_status == 0
            // Current user has sent a follow request, but it's pending
            $follow_button_text = 'Pending';
        }
    } else {
        // Current user does NOT have an outgoing follow to target user
        if ($target_follows_current_status !== null && $target_follows_current_status == 0) {
            // Target user has sent a follow request to current user, and it's pending
            $follow_button_text = 'Accept Follow';
        } else {
            // No direct relationship from current to target, and no pending request from target to current
            $follow_button_text = 'Follow'; // Default: current user can initiate a follow
        }
    }
}
// If not logged in ($current_user_id is null) AND NOT viewing own profile (since own profile logic is above)
// the button text will remain 'Follow' (default)

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <title>BandMate - <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></title>

    <style>
        /* Your existing CSS here */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        

        /* profile, name */
        .container {
            display: flex;
            justify-content: left;
            align-items: left;
            flex-direction: column;
            margin-top: 20px;
        }

        .prof {
            display: flex;
            align-items: left;
            text-align: left;
            gap: 10px;
        }

        .prof img{
            border-radius: 50%;
            width: 150px;
            height: 150px;
            margin-bottom: 10px;
            margin-right: 20px;
            margin-top: 10px;
            margin-left: 20px;
        }

        .prof .details {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .prof .details .header {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .prof .details h1 {
            font-size: 30px;
            margin-top: 30px;
            font-weight: 600;
        }

        .prof .details .full-name {
            font-size: 18px;
        }

        .prof .details .buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .prof .details .buttons .follow-btn,
        .prof .details .buttons .message-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 200;
        }

        .prof .details .buttons .follow-btn {
            background-color: #000000;
            color: #FFF;
        }

        .prof .details .buttons .follow-btn:hover {
            background-color: #191919;
        }

        .prof .details .buttons .message-btn:hover {
            background-color: #d9d9d9;
        }
    </style>

</head>
<body>

<?php include_once "component/navs.php" ?>

<div class="container">
    <div class="prof">
        <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture">
        <div class="details">
            <div class="header">
                <h1><?php echo htmlspecialchars($firstname); ?></h1>
                <div class="buttons">
                    <?php if (!$is_own_profile && $current_user_id): // Show follow button only if not own profile and user is logged in ?>
                        <button class="follow-btn" id="followButton" data-target-user-id="<?php echo $target_user_id; ?>">
                            <?php echo htmlspecialchars($follow_button_text); ?>
                        </button>
                    <?php elseif ($is_own_profile): // If it's the user's own profile ?>
                        <button class="follow-btn" onclick="window.location.href='edit_profile.php'">Edit Profile</button>
                    <?php endif; ?>

                    <button type="button" class="message-btn" onclick="window.location.href='message.php?target_user_id=<?php echo urlencode($target_user_id); ?>'">Message</button>
                </div>
            </div>
            <div class="full-name">
                <h3 class="fullname"><?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></h3>
            </div>
            </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const followButton = document.getElementById('followButton');

    if (followButton) {
        followButton.addEventListener('click', async function() {
            const targetUserId = this.getAttribute('data-target-user-id');
            const currentButtonText = this.textContent.trim();
            let action;

            if (currentButtonText === 'Follow') {
                action = 'follow';
            } else if (currentButtonText === 'Accept Follow') {
                action = 'accept_follow'; // New action for accepting a follow request
            } else if (currentButtonText === 'Following' || currentButtonText === 'Pending') {
                action = 'unfollow';
                // Optional: Add a confirmation dialog for unfollow
                if (!confirm('Are you sure you want to unfollow?')) {
                    return;
                }
            } else {
                return; // Should not happen if button text is one of the expected values
            }

            try {
                const formData = new FormData();
                formData.append('target_user_id', targetUserId);
                formData.append('action', action);

                const response = await fetch('handle_follow.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    followButton.textContent = data.buttonText;
                    // You might want to update some visual feedback based on data.status if needed
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred during the follow action.');
            }
        });
    }
});
</script>

</body>
</html>