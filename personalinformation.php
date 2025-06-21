<?php 
session_start();
include 'connectdb.php';

if (!isset($_SESSION['username'])) {
  header("location: login.php");
  exit();
}

$username = $_SESSION['username'];

$stmt = $conn -> prepare("SELECT profile_picture FROM users WHERE username = ?");
$stmt -> bind_param("s", $username);
$stmt -> execute();
$stmt -> bind_result($profilePicture);
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
  <title>Document</title>

  <style>
    * {
      font-family: 'Poppins', sans-serif;
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

    .camera {
      color: red;
      font-size: 30px;
      margin-left: -60px;
      margin-top: 170px;
    }

    .header {
      margin: 20px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
  </style>

</head>
<body>
<a href="editprofile.php" class="edit-profile"><i class="fa-solid fa-arrow-left"></i>&nbsp;Edit Profile</a>

<div class="container">
  <div class="header">
    <img id="profileImage" src="<?php echo $profilePicture; ?>" width="150" height="150" style="border-radius: 50%;">
    <label for="fileInput">
      <i class="fa-solid fa-camera camera" style="cursor: pointer;"></i>
    </label>
    <input type="file" id="fileInput" style="display: none;" accept="image/*">
  </div>
</div>

<script>
  document.getElementById("fileInput").addEventListener('change', function() {
    if (this.files && this.files[0]) {
      let formData  = new FormData();
      formData.append("profile_picture", this.files[0]);

      fetch("upload_profile.php", {
        method: "POST",
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById("profileImage").src = data.newImageUrl;
        } else {
          alert("Failed to upload image.");
        }
      })
      .catch(error => console.error("Error:", error));
    }
  })
</script>
</body>
</html>