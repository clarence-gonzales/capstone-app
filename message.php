<?php
session_start();

if(!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}

include 'connectdb.php';

$username = isset($_GET['username']) ? $_GET['username'] : null;

$loggedInUsername = $_SESSION['username'];

// Use a session key that is unique per user
$userCardsKey = 'userCards_' . $loggedInUsername;

$userCards = $_SESSION[$userCardsKey] ?? [];
if (isset($_GET['username'])) {
  $username = $_GET['username'];
  // $_SESSION['userCards'] = [];
  
  $stmt = $conn -> prepare("SELECT firstname, lastname, profile_picture FROM users WHERE username = ?");
  $stmt -> bind_param("s", $username);
  $stmt -> execute();
  $stmt -> bind_result($firstname, $lastname, $profilePicture);
  if ($stmt -> fetch()) {
    $userDetails = [

      'firstname' => $firstname,
      'lastname' => $lastname,
      'profilePicture' => $profilePicture ?: 'images/default.jpg',
    ];

    $exists = false;
    foreach ($userCards as $card) {
      if ($card['firstname'] === $firstname && $card['lastname'] === $lastname) {
        $exists = true;
        break;
      }
    }
    
    if (!$exists) {
    array_unshift($userCards, $userDetails);
    }
  }
  $stmt -> close();

  $_SESSION[$userCardsKey] = $userCards;
}

