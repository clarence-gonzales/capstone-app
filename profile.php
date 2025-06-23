<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

include 'connectdb.php';
if (!$conn) {
  die("Database connection failed: " . $conn -> connect_error);
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT firstname, lastname, profile_picture FROM users WHERE username = ?");
$stmt -> bind_param("s", $username); 
$stmt -> execute();
$stmt -> bind_result($firstName, $lastName, $profilePicture);
$stmt -> fetch();
$stmt -> close();

if (!$firstName || !$lastName){
  $firstName = "Unknown";
  $lastName = "Unknown";
}

$defaultProfilePicture = "images/default.jpg";
$profilePicture = !empty($profilePicture) ? $profilePicture : $defaultProfilePicture;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
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

    

    .profile {
      display: flex;
      align-items: center;
    }

    img {
      margin: 5px 5px 5px;
    }

    .profile h2 {
      font-size: 24px;
      margin-left: 10px;
    }

    hr {
      margin-top: 20px;
    }

    .edit-view {
      display: flex;
      align-items: center;
      gap: 8px;
      position: relative;
      padding: 10px 0;
    }

    .edit-view h4 {
      margin: 0;
      font-size: 16px;
    }

    .edit-view a {
      text-decoration: none;
      color: #000000;
    }

    .icon i {
      font-size: 20px;
      margin-left: 15px;
      margin-top: 5px;
      display: flex;
      color: #000000;
    }

    .color-box {
      background-color: #333;
      height: 20px;
      width: 100%;
    }

    .line1 {
      margin-bottom: 0;
    }

    .line2 {
      margin-top: 1px;
    }

    .edit-view1 {
      align-items: center;
    }

    .edit-view1 h4 {
      margin: 0;
      margin-left: 20px;
      font-size: 16px;
    }

    .edit-view1 a {
      text-decoration: none;
      color: #000000;
    }

    .line3 {
      margin: 0 0;
    }
  </style>
</head>
<body>
<?php include_once 'component/navs.php'; ?>

  <div class="profile">
    <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" width="100" height="100" style="border-radius: 50%;">
    <h2><?php echo $firstName . ' ' . $lastName; ?></h2>
  </div>
  <div class="icon">
    <hr>
    <div class="edit-view">
      <i class="fa-solid fa-pencil" class="icon"></i>
      <h4><a href="editprofile.php">Edit Profile</a></h4>
    </div>
    <hr>
    <div class="edit-view">
      <i class="fa-solid fa-user"></i>
      <h4><a href="#">View Profile</a></h4>
    </div>
  
    <div>
      <hr class="line1">
      <div class="color-box"></div>
      <hr class="line2">
    </div>
    <div class="edit-view1">
      <h4><a href="#">Preferences</a></h4>
    </div>
    <hr class="line3">
    <div class="edit-view1">
      <h4><a href="#">About</a></h4>
    </div>
    <hr class="line3">
    <div class="edit-view1">
      <h4><a href="logout.php">Logout</a></h4>
    </div>
    <hr class="line3">
  </div>
</body>
</html>