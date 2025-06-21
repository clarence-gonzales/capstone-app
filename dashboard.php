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
    echo "Post saved succesfully";
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
      'username' => $postUsername,
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <title>BandMate</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
    }

    /* Navbar */
    .navbar {
      display: flex;
      align-items: center;
      background-color: #30DFAB;
      padding: 10px 20px;
      position: relative;
    }

    .navbar .logo {
      font-size: 24px;
      font-weight: bold;
      color: #000000;
      flex: 1;
    }

    .navbar .icons {
      display: flex;
      gap: 50px;
      position: absolute;
      left: 50%;
      transform: translate(-50%);
    }

    .navbar .icons i {
      font-size: 25px;
      width: 50px;
      cursor: pointer;
      color: #000000;
    }

    /* create post */
    .create-post {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .context {
      width: 600px;
      height: 13vh;
      background-color: #E2E2E2;
      padding: 20px;
      border-radius: 15px;
      margin-top: 20px;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px;
    }

    .context img {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      cursor: pointer;
      transition: 0.3s;
    }

    .context img:hover {
      opacity: 0.7;
    }

    .whatsonmind {
      cursor: pointer;
      color: #000000;
      margin-left: -180px;
      font-size: 18px;
    }

    .txt-card .whatsonmind h3 {
  font-size: 18px; /* Resize as needed */
  font-weight: normal; /* Optional: change weight */
  color: #333;         /* Optional: change color */
}

    .txt-card {
      display: flex;
      align-items: center;
      background: #B6B6B6;
      padding: 10px;
      border-radius: 25px;
      margin-bottom: 5px;
      margin-left: -10px;
      cursor: pointer;
      width: 100%;
      justify-content: center;
      margin-bottom: 10px;
    }

    /* Popup Create Post */
    .create-post-container {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #D9D9D9;
      color: #FFFFFFFF;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
      width: 600px;
      max-height: 500px;
      overflow-y: auto;
      flex-direction: column;
    }

    .create-post-container textarea {
      width: 100%;
      height: 100px;
      margin-bottom: 10px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      color: #000000;
      resize: vertical;
      resize: none;
    }

    .create-post-container .post {
      background-color: #000000;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 3px;
      width: 100%;
      font-weight: bold;
      cursor: pointer;
    }

    .create-post-container .post:hover {
      background-color: #2C2D2D;
    }

    .xmark-icon {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 30px;
      color: #000000;
      transition: 0.3s;
    }

    .xmark-icon:hover {
      opacity: 0.7;
    }

    textarea {
      font-family: 'Poppins', sans-serif;
      color: #000000;
      padding: 10px;
    }

    .create-post {
      color: #000000;
    }
    
    .profile-info {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }
    
    .picture {
      width: 55px;
      height: 55px;
      border-radius: 50%;
      margin-top: 10px;
      transition: 0.3s;
    }

    .picture:hover {
      opacity: 0.7;
    }

    .name {
      color: #000000;
      margin-left: 10px;
      /* margin-bottom: -30px; */
    }

    /* Img preview */
    #image-preview-container {
      width: 100%;
      max-height: 200px;
      display: flex;
      justify-content: flex-start;
      align-items: center;
      overflow-x: auto;
      border-radius: 5px;
      margin-top: 10px;
      margin-bottom: 20px;

      flex-wrap: wrap;
      gap: 10px;
      padding: 5px;
    }

    .img-wrapper {
      position: relative;
      width: 100px;
      height: 100px;
      display: inline-block;
    }

    .preview-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 5px;
    }

    .remove-btn {
      position: absolute;
      top: 3PX;
      right: 3px;
      background: rgba(0, 0, 0, 1);
      color: #FFFFFFFF;
      border: none;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      font-size: 12px;
      cursor: pointer;
      font-weight: bold;
    }

    .remove-btn:hover {
      background: #2C2D2D;
    }

    #image-preview {
      max-width: 100%;
      max-height: 200px;
      object-fit: contain;
      border-radius: 10px;
      display: none;
    }

    /*Add to Post */
    .add-post {
      padding: 20px;
      border: 3px solid #000000;
      margin-bottom: 10px;
      border-radius: 5px;
    }

    .add-post .addpost-text {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .addpost-text {
      color: #000000;
    }

    .addpost-text .image {
      color: #F7374F;
      font-size: 24px;
      transition: 0.3s;
    }

    .addpost-text .guitar {
      color: #059212;
      font-size: 24px;
      transition: 0.3s;
    }

    .addpost-text .image:hover, .guitar:hover {
      opacity: 0.7;
    }

    .main-container {
      display: flex;
      flex-direction: column;
    }

    /* Modal Looking for */
    .looking-popup-wrapper {
      position: fixed;
      display: none;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      justify-content: center;
      align-items: center;
      z-index: 999;
    }

    .looking-popup {
      width: 600px;
      height: 500px;
      background: #31363F;
      color: #FFFFFFFF;
      padding: 20px;
      border-radius: 10px;
      align-items: center;
      overflow-y: auto;
    }

    .looking-popup .instrumentalist-list {
      overflow-y: hidden;
    }

    .instrumentalist-list {
      height: calc(100% - 80px);
    }

    :is(.looking-popup)::-webkit-scrollbar {
    width: 0px;
  }

    .looking-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      padding: 10px;
      /* color: #00000000; */
      margin-bottom: 10px;
    }

    .looking-header h3 {
      flex-grow: 1;
      text-align: center;
      margin: 0;
    }

    #closeLookingPopup {
      flex-shrink: 0;
    }

    .looking-header i {
      background-color: #EEEEEE;
      border-radius: 50%;
      font-size: 20px;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #000000;
      transition: 0.3s;
    }

    .looking-header i:hover {
      opacity: 0.7;
    }

    .search-input-wrapper {
      position: relative;
      width: 100%;
      height: 50px;
      margin-top: 10px;
    }

    .search-input-wrapper i {
      position: absolute;
      top: 5px;
      left: 15px;
      color: #999;
      font-size: 20px;
    }

    .search-input-wrapper input[type="text"] {
      width: 100%;
      padding: 8px 8px 8px 40px;
      margin-bottom: 10px;
      border: none;
      border-radius: 25px;
    }

    .instrument {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px;
      border-radius: 10px;
      margin-bottom: 10px;
      background-color: #212529;
      color: #FFFFFFFF;
      cursor: pointer;
      width: 100%;
      box-sizing: border-box;
    }

    .instrument:hover {
      opacity: 0.8;
    }

    .instrument img {
      background-color: #15181C;
      padding: 5px;
      border-radius: 10px;
    }

    /* Post on Dashboard */
    #post-container {
      border: 1px solid #ccc;
      padding: 10px;
      margin-top: 10px;
      border-radius: 5px;

      max-width: 700px;
    }

    .post-item {
      border: 1px solid #E0E0E0;
      padding: 15px 10px;
      padding-bottom: 10px;
      margin-bottom: 10px;
      margin-top: 10px;
      /* display: flex; */
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
      /* height: 200px; */
      /* width: 600px; */
      width: 100%;
      max-width: 600px;
      margin: 10px auto;
      background-color: #B6B6B6;
      border-radius: 15px;

      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
      transition: box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
      min-height: 150px;
      word-wrap: break-word;
      overflow: hidden;
      position: relative;
    }

    .post-item:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .user-info {
      display: flex;
      align-items: center;
      margin-right: 10px;
      align-items: flex-start;
    }
    
    .post-profile-picture {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ddd;
      margin-right: 10px;
      cursor: pointer;
      transition: 0.3s;
    }

    .post-profile-picture:hover {
      opacity: 0.7;
    }

    .post-name {
      font-weight: 600;
      font-size: 16px;
      margin-bottom: 5px;
      margin-top: 10px;
      margin-left: -2px;
      cursor: pointer;
    }

    .post-name a {
      text-decoration: none;
      color: #000000
    }

    .post-name a:hover {
      text-decoration: underline;
    }

    .post-content {
      margin-right: 20px;
      margin-top: 10px;
      font-size: 15px;
      line-height: 1.6;
      white-space: pre-wrap;
      word-wrap: break-word;
      overflow: hidden;
      /* text-align: left; */
    }

    .post-time {
      font-size: 0.8rem;
      color: #666;
      /* margin-top: -65px; */
      /* margin-left: 57px; */
      position: absolute;
      right: 10px;
      bottom: 10px;
    }

    .post-image img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
    margin-top: 70px;
    margin-bottom: -5px;
    /* display: block; */
    object-fit: cover;
    }

    /* Like */
    .post-actions {
      margin-top: 70px;
      display: flex;
      justify-content: flex-start;
      gap: 15px;
    }

    .post-actions i {
      font-size: 20px;
      cursor: pointer;
      color: #000000;
      margin-left: 7px;
      transition: color 0.3s ease;
    }

    .post-actions i:hover {
      color: #808080;
    }

    .post-actions i.liked:hover {
      color: red;
      cursor: default;
    }
    
    .like-button.liked {
      color: red;
      font-weight: bold;
      transition: color 0.3s ease;
    }

    .like-count {
      margin-left: -7px;
    }

    /* Comment */
    .comment-popup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #D9D9D9;
      color: #000000;
      padding: 10px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
      width: 450px;
      height: 500px;
      max-height: 500px;
      display: flex;
      flex-direction: column;
    }

    :is(.popup-content)::-webkit-scrollbar {
    width: 0px;
  }

    .popup-content {
      display: flex;
      flex-direction: column;
      gap: 10px;
      overflow-y: auto;
      flex-grow: 1;
      max-height: 80%;
      padding-right: 10px;
    }

    .comment-input-container {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 400px;
      padding: 10px;
      border-radius: 5px;
      position: sticky;
      bottom: 0;
    }

    textarea#commentInput {
      flex-grow: 1;
      width: 100%;
      height: 50px;
      border: 1px solid #ccc;
      border-radius: 5px;
      padding: 10px;
      resize: none;
    }

    .comment-submit-btn {
      border: none;      
      display: flex;
      align-items: right;
      justify-content: center;
      border-radius: 50%;
      cursor: pointer;
      margin-left: 5px;
      background: none;
      padding: 0;
    }

    .comment-submit-btn i {
      color: #000000;
      font-size: 24px;
      transform: rotate(40deg);
    }

    .comment-input-container {
      display: flex;
      gap: 10px;
    }

    .close-popup {
      font-size: 24px;
      text-align: right;
      display: flex;
      justify-content: right;
      position: sticky;
      top: 0;
      right: 0;
      cursor: pointer;
      transition: 0.3s;
    }

    .close-popup:hover {
      opacity: 0.7;
    }

    .input-with-icon {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: -50px;
    }

    .profile-picture {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      object-fit: cover;
    }

    /*Commnt contianer */
    .comment-container {
      display: flex;
      flex-direction: column;
      gap: 5px;
      background-color: #F9F9F9;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .comment-header {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .comment-profile-picture {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      object-fit: cover;
    }

    .comment-author {
      display: flex;
      flex-direction: column;
    }

    .comment-author strong {
      font-size: 14px;
      color: #333;
    }

    .comment-author small {
      font-size: 12px;
      color: #777;
    }

    .comment-text {
      font-size: 14px;
      color: 555;
      margin-top: 5px;
    }