// Message fetch
$stmt = $conn -> prepare("SELECT m.id, m.message, m.created_at, u.firstname, u.lastname, u.profile_picture FROM sendmessages m
JOIN users u ON  m.sender_username = u.username
WHERE m.receiver_username = ?
ORDER BY m.created_at DESC");
$stmt->bind_param("s", $loggedInUsername);
$stmt -> execute();
$stmt -> bind_result($messageId, $messageContent,$createdAt, $firstname, $lastname, $profilePicture);
$messages = [];
$_SESSION['message_ids'] = [];
while ($stmt -> fetch()) {
  $messages[] = [
    'id' => $messageId,
    'message' => $messageContent,
    'firstname' => $firstname,
    'lastname' => $lastname,
    'profilePicture' => $profilePicture,
  ];
}

$stmt -> close();


  $displayCards = array_merge($userCards, $messages);

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

    

    /* message header */
    .toggle-wrapper {
      display: flex;
      justify-content: center;
      width: 100%;
      margin-top: 10px;
    }

    .toggle-container {
      display: flex;
      width: 250px;
      /* border-radius: 25px; */
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

    /* message container */
    .parent-container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding-left: 20px;
      padding-right: 20px;
    }

    .message-container {
      width: 150vh;
      height: 85vh;
      background-color: #292929;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      margin-top: 5px;
    }

    .message-header {
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
      padding: 10px;
      color: #FFFFFFFF;
    }


    .message-header i{
      font-size: 25px;
      color: #ffffffff;
    }

    /* serch style */
    .message-container .search {
      margin: 20px 0;
      display: flex;
      position: relative;
      align-items: center;
    }

    .message-container .search input {
      /* position: absolute; */
      height: 42px;
      width: calc(100% - 50px);
      border: 1px solid #ccc;
      padding: 0 13px;
      font-size: 16px;
      border-radius: 5px 0 0 5px;
      outline: none;
      /* opacity: 0; */
      pointer-events: none;
      transition: all 0.3s ease;
    }

    .message-container .search input.active {
      /* opacity: 1; */
      pointer-events: auto;
    }

    .message-container .search button {
      width: 47px;
      height: 42px;
      border: none;
      outline: none;
      color: #333;
      background: #fff;
      font-size: 17px;
      border-radius: 0 5px 5px 0;
      cursor: pointer;
      font-size: 17px;
      border-radius: 0 5px 5px 0;
      transition: all 0.3s ease;
    }

    .message-container .search button.active {
      color: #fff;
      background: #333;
    }

    .message-container .search button.active i::before {
      content: "\f00d";
    }

    /* message card */
    .message-card {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      background: #222222;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
      margin-bottom: 15px;
      height: 450px;
      overflow-y: auto;
    }

    :is(.message-card)::-webkit-scrollbar {
      width: 0px;
    }

    .message-card img {
      height: 40px;
      width: 40px;
      border-radius: 50%;
      margin-right: 10px;
    }

    .profile-container h4 {
      font-size: 18px;
    }

    .profile-container {
      display: flex;
      align-items: center;
      gap: 2px;
      padding: 15px;
      border-radius: 10px;
      width: 100%;
      color: #FFFFFF;
      cursor: pointer;
    }

    .profile-container:hover {
      background-color: #444444;
    }

    .three-dots {
      font-size: 20px;
      color: #888888;
      display: none;
      margin-left: auto;
      background-color: #222222;
      border-radius: 50%;
      padding: 10px 10px;
      cursor: pointer;
      transition: background-color 0.3s ease, outline-color 0.3s ease;
      box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.2);
      outline: 2px solid #888888;
    }

    .three-dots:hover {
      background-color: #29292929;
      box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.4);
      outline-color: #888888; 
    }

    .profile-container:hover .three-dots {
      display: block;
    }

    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
      background-color: #F8F8F8;
      box-shadow: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      width: 150vh;
      height: 90vh;
      display: flex;
      flex-direction: column;
      overflow-y: hidden;
      background: #FFFFFF;
      box-shadow: rgba(0, 0, 0, 0.5);
    }

    .profile-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      width: 100%;
      border-bottom: 2px solid #ccc;
      padding-bottom: 10px;
    }

    .profile-details {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #333;
    }

    .profile-details img {
      height: 80px;
      width: 80px;
      border-radius: 50%;
    }

    .user-info {
      display: flex;
      flex-direction: column; /* Stack elements vertically */
    }

    .profile-details h4 {
      margin: 0;
      font-size: 20px;
      font-weight: bold;
    }

    .profile-details p {
      margin: 0;
      font-size: 14px;
      font-weight: 300;
    }

    .close-btn {
      border: none;
      font-size: 30px;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
      color: #333;
      transition: 0.3s;
    }

    .close-btn:hover {
      opacity: 0.7;
    }

    .modal-body {
      flex: 1;
      overflow-y: auto;
      margin-bottom: 10px;
    }

    /* dropdown */
    .dropdown-menu {
      display: none;
      position: absolute;
      /* top: 350px; */
      left: 500px;
      background-color: #222222;
      border: 1px solid #444444;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
      z-index: 1000;
      padding: 10px;
      list-style: none;
      margin: 0;
      width: 150px;
    }

  .dropdown-menu li {
    padding: 10px;
    color: #FFFFFF;
    cursor: pointer;
  }

  .dropdown-menu li:hover {
    background-color: #444444;
    border-radius: 10px;
  }

  /* Position adjustment for dropdown menu */
  .three-dots-container {
    position: relative;
  }

  .three-dots {
    z-index: 1;
  }


  /* Chat Box */
  .chat-box {
    height: 500px;
    overflow-y: auto;
    background: #F7F7F7;
    border-radius: 10px;
    padding: 10px 30px 20px 30px;
    box-shadow: inset 0 32px 32px -32px rgb(0 0 0 / 5%),
                inset 0 -32px 32px -32px rgb(0 0 0 / 5%);
  }

  :is(.chat-box)::-webkit-scrollbar {
    width: 0px;
  }

  .chat-box .chat p {
    margin: 15px 0;
  }

  .chat-box .chat p {
    word-wrap: break-word;
    padding: 8px 16px;
    box-shadow: 0 0 32px rgb(0 0 0 / 8%),
                0 16px 16px -16px rgb(0 0 0 / 10%);
  }

  .chat-box .outgoing {
    display: flex;
  }

  .outgoing .details {
    margin-left: auto;
    max-width: calc(100% - 130px);
  }

  .outgoing .details p {
    background: #333;
    color: #FFF;
    border-radius: 18px 18px 0 18px;
  }

  .chat-box .incoming {
    display: flex;
    align-items: flex-end;
  }

  .chat-box .incoming img {
    height: 35px;
    width: 35px;
    border-radius: 50%;
  }

  .incoming .details {
    margin-left: 10px;
    margin-right: auto;
    max-width: calc(100% - 130px);
  }

  .incoming .details p {
    margin-left: 10px;
  }

  .incoming .details p {
    color: #333;
    background: #FFF;
    border-radius: 18px 18px 18px 0;
  }

  .modal .typing-area {
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
  }

  .typing-area input{
    height: 45px;
    width: calc(100% - 58px);
    font-size: 17px;
    border: 1px solid #ccc;
    padding: 0 13px;
    border-radius: 5px 0 0 5px;
    outline: none;
  }

  .typing-area button {
    width: 55px;
    border: none;
    outline: none;
    background: #333;
    color: #fff;
    font-size: 19px;
    cursor: pointer;
    border-radius: 0 5px 5px 0;
    transition: 0.3s;
  }

  .typing-area button:hover {
    opacity: 0.7;
  }

  </style>
