<?php 
session_start();
include 'connectdb.php';

if (!isset($_SESSION['username'])) {
  header("location: login.php");
  exit();
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT firstname, lastname, profile_picture FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($firstName, $lastName, $profilePicture);
$stmt->fetch();
$stmt->close();

$defaultProfilePicture = "images/default.jpg";
$profilePicture = $profilePicture ? $profilePicture : $defaultProfilePicture;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <title>Edit Profile | BandMate</title>
  <style>
    :root {
      --primary-color: #4361ee;
      --primary-dark: #3a56d4;
      --text-color: #2b2d42;
      --light-gray: #f8f9fa;
      --medium-gray: #e9ecef;
      --dark-gray: #6c757d;
      --border-color: #e1e1e1;
      --card-bg: #ffffff;
      --section-bg: #f5f7fa;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--section-bg);
      color: var(--text-color);
      line-height: 1.6;
    }

    .header-bar {
      background-color: #30DFAB;
      color: white;
      padding: 15px 20px;
      display: flex;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .back-button {
      color: white;
      text-decoration: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: opacity 0.2s;
    }

    .back-button:hover {
      opacity: 0.8;
    }

    .back-button i {
      font-size: 18px;
    }

    .profile-container {
      max-width: 800px;
      margin: 20px auto;
      padding: 0 20px;
    }

    .profile-card {
      background-color: var(--card-bg);
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      padding: 20px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }

    .profile-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      margin-right: 20px;
    }

    .profile-info h2 {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .profile-info p {
      color: var(--dark-gray);
      font-size: 14px;
    }

    .settings-container {
      background-color: var(--card-bg);
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      overflow: hidden;
    }

    .settings-item {
      display: flex;
      align-items: center;
      padding: 18px 20px;
      border-bottom: 1px solid var(--border-color);
      text-decoration: none;
      color: var(--text-color);
      transition: background-color 0.2s;
    }

    .settings-item:last-child {
      border-bottom: none;
    }

    .settings-item:hover {
      background-color: var(--light-gray);
    }

    .settings-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: rgba(67, 97, 238, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      color: var(--primary-color);
      font-size: 18px;
    }

    .settings-details {
      flex: 1;
    }

    .settings-details h3 {
      font-size: 16px;
      font-weight: 500;
      margin-bottom: 3px;
    }

    .settings-details p {
      font-size: 13px;
      color: var(--dark-gray);
    }

    .settings-arrow {
      color: var(--dark-gray);
      font-size: 14px;
    }

    @media (max-width: 600px) {
      .profile-card {
        flex-direction: column;
        text-align: center;
        padding: 25px 20px;
      }
      
      .profile-avatar {
        margin-right: 0;
        margin-bottom: 15px;
      }
      
      .profile-info {
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <div class="header-bar">
    <a href="profile.php" class="back-button">
      <i class="fas fa-arrow-left"></i>
      <span>Edit Profile</span>
    </a>
  </div>
  
  <div class="profile-container">
    <div class="profile-card">
      <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="profile-avatar">
      <div class="profile-info">
        <h2><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h2>
        <p>Update your profile information</p>
      </div>
    </div>
    
    <div class="settings-container">
      <a href="personalinformation.php" class="settings-item">
        <div class="settings-icon">
          <i class="fas fa-user"></i>
        </div>
        <div class="settings-details">
          <h3>Personal Information</h3>
          <p>Update your name, profile picture, and basic details</p>
        </div>
        <i class="fas fa-chevron-right settings-arrow"></i>
      </a>
      
      <a href="#" class="settings-item">
        <div class="settings-icon">
          <i class="fas fa-music"></i>
        </div>
        <div class="settings-details">
          <h3>Music Style</h3>
          <p>Select your preferred music genres and styles</p>
        </div>
        <i class="fas fa-chevron-right settings-arrow"></i>
      </a>
      
      <a href="#" class="settings-item">
        <div class="settings-icon">
          <i class="fas fa-heart"></i>
        </div>
        <div class="settings-details">
          <h3>Your Interests</h3>
          <p>Tell others about your musical interests</p>
        </div>
        <i class="fas fa-chevron-right settings-arrow"></i>
      </a>
      
      <a href="#" class="settings-item">
        <div class="settings-icon">
          <i class="fas fa-drum"></i>
        </div>
        <div class="settings-details">
          <h3>Your Instruments</h3>
          <p>List the instruments you play</p>
        </div>
        <i class="fas fa-chevron-right settings-arrow"></i>
      </a>
    </div>
  </div>
</body>
</html>