.post-options {
  position: absolute;
  right: 10px;
  cursor: pointer;
  bottom: 220px;
}

.post-options i {
  font-size: 20px;
}

.delete-menu {
  position: absolute;
  top: 0;
  right: 0;
  padding: 5px;
  z-index: 10;
}

.delete-menu .delete-button {
  background-color: #ff4d4d;
  color: #fff;
  border: none;
  border-radius: 3px;
  padding: 5px 10px;
  cursor: pointer;
}

.delete-menu .delete-button:hover {
  background-color: #e60000;
}
  </style>
</head>
<body>
<nav class="navbar">
    <div class="logo">BandMate</div>
    <div class="icons">
      <a href="dashboard.php"><i class="fa-solid fa-newspaper"></i></a>
      <a href="search.php"><i class="fa-solid fa-search"></i></a>
      <a href="contact.php"><i class="fa-solid fa-users"></i></a>
      <a href="message.php"><i class="fa-solid fa-message"></i></a>
      <a href="profile.php"><i class="fa-solid fa-user"></i></a>
    </div>
  </nav>

  <div class="main-container">
    <div class="create-post">
      <div class="context">
      <a href="profile.php"><img src="<?php echo $profilePicture; ?>"></a>
          <div class="txt-card">
            <div class="whatsonmind">
              <h3><?php echo "What's on your mind, " . $firstName . "?"; ?></h3>
            </div>
          </div>
      </div>
    </div>
    
    <!-- Posts -->
    <div id="posts-container">
      <?php foreach ($posts as $post): ?>
        <?php 
            $profileLink = ($post['username'] === $_SESSION['username']) ? 'profile.php' : 'userprofile.php?username=' . urlencode($post['username']);
            ?>
        <div class="post-item">
          <div class="user-info">
            <!-- profile pic -->
            <a href="<?php echo $profileLink; ?>"><img src="<?php echo $post['profilePicture']; ?>" alt="<?php echo $post['firstName'] . ' ' . $post['lastName']; ?>" class="post-profile-picture"></a>
            <!-- profile name -->
            <a href="<?php echo $profileLink; ?>"><div class="post-name"><?php echo $post['firstName'] . ' ' . $post['lastName']; ?></a>
          
            <?php if (!empty($post['instrument'])): ?>
              <span> is looking for </span>
                <img src="<?php echo getInstrumentImageUrl($post['instrument']); ?>" alt="<?php echo $post['instrument']; ?>" style="width: 20px; height: 20px; margin-left: 5px;">
                <span><?php echo htmlspecialchars($post['instrument']); ?></span>
            <?php endif; ?>

          </div>
          </div>
          <div class="post-content"><?php echo $post['content']; ?>


          <div class="post-options">
        <i class="fa-solid fa-ellipsis-vertical" onclick="toggleDeleteMenu(<?php echo $post['id']; ?>)"></i>
        <div class="delete-menu" id="delete-menu-<?php echo $post['id']; ?>" style="display: none;">
          <button class="delete-button" onclick="deletePost(<?php echo $post['id']; ?>)">Delete</button>
        </div>
      </div>

          </div>
          <div class="post-body">
            <div class="post-time">
              <small><?php echo date("F j, Y, g:i a", strtotime($post['createdAt'])); ?></small>
            </div>
          </div>

          <!-- Like and comment Icon for Text -->
          <?php if (empty($post['image'])): ?>
            <div class="post-actions">
              <i class="fa-regular fa-heart like-button <?php echo $post['liked'] ? 'liked' : ''; ?>" data-post-id="<?php echo $post['id']; ?>"></i>
              <span class="like-count" id="like-count-<?php echo $post['id']; ?>"><?php echo $post['likeCount'] ?? 0; ?> </span>
              <i class="fa-regular fa-comment comment-button" onclick="openCommentPopup(<?php echo $post['id']; ?>)"></i>
              <span class="comment-count" id="comment-count-<?php echo $post['id']; ?>"><?php echo $post['commentCount'] ?? 0; ?> </span>
            </div>
          <?php endif; ?>

          <!-- image -->
          <?php if (!empty($post['image'])): ?>
            <div class="post-image">
              <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" style="max-width: 100%; height: auto; object-fit: cover; border-radius: 10px;">
            </div>
            <!-- Like and comment icon for image -->
            <div class="post-actions image-icons">
              <i class="fa-regular fa-heart like-button <?php echo $post['liked'] ? 'liked' : ''; ?>" data-post-id="<?php echo $post['id']; ?>"></i>
              <span class="like-count" id="like-count-<?php echo $post['id']; ?>"><?php echo $post['likeCount'] ?? 0; ?> </span>
              <i class="fa-regular fa-comment comment-button" onclick="openCommentPopup(<?php echo $post['id']; ?>)"></i>
              <span class="comment-count" id="comment-count-<?php echo $post['id']; ?>"><?php echo $post['commentCount'] ?? 0; ?> </span>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Popup for Comments -->
  <div class="comment-popup" id="commentPopup" style="display: none;">
    <div class="popup-content">
      <i class="fa-solid fa-circle-xmark close-popup" onclick="closeCommentPopup()"></i>
      <div id="commentList">
              
      </div>
    </div>
    <div class="comment-input-container">
      <form id="commentForm" action="submit_comment.php" method="POST">
        <div class="input-with-icon">
          <img src="<?php echo $profilePicture; ?>" alt="" class="profile-picture">
          <textarea name="comment" id="commentInput" placeholder="Write a comment..." required></textarea>
          <button type="submit" class="comment-submit-btn">
            <i class="fa-solid fa-location-arrow"></i>
          </button>
        </div>
        <input type="hidden" name="post_id" id="postIdInput" value="123">
      </form>
    </div>

  </div>


  <!-- Popup -->
  <div class="create-post-container" id="createPost">
  <i class="fa-solid fa-circle-xmark xmark-icon" onclick="document.getElementById('createPost').style.display='none';"></i>

  <h3 class="create-post">Create Post</h3>
    <hr>
    <div class="profile-info">
      <a href="profile.php"><img src="<?php echo htmlspecialchars($profilePicture); ?>" class="picture"></a>
      <div class="name-container">
      <h3 class="name"><?php echo $firstName . ' ' . $lastName; ?></h3>
      <span id="selectedInstrumentalist"style="color: #000000; font-weight: bold; margin-left: 15px; margin-top: -100px;"></span>
      </div>
    </div>

    <!-- form -->
  <form action="dashboard.php" method="POST" enctype="multipart/form-data">
      <textarea placeholder="What's on your mind?" name="post_content" required></textarea>
      <input type="hidden" name="selected_instrument" id="selectedInstrumentInput">
      
    <!-- Image prev -->
    <div id="image-preview-container">
        <img id="image-preview" src="" alt="Image Preview">
    </div>

    <!--Add to post -->
    <div class="add-post">
      <div class="addpost-text">
      <h4>Add to your post</h4>
      <i class="fa-regular fa-image image" id="image-icon" style="cursor: pointer"></i>
      <i class="fa-solid fa-guitar guitar" id="openModal" style="cursor: pointer"></i>
      <input type="file" name="post_image" id="file-input" style="display: none;" accept="image/*" multiple>
      </div>
    </div>

      <button type="submit" name="submit_post" class="post">Post</button>
