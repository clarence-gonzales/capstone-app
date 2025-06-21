<?php
session_start();
include 'connectdb.php';

$recaptchaSecret = '6LfAQCkrAAAAAPRd5mtO3CXq2rzQfeEfEAjz4QwG';

if (isset($_POST['logIn'])) {
    $userName = $_POST['username'];
    $password = md5($_POST['password']);

    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
    $responseData = json_decode($verifyResponse);
    
if (!$responseData->success) {
    echo "<script>alert('CAPTCHA verification failed. Please try again.');</script>";
    } else {

    // Query the database for user details
    $sql = "SELECT * FROM users WHERE username = '$userName' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $_SESSION['user_id'] = $row['id'];


        // Store user data in session
        // $_SESSION['username'] = $row['username'];
        // $_SESSION['firstName'] = $row['firstName'];
        // $_SESSION['lastName'] = $row['lastName'];

        $_SESSION['userDetails'] = [
          'username' => $row['username'],
          'firstname' => $row['firstName'],
          'lastname' => $row['lastName'],
          'profilePicture' => $row['profile_picture'] ?? 'images/default.jpg'
      ];

        header("location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Incorrect Username or Password');</script>";
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <title>BandMate</title>

<style>
  body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }
  
  .container {
    width: 400px;
    background: white;
    padding: 20px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
    border-radius: 10px;
  }
  h2 {
    text-align: center;
  }
  input, select {
    width: 90%;
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  button {
    width: 100%;
    background: #333;
    color: white;
    padding: 10px;
    border: none;
    cursor: pointer;
    border-radius: 25px;
    margin-top: 10px;
    font-weight: bold;
    font-size: 16px;
    transition: 0.3s;
  }
  button:hover {
    opacity: 0.7;
  }

  .fpassword {
    font-size: 16px;
    text-align: center;
    margin-top: 5px;
    margin-bottom: 5px;
  }

  .fpassword a {
    text-decoration: none;
    color: #2979FF;
  }

  .fpassword a:hover {
    text-decoration: underline;
  }

  .login-link {
    text-align: center;
    margin-top: 10px;
  }

  .login-link a{
    text-decoration: none;
    color: #2979FF;
  }

  .login-link a:hover {
    text-decoration: underline;
  }

</style>
</head>
<body>


  <div class="container">
    <h2 class="heading"><span style="color: gray;">Welcome to </span>BandMate</h2>
      <p style="text-align: center;">Log in and enjoy the experience!</p>
      <hr>

      <form action="register.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <p class="fpassword"><a href="#">forgot password</a></p>
        <div class="g-recaptcha" data-sitekey="6LfAQCkrAAAAAPRd5mtO3CXq2rzQfeEfEAjz4QwG"></div>

        <button type="submit" name="logIn" class="btn">Log In</button>

        <p class="login-link">Don't have an account? <a href="signup.php">Signup</a></p>
      </form>
  </div>

</body>
</html>
