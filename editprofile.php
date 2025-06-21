<?php 
session_start();
include 'connectdb.php';

if (!isset($_SESSION['username'])) {
  header("location: login.php");
  exit();
}

$username = $_SESSION['username'];

$stmt = $conn -> prepare("SELECT firstname, lastname, profile_picture FROM users WHERE username = ?");
$stmt -> bind_param("s", $username);
$stmt -> execute();
$stmt -> bind_result($firstName, $lastName, $profilePicture);
$stmt -> fetch();
$stmt -> close();

$defaultProfilePicture = "images/default.jpg";
$profilePicture = $profilePicture ? $profilePicture : $defaultProfilePicture;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <title>Edit Profile</title>

  <style>
    * {
      font-family: 'Poppins', sans-serif;
    }

    .container {
      width: 90%;
      padding: 20px;
      border-radius: 5px;
      background-color: #D9D9D9;
      box-shadow: 0 0 10px black;
      margin: 20px auto;
    }

    .header {
      display: flex;
    }

    .edit-profile {
        width: 100%;
        margin-left: -8px;
        margin-top: -8px;
        display: inline-flex;
        align-items: center;
        font-weight: bold;
        font-size: 16px;
        color: #FFFFFF;
        text-decoration: none;
        background-color: black;
        padding: 10px;
    }

    .personal-info-banner {
      width: 90%;
      margin: auto;
      border-radius: 5px;
      background-color: #D9D9D9;
      padding: 10px;
      display: flex;
      align-items: center;
    }

    .containerr {
      background-color: #E2E2E2;
      width: 93%;
      margin: auto;
      border-radius: 5px;
    }
        
    .profile-section {
      display: flex;
      align-items: center;
      padding: 10px;
      border-bottom: 2px solid #ccc;
    }

    .profile-icon {
      width: 30px;
      height: 30px;
      margin-right: 10px;
      font-size: 30px;
    }

    .profile-text {
      color: #555;
      font-size: 14px;
      margin-left: 10px;
      font-weight: bold;
    }

    .profile-text a {
      text-decoration: none;
      color: #000000;
    }

    img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      margin: 10px 10px;
      margin-top: -10px;
    }


  </style>

</head>
<body>
  <a href="profile.php" class="edit-profile"><i class="fa-solid fa-arrow-left"></i>&nbsp;Edit Profile</a>
  
<div class="container">
  <div class="header">
    <img src="<?php echo $profilePicture; ?>">
    <h2><?php  echo $firstName . " " . substr($lastName, 0, 1) . "."; ?></h2>
  </div>
</div>

<div class="containerr">
  <div class="profile-section">
    <i class="fas fa-user-circle profile-icon"></i>
    <div class="profile-text">
      <a href="personalinformation.php">Personal Information</a><br>
      <span style="font-size: 12px; color: #777;">Tap to Edit</span>
    </div>
  </div>

  <div class="profile-section">
    <i class="fas fa-music profile-icon"></i>
    <div class="profile-text">
      <a href="#">Music Style</a><br>
      <span style="font-size: 12px; color: #777;">Tap to Edit</span>
    </div>
  </div>

  <div class="profile-section">
    <i class="far fa-smile profile-icon"></i>
    <div class="profile-text">
      <a href="#">Your Interests</a><br>
      <span style="font-size: 12px; color: #777;">Tap to Edit</span>
    </div>
  </div>

  <div class="profile-section">
    <i class="fas fa-drum profile-icon"></i>
    <div class="profile-text">
      <a href="#">Your Instruments</a><br>
      <span style="font-size: 12px; color: #777;">Tap to Edit</span>
    </div>
  </div>
</div>
</body>
</html>