</form>
  </div>

  <!-- Modal Looking for  -->
  <div class="looking-popup-wrapper">
    <div class="looking-popup">
      <div class="looking-popup-con">
        <div class="looking-header">
          <i id="closeLookingPopup" class="fa-solid fa-arrow-left"></i>
          <h3>What are you looking for?</h3>
        </div>
        <hr>
          <div class="search-input-wrapper">
            <i class="fa-solid fa-search"></i><input type="text" placeholder="Search" id="searchBox">
          </div>
          <div class="instrumentalist-list" id="instrumentalistsList">
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/color/48/violin.png" alt="violin"/>Violinist</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/emoji/48/drum-emoji.png" alt="drum-emoji"/>Drummer</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/emoji/48/guitar-emoji.png" alt="guitar-emoji"/>Guitarist</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/fluency/48/trumpet.png" alt="trumpet"/>Trumpeter</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/fluency/48/electronic-music.png" alt="electronic-music"/>Pianist</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/color/48/bass-guitar.png" alt="bass-guitar"/>Bassist</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/fluency/48/harp.png" alt="harp"/>Harpist</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/fluency/48/cello.png" alt="cello"/>Cellist</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/fluency/48/flute.png" alt="flute"/>Flautist</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/fluency/48/trombone.png" alt="trombone"/>Trombonist</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/color/48/saxophone.png" alt="saxophone"/>Saxophonist</div>
            <div class="instrument"><img width="48" height="48" src="https://img.icons8.com/color/48/theremin.png" alt="theremin"/>Thereminist</div>
          </div>
      </div>
    </div>
  </div>

