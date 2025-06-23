<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

include 'connectdb.php';
if (!$conn) {
  die("Database connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT firstname, lastname, profile_picture FROM users WHERE username = ?");
$stmt->bind_param("s", $username); 
$stmt->execute();
$stmt->bind_result($firstName, $lastName, $profilePicture);
$stmt->fetch();
$stmt->close();

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
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <title>BandMate - Profile</title>
  <style>
    :root {
      --primary-color: #4361ee;
      --primary-dark: #3a56d4;
      --text-color: #2b2d42;
      --light-gray: #f8f9fa;
      --medium-gray: #e9ecef;
      --dark-gray: #6c757d;
      --border-color: #e1e1e1;
      --hover-bg: #f5f5f5;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
      color: var(--text-color);
      line-height: 1.6;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .profile-header {
      display: flex;
      align-items: center;
      padding: 30px 0;
      border-bottom: 1px solid var(--border-color);
      margin-bottom: 20px;
    }

    .profile-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 30px;
      border: 3px solid white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .profile-info h2 {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 5px;
      color: var(--text-color);
    }

    .profile-info p {
      color: var(--dark-gray);
      font-size: 16px;
      margin-bottom: 15px;
    }

    .profile-menu {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      margin-top: 20px;
    }

    .menu-item {
      display: flex;
      align-items: center;
      padding: 18px 25px;
      transition: all 0.2s ease;
      border-bottom: 1px solid var(--border-color);
      text-decoration: none;
      color: var(--text-color);
    }

    .menu-item:last-child {
      border-bottom: none;
    }

    .menu-item:hover {
      background-color: var(--hover-bg);
    }

    .menu-item i {
      font-size: 20px;
      width: 30px;
      color: var(--primary-color);
      margin-right: 15px;
    }

    .menu-item h4 {
      font-size: 16px;
      font-weight: 500;
      flex: 1;
    }

    .menu-item .arrow {
      color: var(--dark-gray);
      font-size: 14px;
    }

    .divider {
      height: 8px;
      background-color: var(--light-gray);
      border: none;
      margin: 0;
    }

    .logout-item {
      color: #e63946;
    }

    .logout-item i {
      color: #e63946;
    }

    @media (max-width: 768px) {
      .profile-header {
        flex-direction: column;
        text-align: center;
        padding: 20px 0;
      }
      
      .profile-avatar {
        margin-right: 0;
        margin-bottom: 20px;
      }
      
      .profile-info {
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <?php include_once 'component/navs.php'; ?>
  
  <div class="container">
    <div class="profile-header">
      <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" class="profile-avatar">
      <div class="profile-info">
        <h2><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h2>
        <p>Member since <?php echo date('Y'); ?></p>
      </div>
    </div>
    
    <div class="profile-menu">
      <a href="editprofile.php" class="menu-item">
        <i class="fas fa-pencil-alt"></i>
        <h4>Edit Profile</h4>
        <i class="fas fa-chevron-right arrow"></i>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-user"></i>
        <h4>View Profile</h4>
        <i class="fas fa-chevron-right arrow"></i>
      </a>
      
      <hr class="divider">
      
      <a href="#" class="menu-item">
        <i class="fas fa-cog"></i>
        <h4>Preferences</h4>
        <i class="fas fa-chevron-right arrow"></i>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-info-circle"></i>
        <h4>About</h4>
        <i class="fas fa-chevron-right arrow"></i>
      </a>
      
      <hr class="divider">
      
      <a href="logout.php" class="menu-item logout-item">
        <i class="fas fa-sign-out-alt"></i>
        <h4>Logout</h4>
        <i class="fas fa-chevron-right arrow"></i>
      </a>
    </div>
  </div>
</body>
</html>e