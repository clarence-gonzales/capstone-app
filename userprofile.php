<?php
session_start();

include 'connectdb.php';

$username = isset($_GET['username']) ? $_GET['username'] : $_SESSION['username'];

// fetch user details
$stmt = $conn -> prepare("SELECT firstname, lastname, profile_picture FROM users WHERE username = ?");
$stmt -> bind_param("s", $username);
$stmt -> execute();
$stmt -> bind_result($firstname, $lastname, $profilePicture);
$stmt -> fetch();
$stmt -> close();

// default pfp
$defaultProfilePicture = "images/default.jpg";
$profilePicture = $profilePicture ? $profilePicture : $defaultProfilePicture;

$_SESSION['userDetails_' . $_SESSION['username']] = [
  'firstname' => $firstname,
  'lastname' => $lastname,
  'profilePicture' => $profilePicture,
];

// Messagw
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $conn -> prepare("INSERT INTO messages (username, firstname, lastname, profile_picture, created_at) VALUES (?, ?, ?, ?, NOW())");
  $stmt -> bind_param("ssss", $_SESSION['username'], $firstname, $lastname, $profilePicture);
  $stmt -> execute();
  $stmt -> close();
  header("Location: message.php");
  exit;
}

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

  <div class="container">
    <div class="prof">
      <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture">
      <div class="details">
        <div class="header">
          <h1><?php echo htmlspecialchars($firstname); ?></h1>
          <div class="buttons">
            <form method="POST" style="display: inline;"></form>
              <button class="follow-btn">Follow</button>
              <button type="submit" class="message-btn" onclick="window.location.href='message.php?username=<?php echo urlencode($username); ?>'">Message</button>
          </div>
        </div>
        <div class="full-name">
            <h3 class="fullname"><?php echo $firstname . ' ' . $lastname ?></h3>
          </div>
      </div>
    </div>
  </div>

</body>
</html>