<script>
  // Create Post
  const textarea =  document.querySelector('textarea');

  textarea.addEventListener('input', function() {
    this.style.color = 'black';
  });

  const whatsonmind = document.querySelector('.whatsonmind');
  const createPostContainer = document.getElementById('createPost');

  whatsonmind.addEventListener('click', () => {
    createPostContainer.style.display = 'block';
  });

  // Add to post
  document.addEventListener("DOMContentLoaded", function () {
    const imageIcon = document.getElementById("image-icon");
    const fileInput = document.getElementById("file-input");
    const imagePreviewContainer = document.getElementById("image-preview-container");

    imageIcon.addEventListener("click", () => {
      fileInput.click();
    });

    fileInput.addEventListener("change", function (event) {
       imagePreviewContainer.innerHTML = "";

       const files = event.target.files;

       if (files.length > 0) {
        Array.from(files).forEach(file => {
          const reader = new FileReader();
          reader.onload = function (e) {
            const imgWrapper = document.createElement("div");
            imgWrapper.classList.add("img-wrapper");

            const img = document.createElement("img");
            img.src = e.target.result;
            img.classList.add("preview-image");

            const removeBtn = document.createElement("button");
            removeBtn.innerHTML = "X";
            removeBtn.classList.add("remove-btn");
            removeBtn.onclick = function () {
              imgWrapper.remove();
            };

            imgWrapper.appendChild(img);
            imgWrapper.appendChild(removeBtn);
            imagePreviewContainer.appendChild(imgWrapper);
          };
          reader.readAsDataURL(file);
        });
       } 
   });
  });

  const guitarIcon = document.getElementById("openModal");
  const lookingPopup = document.querySelector(".looking-popup-wrapper");

  guitarIcon.addEventListener("click", () => {
    lookingPopup.style.display = "flex";
  });

  // Arrow Left Back Button
  const closePopupBtn = document.getElementById("closeLookingPopup");

  closePopupBtn.addEventListener("click", () => {
    lookingPopup.style.display = "none";
  });

  // Search Box
  const searchBox = document.getElementById("searchBox");
  const instrumentalistsList = document.getElementById("instrumentalistsList");
  const instruments = instrumentalistsList.getElementsByClassName("instrument");

  searchBox.addEventListener("input", function() {
    const filter = searchBox.value.toLowerCase().trim();
    Array.from(instruments).forEach(item => {
      const text = item.textContent.toLowerCase();
      item.style.display = text.includes(filter) ? "" : "none";
    });
  });

  // Selected Instrumentalist
  const selectedInstrumentalistSpan = document.getElementById("selectedInstrumentalist");

  document.querySelectorAll(".instrument").forEach(item => {
    item.addEventListener("click", function() {
      const selectedInstrumentalist = this.textContent.trim();
      const selectedInstrumentImage = this.querySelector('img').src;

      selectedInstrumentalistSpan.innerHTML = `is <img src="${selectedInstrumentImage}" alt="${selectedInstrumentalist}" style="width: 20px; height: 20px; margin-right: -3px;"/> looking for ${selectedInstrumentalist}.` ;

      lookingPopup.style.display = "none";
    });
  });


  document.querySelectorAll(".instrument").forEach(item => {
        item.addEventListener("click", function() {
            const selectedInstrument = this.textContent.trim();
            const selectedInstrumentImage = this.querySelector('img').src;
            document.getElementById("selectedInstrumentInput").value = selectedInstrument;
        });
    });


  // Like
  document.querySelectorAll('.like-button').forEach(button => {
  button.addEventListener('click', function() {
    const postId = this.getAttribute('data-post-id');
    const likeCountSpan = document.getElementById(`like-count-${postId}`);
    const currentCount = parseInt(likeCountSpan.textContent) || 0;

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
      } else {
          console.error('Error:', data.message);
          alert('Failed to process your request.');
      }
  })
  .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while processing your request. Please try again.');
  });
});
});

