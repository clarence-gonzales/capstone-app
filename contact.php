<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <title>Contact</title>

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

    .toggle-wrapper {
      display: flex;
      justify-content: center;
      width: 100%;
      margin-top: 10px;
    }

    .toggle-container {
      display: flex;
      width: 250px;
      border-radius: 25px;
      overflow: hidden;
      border: 2px solid #000000;
    }

    .toggle-button {
      flex: 1;
      padding: 10px;
      text-align: center;
      font-weight: bold;
      cursor: pointer;
    }

    .followers {
      background-color: #D9D9D9;
      border-radius: 25px 0 0 25px;
    }

    .followers a {
      text-decoration: none;
      color: #000000;
    }

    .following {
      background-color: #FFFFFFFF;
      border-radius: 0 25px 25px 0;
    }

    .following a {
      text-decoration: none;
      color: #000000;
    }

    .parent-container {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .followers-container {
      width: 100vh;
      height: 70vh;
      background-color: #E5E5E5;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      margin-top: 5px;
    }

    .followers-header {
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
    }

    .followers-header {
      display: flex;
      padding: 10px;
      margin-bottom: 15px;
    }

    .followers-header i{
      font-size: 25px;
      color: #000000;
    }

    .follower-card {
      display: flex;
      align-items: center;
      background: #FFFFFFFF;
      padding: 10px;
      border-radius: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
      margin-bottom: 15px;
    }

    .follower-card i {
      font-size: 30px;
      color: #000000;
      margin-right: 10px;
    }

    .follower-name  {
      font-weight: bold;
    }

    .no-followers {
      text-align: center;
      font-weight: bold;
      color: #000000;
      margin-top: 100px;
    }

    @media (max-width: 600px) {
      .navbar {
        flex-direction: column;
        align-items: center;
      }
      .icons {
        margin-top: 10px;
      }
      .toggle-container {
        width: 90%;
      }

      .navbar .logo {
        padding-top: 30px;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="logo">BandMate</div>
    <div class="icons">
      <a href="dashboard.php"><i class="fa-solid fa-newspaper"></i></a>
      <a href="search.php"><i class="fa-solid fa-search"></i></a>
      <a href=""><i class="fa-solid fa-users"></i></a>
      <a href="message.php"><i class="fa-solid fa-message"></i></a>
      <a href="profile.php"><i class="fa-solid fa-user"></i></a>
    </div>
  </nav>

  <div class="toggle-wrapper">
    <div class="toggle-container">
      <div class="toggle-button followers">0 Followers</div>
      <div class="toggle-button following"><a href="contactfollowing.php">0 Following</a></div>
    </div>
  </div>

  <div class="parent-container">
    <div class="followers-container">
      <div class="followers-header">
        <i class="fa-solid fa-user-plus"></i><h4>Follower/s</h4>
      </div>

      <div class="follower-card">
        <i class="fa-solid fa-user-circle"></i><h4>Name</h4>
        <hr>
      </div>

      <div class="no-followers">
        You currently have no followers
      </div>
    </div>
  </div>

</body>
</html>