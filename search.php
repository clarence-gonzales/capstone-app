<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <title>BandMate</title>
</head>

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

  .content {
    margin-left: 200px;
    padding: 20px;
    margin-top: 60px;
  }

  .image-section {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 50px;
  }

  .image-box {
    position: relative;
    width: 250px;
    height: 250px;
    border: 5px solid #D9D9D9;
    overflow: hidden;
  }

  .image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .overlay {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    color: #FFFFFF;
    font-weight: bold;
    font-size: 18px;
    background: rgba(0, 0, 0, 0.6);
    padding: 5px 15px;
  }

  /* @media screen and (max-width: 768ppx) {
    .sidebar {
      width: 100%;
      height: auto;
      position: relative;
      padding: 10px 0;
      display: flex;
      justify-content: center;
    }

    .sidebar ul {
      display: flex;
      flex-direction: row;
      justify-content: center;
      width: 100%;
    }

    .sidebar ul li {
      margin: 10px 15px;
    }

    .line {
      display: none;
    }

    .content {
      margin-left: 0;
      padding-top: 20px;
    }

    .image-section {
      flex-direction: column;
      align-items: center;
    }

    .image-box {
      width: 90%;
      height: auto;
    }
  } */

</style>
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

    <div class="content">
      <div class="image-section">
        <div class="image-box">
          <a href="#"><img src="images/Musicianss.jpg" alt="Musicians"></a>
          <div class="overlay">MUSICIANS</div>
        </div>
          <div class="image-box">
            <a href="#"><img src="images/Bands.jpg" alt="Bands"></a>
            <div class="overlay">BANDS</div>
          </div>
        </div>
      </div>

</body>
</html>