// Comment
function openCommentPopup(postId) {
  const popup = document.getElementById('commentPopup');
  const postIdInput = document.getElementById('postIdInput');
  const commentList = document.getElementById('commentList');

  postIdInput.value = postId;
  commentList.innerHTML = '';

  fetch(`fetch_comments.php?post_id=${postId}`)
  .then(response => response.json())
  .then(data => {
    if (Array.isArray(data)) {
    data.forEach(comment => {
      const commentDiv = document.createElement('div');
      commentDiv.classList.add('comment-container');
      commentDiv.innerHTML = `
      <div class="comment-header">
      <img src="${comment.profilePicture || 'images/default.jpg'}" alt="${comment.firstName || 'Unknown'} ${comment.lastName || 'Unknown'}"
        class="comment-profile-picture">
      <div class="comment-author">
      <strong>${comment.firstName || 'Unknown'} ${comment.lastName || 'Unknown'}</strong>
      <small>${new Date(comment.createdAt).toLocaleString() || 'Unknown Date'}</small>
      </div>
      </div>
      <p class="comment-text">${comment.text || ''}</p>
      `;
      commentList.appendChild(commentDiv);
    });
    } else {
      commentList.innerHTML = '<p>No comments yet. Be the first to comment!</p>';
    }
  })
  .catch(error => console.error('Error fetching comments:', error));

  popup.style.display = 'block';
}

