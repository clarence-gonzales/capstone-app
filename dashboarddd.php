<?php
session_start();
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
  // Process form submission
}


// post submission
if (isset($_POST['post_submit'])) {
  $POSTcONTENT = $_POST['post_content'];
  $image = null;

  // image
  if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $imageName = $_FILES['image']['name'];
    $imageTemp = $_FILES['image']['tmp_name'];
    $imagePath = "uploads/" . $imageName;

    move_uploaded_file($imageTemp, $imagePath);
    $image = $imagePath;
}

// Insert post into database
$stmt = $conn->prepare("INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $userId, $postContent, $image);
$stmt->execute();
$stmt->close();
}

// Fetch all posts
$stmt = $conn->prepare("SELECT p.content, p.image, p.created_at, u.firstname, u.lastname, u.profile_picture FROM posts p INNER JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
$stmt->execute();
$stmt->bind_result($content, $image, $created_at, $firstname, $lastname, $profile_picture);
$posts = [];
while ($stmt->fetch()) {
    $posts[] = [
        'content' => $content,
        'image' => $image,
        'created_at' => $created_at,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'profile_picture' => $profile_picture ? $profile_picture : $defaultProfilePicture
    ];

    $stmt->close();

    
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <title>Dashboard</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
    }

    .navbar {
      display: flex;
      align-items: center;
      background-color: #D9FFF5;
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
      color: #30DFAB;
    }

    /* create post */
    .create-post {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .context {
      width: 100vh;
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
      margin-left: -300px;
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
      color: #D9D9D9;
      resize: vertical;
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

    .post-profile-picture {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      margin-right: 10px;
    }

    .post-name {
      font-weight: bold;
      margin-bottom: 5px;
      margin-left: 54px;
      margin-top: -47px;
    }

    .main-container {
      display: flex;
      flex-direction: column;
    }

    #post-container {
      border: 1px solid #ccc;
      padding: 10px;
      margin-top: 10px;
      border-radius: 5px;
    }

    .post-item {
      border: 1px solid #ccc;
      padding: 10px;
      margin-bottom: 10px;
      margin-top: 10px;
      /* display: flex; */
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
      height: 200px;
      width: 400px;
      margin: 10px auto;
      background-color: #B6B6B6;
      border-radius: 10px;
    }

    .user-info {
      display: flex;
      align-items: center;
      margin-right: 10px;
      align-items: flex-start;
    }

    .post-content {
      /* margin-right: 50px; */
      margin-top: 30px;
      /* color: white; */
    }

    .post-time {
      font-size: 0.8rem;
      color: #666;
      margin-top: 10px;
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
  </div>
  <!-- <div id="posts-container"></div> -->


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

  <form action="dashboarddd.php" method="POST" enctype="multipart/form-data">
      <textarea placeholder="What's on your mind?" name="post_content" required><?php echo isset($_POST['post_content']) ? htmlspecialchars($_POST['post_content']) : ''; ?></textarea>

      <!-- Image upload -->
    <div id="image-preview-container">
        <img id="image-preview" src="" alt="Image Preview">
    </div>
      
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
      <input type="file" id="file-input" style="display: none;" accept="image/*" multiple>
      </div>
    </div>

      <button type="submit" name="submit_post" class="post">Post</button>
</form>
  </div>

  <div id="post-container">
    <?php foreach ($posts as $post): ?>
        <div class="post-item">
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" class="post-profile-picture" alt="Profile Picture">
                <div>
                    <p class="post-name"><?php echo htmlspecialchars($post['firstname']) . ' ' . htmlspecialchars($post['lastname']); ?></p>
                    <p class="post-time"><?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></p>
                </div>
            </div>
            <div class="post-content">
                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                <?php if ($post['image']): ?>
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" style="max-width: 100%; height: auto; border-radius: 10px;">
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
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

  // Post button and Posting
  document.addEventListener("DOMContentLoaded", function() {
  const postButton = document.querySelector(".post");
  const textarea = document.querySelector('textarea');
  const postsContainer = document.getElementById("posts-container");

  const profilePicture = "<?php echo $profilePicture; ?>";
  const firstName = "<?php echo $firstName; ?>";
  const lastName = "<?php echo $lastName; ?>";

  postButton.addEventListener("click", function(event) {
    // event.preventDefault(); // Prevent form submission

    const postContent = textarea.value;
    if (postContent.trim() !== "") {
      const newPost = document.createElement("div");
      newPost.classList.add("post-item");
      newPost.innerHTML = `
        <img src="<?php echo $profilePicture; ?>" alt="<?php echo $firstName . ' ' . $lastName; ?>" class="post-profile-picture">
        <div class="post-name"><?php echo $firstName . ' ' . $lastName; ?></div>
        <p class="post-content">${postContent}</p>
      `;
      postsContainer.appendChild(newPost);
      textarea.value = ""; // Clear the textarea
    }
  });
});

</script>
</body>
</html>