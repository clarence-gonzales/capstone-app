<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'connectdb.php';

$username = $_SESSION['username'];

// get user details
$stmt = $conn->prepare("SELECT firstname, lastname, profile_picture FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($firstName, $lastName, $profilePicture);
$stmt->fetch();
$stmt->close();

// default pfp
$defaultProfilePicture = "images/default.jpg";
$profilePicture = $profilePicture ? $profilePicture : $defaultProfilePicture;

if (!$firstName || !$lastName){
    $firstName = "Unknown";
    $lastName = "Unknown";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postContent = $_POST['post_content'];
    $selectedInstrument = isset($_POST['selected_instrument']) ? $_POST['selected_instrument'] : null;

    // Image Upload
    $imagePath = null;
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == UPLOAD_ERR_OK) {
        $targetDir = 'uploads/';
        $imagePath = $targetDir . basename($_FILES['post_image']['name']);
        if (!move_uploaded_file($_FILES['post_image']['tmp_name'], $imagePath)) {
            echo "Error uploading image.";
            exit();
        }
    }

    // Insert the post content into the database
    $stmt = $conn->prepare("INSERT INTO posts (username, content, instrument, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $postContent, $selectedInstrument, $imagePath);

    if ($stmt->execute()) {
        echo "Post saved successfully";
    } else {
        echo "Error saving post: " . $stmt->error;
    }

    $stmt->close();

    header("location: dashboard.php");
    exit();
}

// Fetch posts
$posts = [];
$stmt = $conn->prepare("SELECT p.id, p.content, p.created_at, p.instrument, p.image, u.profile_picture, u.firstname, u.lastname, p.username, p.like_count, EXISTS(SELECT 1 FROM likes WHERE likes.post_id = p.id AND likes.username = ?) AS liked, (SELECT COUNT(*) FROM comments WHERE comments.post_id = p.id) AS comment_count FROM posts p JOIN users u ON p.username = u.username ORDER BY p.created_at DESC");
$stmt->bind_param("s", $username);
if ($stmt) {
    $stmt->execute();
    $stmt->bind_result($postId, $content, $createdAt, $instrument, $image, $postProfilePicture, $postFirstName, $postLastName, $postUsername, $likeCount, $liked, $commentCount);

    while ($stmt->fetch()) {
        $posts[] = [
            'id' => $postId,
            'content' => $content,
            'createdAt' => $createdAt,
            'instrument' => $instrument,
            'image' => $image,
            'profilePicture' => $postProfilePicture,
            'firstName' => $postFirstName,
            'lastName' => $postLastName,
            'username' => $postUsername, // Ensure username is fetched to compare with session username
            'likeCount' => $likeCount,
            'liked' => $liked,
            'commentCount' => $commentCount
        ];
    }
    $stmt->close();
} else {
    die("Error preparing the query: " . $conn->error);
}

// Instruments
function getInstrumentImageUrl($instrument) {
    $instruments = [
        "Violinist" => "https://img.icons8.com/color/48/violin.png",
        "Drummer" => "https://img.icons8.com/emoji/48/drum-emoji.png",
        "Guitarist" => "https://img.icons8.com/emoji/48/guitar-emoji.png",
        "Trumpeter" => "https://img.icons8.com/fluency/48/trumpet.png",
        "Pianist" => "https://img.icons8.com/fluency/48/electronic-music.png",
        "Bassist" => "https://img.icons8.com/color/48/bass-guitar.png",
        "Harpist" => "https://img.icons8.com/fluency/48/harp.png",
        "Cellist" => "https://img.icons8.com/fluency/48/cello.png",
        "Flautist" => "https://img.icons8.com/fluency/48/flute.png",
        "Trombonist" => "https://img.icons8.com/fluency/48/trombone.png",
        "Saxophonist" => "https://img.icons8.com/color/48/saxophone.png",
        "Thereminist" => "https://img.icons8.com/color/48/theremin.png"
    ];
    return $instruments[$instrument] ?? "default-image-url.png";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BandMate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #30DFAB;
            --secondary-color: #2A9D8F;
            --dark-color: #264653;
            --light-color: #E9F5F2;
            --accent-color: #F4A261;
            --text-color: #333333;
            --text-light: #777777;
            --white: #FFFFFF;
            --gray-light: #F5F5F5;
            --gray-medium: #E0E0E0;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 6px 16px rgba(0, 0, 0, 0.12);
            --border-radius: 12px;
            --border-radius-sm: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--gray-light);
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            display: flex;
            align-items: center;
            background-color: var(--primary-color);
            padding: 12px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark-color);
        }

        .navbar .icons {
            display: flex;
            gap: 20px;
            margin-left: auto;
        }

        .navbar .icons i {
            font-size: 22px;
            color: var(--dark-color);
            transition: transform 0.2s, color 0.2s;
        }

        .navbar .icons i:hover {
            color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .navbar .icons a.active i {
            color: var(--secondary-color);
        }

        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Create Post */
        .create-post {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .context {
            width: 100%;
            max-width: 600px;
            background-color: var(--white);
            padding: 16px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: box-shadow 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .context:hover {
            box-shadow: var(--shadow-hover);
        }

        .context img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }

        .txt-card {
            flex-grow: 1;
            background: var(--gray-medium);
            padding: 12px 16px;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .txt-card:hover {
            background: #CCCCCC;
        }

        .whatsonmind h3 {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-color);
        }

        /* Post Container */
        .post-item {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
            transition: box-shadow 0.3s;
            width: 100%;
            max-width: 600px;
            margin: 0 auto 20px;
            position: relative;
        }

        .post-item:hover {
            box-shadow: var(--shadow-hover);
        }

        .user-info {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .post-profile-picture {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
            margin-right: 12px;
        }

        .post-header-details {
            display: flex;
            flex-direction: column;
            flex-grow: 1; /* Allow details to take available space */
        }

        .post-name-and-instrument {
            display: flex;
            align-items: center;
            flex-wrap: wrap; /* Allow wrapping on small screens */
        }

        .post-name-and-instrument a {
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            color: var(--text-color);
            transition: color 0.2s;
            margin-right: 5px; /* Space between name and "is looking for" */
        }

        .post-name-and-instrument a:hover {
            color: var(--secondary-color);
        }

        .post-name-and-instrument span {
            font-size: 16px;
            color: var(--text-color);
        }

        .post-time {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 2px; /* Space between name and time */
        }

        /* The post content, image and actions are in their correct places below */
        .post-content {
            margin: 12px 0;
            font-size: 15px;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .post-image img {
            width: 100%;
            max-height: 400px;
            border-radius: var(--border-radius-sm);
            object-fit: cover;
            margin: 12px 0;
        }

        .post-actions {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--gray-medium);
        }

        .post-actions i {
            font-size: 20px;
            cursor: pointer;
            color: var(--text-light);
            transition: color 0.2s;
        }

        .post-actions i:hover {
            color: var(--secondary-color);
        }

        .post-actions i.liked {
            color: #FF4D4D;
        }

        .post-actions i.liked:hover {
            color: #FF4D4D;
        }

        .like-count, .comment-count {
            font-size: 14px;
            color: var(--text-light);
            margin-left: -15px; /* Adjust to bring count closer to icon */
            margin-right: 5px; /* Space between count and next icon/button */
        }

        .delete-button-inline {
            background-color: #FF4D4D;
            color: var(--white);
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 5px 10px; /* Smaller padding for inline button */
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            transition: background-color 0.2s;
            margin-left: auto; /* Push delete button to the right */
        }

        .delete-button-inline:hover {
            background-color: #E60000;
        }

        /* Create Post Popup */
        .create-post-container {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: var(--white);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            z-index: 1001;
        }

        .create-post-container h3 {
            color: var(--text-color);
            margin-bottom: 16px;
            font-weight: 600;
        }

        .xmark-icon {
            position: absolute;
            top: 16px;
            right: 16px;
            font-size: 24px;
            color: var(--text-light);
            cursor: pointer;
            transition: color 0.2s;
        }

        .xmark-icon:hover {
            color: var(--text-color);
        }

        .profile-info {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .picture {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }

        .name {
            margin-left: 12px;
            font-weight: 600;
        }

        .create-post-container textarea {
            width: 100%;
            height: 120px;
            padding: 12px;
            border: 1px solid var(--gray-medium);
            border-radius: var(--border-radius-sm);
            resize: none;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 12px;
        }

        .create-post-container textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        #image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }

        .img-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: var(--border-radius-sm);
        }

        .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .add-post {
            padding: 12px;
            border: 1px dashed var(--gray-medium);
            border-radius: var(--border-radius-sm);
            margin-bottom: 16px;
        }

        .addpost-text {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .addpost-text h4 {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-light);
        }

        .addpost-text i {
            font-size: 20px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .addpost-text i:hover {
            transform: scale(1.1);
        }

        .addpost-text .image {
            color: #F7374F;
        }

        .addpost-text .guitar {
            color: #059212;
        }

        .post-button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .post-button:hover {
            background-color: var(--secondary-color);
        }

        /* Instrument Popup */
        .looking-popup-wrapper {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1002;
            justify-content: center;
            align-items: center;
        }

        .looking-popup {
            background-color: var(--white);
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .looking-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .looking-header h3 {
            font-weight: 600;
            color: var(--text-color);
        }

        .looking-header i {
            font-size: 20px;
            cursor: pointer;
            color: var(--text-light);
            transition: color 0.2s;
        }

        .looking-header i:hover {
            color: var(--text-color);
        }

        .search-input-wrapper {
            position: relative;
            margin-bottom: 16px;
        }

        .search-input-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border: 1px solid var(--gray-medium);
            border-radius: var(--border-radius-sm);
            font-family: 'Poppins', sans-serif;
        }

        .search-input-wrapper input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .instrument {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: var(--border-radius-sm);
            margin-bottom: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .instrument:hover {
            background-color: var(--gray-light);
        }

        .instrument img {
            width: 32px;
            height: 32px;
            margin-right: 12px;
        }

        /* Comment Popup */
        .comment-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow: hidden;
            z-index: 1002;
            flex-direction: column;
        }

        .popup-header {
            padding: 16px;
            border-bottom: 1px solid var(--gray-medium);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .popup-header h3 {
            font-weight: 600;
            color: var(--text-color);
        }

        .close-popup {
            font-size: 24px;
            color: var(--text-light);
            cursor: pointer;
            transition: color 0.2s;
        }

        .close-popup:hover {
            color: var(--text-color);
        }

        .popup-content {
            flex-grow: 1;
            overflow-y: auto;
            padding: 16px;
        }

        .comment-container {
            background-color: var(--gray-light);
            border-radius: var(--border-radius-sm);
            padding: 12px;
            margin-bottom: 12px;
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .comment-profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            border: 1px solid var(--gray-medium);
        }

        .comment-author strong {
            font-size: 14px;
            font-weight: 600;
            display: block;
        }

        .comment-author small {
            font-size: 12px;
            color: var(--text-light);
        }

        .comment-text {
            font-size: 14px;
            color: var(--text-color);
        }

        .comment-input-container {
            padding: 16px;
            border-top: 1px solid var(--gray-medium);
            background-color: var(--white);
        }

        .input-with-icon {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .comment-input {
            flex-grow: 1;
            padding: 10px 16px;
            border: 1px solid var(--gray-medium);
            border-radius: 25px;
            resize: none;
            font-family: 'Poppins', sans-serif;
        }

        .comment-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .comment-submit-btn {
            background: none;
            border: none;
            cursor: pointer;
        }

        .comment-submit-btn i {
            font-size: 24px;
            color: var(--primary-color);
            transition: color 0.2s;
        }

        .comment-submit-btn i:hover {
            color: var(--secondary-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar .icons {
                gap: 15px;
            }

            .navbar .icons i {
                font-size: 20px;
            }

            .post-item {
                padding: 12px;
            }

            .post-actions {
                gap: 15px;
            }

            .create-post-container,
            .looking-popup,
            .comment-popup {
                width: 95%;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 10px 15px;
            }

            .navbar .logo {
                font-size: 20px;
            }

            .navbar .icons {
                gap: 12px;
            }

            .navbar .icons i {
                font-size: 18px;
            }

            .context {
                padding: 12px;
            }

            .context img {
                width: 45px;
                height: 45px;
            }

            .whatsonmind h3 {
                font-size: 14px;
            }

            .post-profile-picture {
                width: 42px;
                height: 42px;
            }

            .post-name {
                font-size: 15px;
            }

            .post-content {
                font-size: 14px;
            }

            .post-actions i {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <?php include_once 'component/navs.php'; ?>

    <div class="main-container">
        <div class="create-post">
            <div class="context" onclick="document.getElementById('createPost').style.display='block'">
                <img src="<?php echo $profilePicture; ?>" alt="Profile Picture">
                <div class="txt-card">
                    <div class="whatsonmind">
                        <h3><?php echo "What's on your mind, " . $firstName . "?"; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div id="posts-container">
            <?php foreach ($posts as $post): ?>
                <?php
                $profileLink = ($post['username'] === $_SESSION['username']) ? 'profile.php' : 'userprofile.php?username=' . urlencode($post['username']);
                ?>
                <div class="post-item">
                    <div class="user-info">
                        <a href="<?php echo $profileLink; ?>"><img src="<?php echo $post['profilePicture']; ?>" alt="<?php echo $post['firstName'] . ' ' . $post['lastName']; ?>" class="post-profile-picture"></a>
                        <div class="post-header-details">
                            <div class="post-name-and-instrument">
                                <a href="<?php echo $profileLink; ?>"><?php echo $post['firstName'] . ' ' . $post['lastName']; ?></a>
                                <?php if (!empty($post['instrument'])): ?>
                                    <span> is looking for </span>
                                    <img src="<?php echo getInstrumentImageUrl($post['instrument']); ?>" alt="<?php echo $post['instrument']; ?>" style="width: 20px; height: 20px; vertical-align: middle;">
                                    <span><?php echo htmlspecialchars($post['instrument']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="post-time">
                                <small><?php echo date("F j, Y, g:i a", strtotime($post['createdAt'])); ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="post-content"><?php echo $post['content']; ?></div>

                    <?php if (!empty($post['image'])): ?>
                        <div class="post-image">
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
                        </div>
                    <?php endif; ?>

                    <div class="post-actions">
                        <i class="fa-regular fa-heart like-button <?php echo $post['liked'] ? 'liked' : ''; ?>" data-post-id="<?php echo $post['id']; ?>"></i>
                        <span class="like-count" id="like-count-<?php echo $post['id']; ?>"><?php echo $post['likeCount'] ?? 0; ?></span>
                        <i class="fa-regular fa-comment comment-button" onclick="openCommentPopup(<?php echo $post['id']; ?>)"></i>
                        <span class="comment-count" id="comment-count-<?php echo $post['id']; ?>"><?php echo $post['commentCount'] ?? 0; ?></span>
                        <?php if ($post['username'] === $_SESSION['username']): // Check if the post belongs to the current user ?>
                            <button class="delete-button-inline" onclick="deletePost(<?php echo $post['id']; ?>)">Delete</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="create-post-container" id="createPost">
        <i class="fa-solid fa-circle-xmark xmark-icon" onclick="document.getElementById('createPost').style.display='none';"></i>
        <h3>Create Post</h3>
        <hr>
        <div class="profile-info">
            <img src="<?php echo htmlspecialchars($profilePicture); ?>" class="picture">
            <div>
                <h3 class="name"><?php echo $firstName . ' ' . $lastName; ?></h3>
                <span id="selectedInstrumentalist" style="color: var(--text-light); font-size: 14px;"></span>
            </div>
        </div>

        <form action="dashboard.php" method="POST" enctype="multipart/form-data">
            <textarea placeholder="What's on your mind?" name="post_content" required></textarea>
            <input type="hidden" name="selected_instrument" id="selectedInstrumentInput">

            <div id="image-preview-container"></div>

            <div class="add-post">
                <div class="addpost-text">
                    <h4>Add to your post</h4>
                    <i class="fa-regular fa-image image" id="image-icon"></i>
                    <i class="fa-solid fa-guitar guitar" id="openModal"></i>
                    <input type="file" name="post_image" id="file-input" style="display: none;" accept="image/*" multiple>
                </div>
            </div>

            <button type="submit" name="submit_post" class="post-button">Post</button>
        </form>
    </div>

    <div class="looking-popup-wrapper" id="lookingPopup">
        <div class="looking-popup">
            <div class="looking-header">
                <i id="closeLookingPopup" class="fa-solid fa-arrow-left"></i>
                <h3>What are you looking for?</h3>
            </div>
            <hr>
            <div class="search-input-wrapper">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Search" id="searchBox">
            </div>
            <div class="instrumentalist-list" id="instrumentalistsList">
                <div class="instrument"><img src="https://img.icons8.com/color/48/violin.png" alt="violin"/>Violinist</div>
                <div class="instrument"><img src="https://img.icons8.com/emoji/48/drum-emoji.png" alt="drum-emoji"/>Drummer</div>
                <div class="instrument"><img src="https://img.icons8.com/emoji/48/guitar-emoji.png" alt="guitar-emoji"/>Guitarist</div>
                <div class="instrument"><img src="https://img.icons8.com/fluency/48/trumpet.png" alt="trumpet"/>Trumpeter</div>
                <div class="instrument"><img src="https://img.icons8.com/fluency/48/electronic-music.png" alt="electronic-music"/>Pianist</div>
                <div class="instrument"><img src="https://img.icons8.com/color/48/bass-guitar.png" alt="bass-guitar"/>Bassist</div>
                <div class="instrument"><img src="https://img.icons8.com/fluency/48/harp.png" alt="harp"/>Harpist</div>
                <div class="instrument"><img src="https://img.icons8.com/fluency/48/cello.png" alt="cello"/>Cellist</div>
                <div class="instrument"><img src="https://img.icons8.com/fluency/48/flute.png" alt="flute"/>Flautist</div>
                <div class="instrument"><img src="https://img.icons8.com/fluency/48/trombone.png" alt="trombone"/>Trombonist</div>
                <div class="instrument"><img src="https://img.icons8.com/color/48/saxophone.png" alt="saxophone"/>Saxophonist</div>
                <div class="instrument"><img src="https://img.icons8.com/color/48/theremin.png" alt="theremin"/>Thereminist</div>
            </div>
        </div>
    </div>

    <div class="comment-popup" id="commentPopup">
        <div class="popup-header">
            <h3>Comments</h3>
            <i class="fa-solid fa-circle-xmark close-popup" onclick="closeCommentPopup()"></i>
        </div>
        <div class="popup-content">
            <div id="commentList"></div>
        </div>
        <div class="comment-input-container">
            <form id="commentForm" action="submit_comment.php" method="POST">
                <div class="input-with-icon">
                    <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-picture" style="width: 40px; height: 40px; border-radius: 50%;">
                    <textarea name="comment" id="commentInput" class="comment-input" placeholder="Write a comment..." required></textarea>
                    <button type="submit" class="comment-submit-btn">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
                <input type="hidden" name="post_id" id="postIdInput" value="">
            </form>
        </div>
    </div>

<script>
    // Create Post
    document.querySelector('.txt-card').addEventListener('click', () => {
        document.getElementById('createPost').style.display = 'block';
    });

    // Image Upload Preview
    document.getElementById('image-icon').addEventListener('click', () => {
        document.getElementById('file-input').click();
    });

    document.getElementById('file-input').addEventListener('change', function(event) {
        const previewContainer = document.getElementById('image-preview-container');
        previewContainer.innerHTML = '';

        const files = event.target.files;
        if (files.length > 0) {
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgWrapper = document.createElement('div');
                    imgWrapper.classList.add('img-wrapper');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('preview-image');

                    const removeBtn = document.createElement('button');
                    removeBtn.innerHTML = '×';
                    removeBtn.classList.add('remove-btn');
                    removeBtn.onclick = function() {
                        imgWrapper.remove();
                    };

                    imgWrapper.appendChild(img);
                    imgWrapper.appendChild(removeBtn);
                    previewContainer.appendChild(imgWrapper);
                };
                reader.readAsDataURL(file);
            });
        }
    });

    // Instrument Popup
    document.getElementById('openModal').addEventListener('click', () => {
        document.getElementById('lookingPopup').style.display = 'flex';
    });

    document.getElementById('closeLookingPopup').addEventListener('click', () => {
        document.getElementById('lookingPopup').style.display = 'none';
    });

    // Search Instruments
    document.getElementById('searchBox').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const instruments = document.querySelectorAll('.instrument');

        instruments.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? 'flex' : 'none';
        });
    });

    // Select Instrument
    document.querySelectorAll('.instrument').forEach(item => {
        item.addEventListener('click', function() {
            const selectedInstrument = this.textContent.trim();
            const selectedInstrumentImage = this.querySelector('img').src;
            document.getElementById("selectedInstrumentInput").value = selectedInstrument;

            const displayText = `Looking for ${selectedInstrument}`;
            document.getElementById("selectedInstrumentalist").innerHTML =
                `<img src="${selectedInstrumentImage}" alt="${selectedInstrument}" style="width: 20px; height: 20px; vertical-align: middle;">
                <span>${displayText}</span>`;

            document.getElementById('lookingPopup').style.display = 'none';
        });
    });

    // Like Functionality
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const likeCountSpan = document.getElementById(`like-count-${postId}`);
            const currentCount = parseInt(likeCountSpan.textContent) || 0;
            const isLiked = this.classList.contains('liked');

            fetch('like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=${postId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'liked') {
                        likeCountSpan.textContent = currentCount + 1;
                        this.classList.add('liked');
                    } else if (data.action === 'unliked') {
                        likeCountSpan.textContent = currentCount - 1;
                        this.classList.remove('liked');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Comment Popup
    function openCommentPopup(postId) {
        const popup = document.getElementById('commentPopup');
        document.getElementById('postIdInput').value = postId;
        const commentList = document.getElementById('commentList');

        // Clear previous comments
        commentList.innerHTML = '';

        // Fetch comments
        fetch(`fetch_comments.php?post_id=${postId}`)
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(comment => {
                        const commentDiv = document.createElement('div');
                        commentDiv.classList.add('comment-container');
                        commentDiv.innerHTML = `
                            <div class="comment-header">
                                <img src="${comment.profilePicture || 'images/default.jpg'}"
                                    alt="${comment.firstName || 'Unknown'} ${comment.lastName || 'Unknown'}"
                                    class="comment-profile-picture">
                                <div class="comment-author">
                                    <strong>${comment.firstName || 'Unknown'} ${comment.lastName || 'Unknown'}</strong>
                                    <small>${new Date(comment.createdAt).toLocaleString()}</small>
                                </div>
                            </div>
                            <p class="comment-text">${comment.text || ''}</p>
                        `;
                        commentList.appendChild(commentDiv);
                    });
                } else {
                    commentList.innerHTML = '<p style="text-align: center; color: var(--text-light);">No comments yet. Be the first to comment!</p>';
                }
            })
            .catch(error => console.error('Error fetching comments:', error));

        popup.style.display = 'flex';
    }

    function closeCommentPopup() {
        document.getElementById('commentPopup').style.display = 'none';
    }

    // Submit Comment
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const postId = document.getElementById('postIdInput').value;
        const commentInput = document.getElementById('commentInput');

        fetch('submit_comment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update comment count
                const commentCountSpan = document.getElementById(`comment-count-${postId}`);
                const currentCount = parseInt(commentCountSpan.textContent) || 0;
                commentCountSpan.textContent = currentCount + 1;

                // Add new comment to the list
                const commentList = document.getElementById('commentList');
                const commentDiv = document.createElement('div');
                commentDiv.classList.add('comment-container');
                commentDiv.innerHTML = `
                    <div class="comment-header">
                        <img src="${data.comment.profilePicture || 'images/default.jpg'}"
                            alt="${data.comment.firstName || 'Unknown'} ${data.comment.lastName || 'Unknown'}"
                            class="comment-profile-picture">
                        <div class="comment-author">
                            <strong>${data.comment.firstName || 'Unknown'} ${data.comment.lastName || 'Unknown'}</strong>
                            <small>Just now</small>
                        </div>
                    </div>
                    <p class="comment-text">${data.comment.text || ''}</p>
                `;

                // If "no comments" message exists, remove it
                const noCommentsMsg = commentList.querySelector('p');
                if (noCommentsMsg) commentList.removeChild(noCommentsMsg);

                commentList.prepend(commentDiv);
                commentInput.value = '';
            }
        })
        .catch(error => console.error('Error submitting comment:', error));
    });

    // Delete Post (Simplified as there's no more toggle menu)
    function deletePost(postId) {
        if (confirm("Are you sure you want to delete this post?")) {
            fetch('delete_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=${postId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find the post-item element by looking for a descendant with the data-post-id
                    // and then finding its closest ancestor with the class 'post-item'
                    const postElement = document.querySelector(`.post-item .like-button[data-post-id="${postId}"]`).closest('.post-item');
                    if (postElement) {
                        postElement.remove();
                    }
                } else {
                    alert('Error deleting post: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // Close popups when clicking on overlay
    document.querySelectorAll('.looking-popup-wrapper, .comment-popup').forEach(popup => {
        popup.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>