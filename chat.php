<?php 
session_start();

if(!isset($_SESSION['unique_id'])) {
  header("location:login.php");
}

include 'connectdb.php';


$username = isset($_GET['username']) ? $_GET['username'] : null;

$_SESSION['user_details'] = $_SESSION['user_details'] ?? [];

$userDetails = [
  'firstname' => 'Unknown',
  'lastname' => 'User',
  'profilePicture' => 'images/default.jpg',
];

if ($username) {
  $stmt = $conn -> prepare("SELECT firstname, lastname, profile_picture FROM users WHERE CONCAT(firstname, lastname) = ?");
  $stmt -> bind_param("s", $username);
  $stmt -> execute();
  $stmt -> bind_result($firstname, $lastname, $profilePicture);
  if ($stmt -> fetch()) {
    $userDetails = [
      'firstname' => $firstname,
      'lastname' => $lastname,
      'profilePicture' => $profilePicture ?: 'images/default.jpg',
    ];
  }
  $stmt -> close();
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
    .wrapper img {
      object-fit: cover;
      border-radius: 50%;
    }
    .wrapper header img {
      height: 50px;
      width: 50px;
    }

    .wrapper header span {
      margin-left: 15px;
    }

    .chat-area header{
      display: flex;
      align-items: center;
      padding: 18px 30px;
    }

    .chat-area header .back-icon{
      font-size: 18px;
      color: #333;
      margin: 0 15px;
    }

    .chat-area header span {
      font-size: 17px;
      font-weight: 500;
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

  .chat-area .typing-area {
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
  
  <div class="wrapper">
    <section class="chat-area">
      <header>

      <?php
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
        if(mysqli_num_rows($sql) > 0) {
          $row = mysqli_fetch_assoc($sql);
        }

      ?>

        <a href="message.php" class="back-icon"><i class="fa-solid fa-arrow-left"></i></a>
        <img src="<?php echo htmlspecialchars($userDetails['profilePicture']) ?>" alt="">
        <div class="details">
          <span><?php echo htmlspecialchars($userDetails['firstname'] . ' ' . $userDetails['lastname']); ?></span>
          <!-- <p>Active now</p> -->
        </div>
      </header>
        <div class="chat-box">
          

        </div>
        <form action="#" class="typing-area" autocomplete="off">
          <input type="text" name="outgoing_id" value="<?php echo $_SESSION['username']; ?>" hidden>
          <input type="text" name="incoming_id" value="<?php echo htmlspecialchars($userDetails['firstname']); ?>" hidden>
          <input type="text" name="message" class="input-field" placeholder="Type a message here...">
          <button><i class="fa-solid fa-location-arrow"></i></button>
        </form>
    </section>
  </div>


  <!-- <script src="javascript/chat.js"></script> -->

  <script>

      // chat area
    const form = document.querySelector(".typing-area"),
    inputField = form.querySelector(".input-field"),
    sendBtn = form.querySelector("button"),
    chatBox = document.querySelector(".chat-box");

    form.onsubmit = (e)=> {
      e.preventDefault();
    }

    sendBtn.onclick = () => { 
      
      // AJAX
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "insert-chat.php", true);
      xhr.onload = () => {
        if(xhr.readyState === XMLHttpRequest.DONE) {
          if(xhr.status === 200) {
            inputField.value = "";
            scrollToBottom();
          }
        }
      }

      let formData = new FormData(form);
      xhr.send(formData);

    }

chatBox.onmouseenter = () => {
  chatBox.classList.add("active");
}
chatBox.onmouseleave = () => {
  chatBox.classList.remove("active");
}

setInterval(()=> {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "get-chat.php", true);
  xhr.onload = ()=> {
    if(xhr.readyState === XMLHttpRequest.DONE) {
      if(xhr.status === 200) {
        let data = xhr.response;
        chatBox.innerHTML = data;
        if(!chatBox.classList.contains("active")) {
          scrollToBottom();
        }
      }
    } 
  }
  let formData = new FormData(form);
  xhr.send(formData);
}, 500);


function scrollToBottom() {
  chatBox.scrollTop = chatBox.scrollHeight;
}

  </script>


</body>
</html>