</head>
<body>
  <?php include_once "component/navs.php"; ?>

  <header>
    <?php
      include_once "connectdb.php";
      $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
      if(mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);
      }
    ?>
  </header>

  <!-- <div class="toggle-wrapper">
    <div class="toggle-container">
      <div class="toggle-button followers">Inbox</div>
    </div>
  </div> -->

  <div class="parent-container">
    <div class="message-container">
      <div class="message-header">
        <i class="fa-solid fa-message"></i><h4>Messages</h4>
      </div>

      <div class="search">
        <input type="text" placeholder="Enter name to search...">
        <button><i class="fa-solid fa-search"></i></button>
      </div>

      <!-- user details -->
      <div class="message-card">
      <?php if (!empty($displayCards)): ?>
        <?php foreach ($displayCards as $card): ?>

          <div class="profile-container" data-username="<?php echo htmlspecialchars($card['firstname'] . $card['lastname']); ?>">
          <img src="<?php echo htmlspecialchars($card['profilePicture']); ?>" alt="Profile Picture">
            <h4><?php echo htmlspecialchars($card['firstname'] . ' ' . $card['lastname']); ?></h4>
            <i class="fa-solid fa-ellipsis three-dots"></i> 
            <ul class="dropdown-menu">
              <li onclick="deleteChat()">Delete Chat</li>
                <!-- <li onclick="markAsRead()">Mark as Read</li> -->
            </ul>
          </div>

      <?php endforeach ?>
      <?php else: ?>
        <div class="message-card">
          <i class="fa-solid fa-user-circle"></i>
          <h4>No message</h4>
        </div>
        <?php endif; ?>
      </div>

        <!-- message display -->
        
    </div>
  </div>

  <script>

    // user details in message card
    function openModal(profilePicture, fullName) {
      document.getElementById('profilePicture').src = profilePicture;
      document.getElementById('fullName').textContent = fullName;
      document.getElementById('userModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('userModal').style.display = 'none';
    }

    document.querySelectorAll('.profile-container').forEach(container => {
      container.addEventListener('click', function () {

        const profilePicture = this.querySelector('img').src;
        const fullName = this.querySelector('h4').textContent;
        openModal(profilePicture, fullName);
      });
    });

    // Search in message-card
    const searchBar = document.querySelector(".message-container .search input"),
    searchBtn = document.querySelector(".message-container .search button");
    messageCard = document.querySelector(".message-card");

    searchBtn.onclick = ()=> {
      searchBar.classList.toggle("active");
      searchBar.focus(); 
      searchBtn.classList.toggle("active");
    }


    // three dots
    document.querySelectorAll('.three-dots').forEach(threeDots => {
      threeDots.addEventListener('click', function (event) {
        event.stopPropagation();
        const dropdown = this.nextElementSibling;
        closeAllDropdowns();
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' :'block';
      });
    });

    document.addEventListener('click', closeAllDropdowns);

    function closeAllDropdowns() {
      document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.style.display = 'none';
      });
    }

    document.querySelectorAll('.profile-container').forEach(container => {
    container.addEventListener('click', function () {
    const username = this.getAttribute('data-username');
    if (username) {
      window.location.href = `chat.php?username=${encodeURIComponent(username)}`;
    }
  });
});

  </script>

  

  <!-- <script src="javascript/chat.js"></script> -->

</body>
</html>
