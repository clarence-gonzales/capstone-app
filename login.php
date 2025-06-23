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
          // profile default?
          'profilePicture' => $row['profile_picture'] ?? 'images/default.jpg'
          // profile default?
      ];

        header("location: dashboard.php");
        exit();
    } else {
        header("location: login.php");;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BandMate | Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    :root {
      --primary-color: #4361ee;
      --primary-dark: #3a56d4;
      --text-color: #2b2d42;
      --light-gray: #f8f9fa;
      --medium-gray: #e9ecef;
      --dark-gray: #6c757d;
      --error-color: #ef233c;
      --success-color: #4cc9f0;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light-gray);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      color: var(--text-color);
      line-height: 1.6;
      background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    .login-container {
      width: 100%;
      max-width: 420px;
      background: white;
      padding: 2.5rem;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      margin: 1rem;
    }
    
    .logo {
      text-align: center;
      margin-bottom: 1.5rem;
    }
    
    .logo h1 {
      font-size: 2rem;
      font-weight: 700;
      color: #30DFAB;
    }
    
    .logo h1 span {
      color: var(--text-color);
      font-weight: 300;
    }
    
    .logo p {
      color: var(--dark-gray);
      font-size: 0.9rem;
      margin-top: 0.5rem;
    }
    
    .divider {
      height: 1px;
      background-color: var(--medium-gray);
      margin: 1.5rem 0;
      position: relative;
    }
    
    .divider::after {
      content: "or";
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 0 1rem;
      color: var(--dark-gray);
      font-size: 0.8rem;
    }
    
    .form-group {
      margin-bottom: 1.25rem;
      position: relative;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-color);
    }
    
    .form-control {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid var(--medium-gray);
      border-radius: 8px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
    
    .password-container {
      position: relative;
    }
    
    .toggle-password {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: var(--dark-gray);
    }
    
    .btn {
      width: 100%;
      padding: 0.75rem;
      background-color: #000000;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 0.5rem;
    }
    
    .btn:hover {
      background-color: #30DFAB;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
    }
    
    .btn:active {
      transform: translateY(0);
    }
    
    .links {
      display: flex;
      justify-content: space-between;
      margin-top: 1rem;
      font-size: 0.85rem;
    }
    
    .links a {
      color: var(--primary-color);
      text-decoration: none;
      transition: color 0.2s;
    }
    
    .links a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }
    
    .signup-link {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.9rem;
      color: var(--dark-gray);
    }
    
    .signup-link a {
      color: var(--primary-color);
      font-weight: 500;
      text-decoration: none;
    }
    
    .signup-link a:hover {
      text-decoration: underline;
    }
    
    .g-recaptcha {
      margin: 1rem 0;
      display: flex;
      justify-content: center;
    }
    
    @media (max-width: 480px) {
      .login-container {
        padding: 1.5rem;
      }
    }
    
    /* Animation */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .login-container {
      animation: fadeIn 0.5s ease-out;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="logo">
      <h1><span>Band</span>Mate</h1>
      <p>Log in to your account</p>
    </div>
    <?php $error ?>
    <form action="register.php" method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-container">
          <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
          <i class="toggle-password fas fa-eye" onclick="togglePasswordVisibility()"></i>
        </div>
      </div>
      
      <div class="g-recaptcha" data-sitekey="6LfAQCkrAAAAAPRd5mtO3CXq2rzQfeEfEAjz4QwG"></div>
      
      <button type="submit" name="logIn" class="btn">Log In</button>
      
      <div class="links">
        <a href="#">Forgot password?</a>
        <a href="#">Need help?</a>
      </div>
    </form>
    
    <div class="signup-link">
      Don't have an account? <a href="signup.php">Sign up</a>
    </div>
  </div>

  <script>
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById('password');
      const icon = document.querySelector('.toggle-password');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
  </script>
</body>
</html>