function closeCommentPopup() {
  const popup = document.getElementById('commentPopup');
  popup.style.display = 'none';
}

document.getElementById('commentForm').addEventListener('submit', function (e) {
  e.preventDefault();
  console.log('Form submitted');
  const formData = new FormData(this);
  const commentInput = document.getElementById('commentInput');
  const commentList = document.getElementById('commentList');

  // clear the comment input
  commentInput.value = '';


  fetch('submit_comment.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    console.log('Response data:', data);
    if (data.success) {
      // Count of comments
      const commentCountSpan = document.getElementById(`comment-count-${postId}`);
      const currentCount = parseInt(commentCountSpan.textContent) || 0;
      commentCountSpan.textContent = currentCount + 1;

    // Comment Container create/ add new comment
    const commentList = document.getElementById('commentList');
    // const comment = data.comment;
    const commentDiv = document.createElement('div');
    commentDiv.classList.add('comment-container');
    commentDiv.innerHTML = `
      <div class="comment-header">
        <img src="${data.comment.profilePicture}" alt="${data.comment.firstName} ${data.comment.lastName}" class="comment-profile-picture">
        <div class="comment-author">
          <strong>${data.comment.firstName} ${data.comment.lastName}</strong>
          <small>${new Date(data.comment.createdAt).toLocaleString()}</small>
        </div>
      </div>
      <p class="comment-text">${data.comment.text}</p>
    `;

    // Add the new comment to the commentList
    commentList.prepend(commentDiv);
    commentInput.value = '';

    } else {
      alert(data.message || 'An error occure while posting the comment.');
    }
  })
  .catch((error) => {
    console.error('error submitting comment:', error);
  });

  });

      



  function toggleDeleteMenu(postId) {
  const menu = document.getElementById(`delete-menu-${postId}`);
  menu.style.display = menu.style.display === 'none' || menu.style.display === '' ? 'block' : 'none';
}

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
        alert("Post deleted successfully!");
        document.querySelector(`#delete-menu-${postId}`).closest('.post-item').remove();
      } else {
        alert("Error deleting post: " + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert("cant delete post");
    });
  }
}
</script>
</body>
</html>