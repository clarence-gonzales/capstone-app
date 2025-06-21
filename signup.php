<!DOCTYPE html>
<html lang="en">
<head>
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
    border: 2px solid #ccc;
    border-radius: 5px;
  }

  label {
    color: #cccccc;
  }

  .gender-container {
      display: flex;
      gap: 10px;
  }

  .gender-option {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 120px;
    padding: 8px 12px;
    border: 2px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
  }

  .checkbox-container {
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #000000;
    gap: 8px;
  }

  .checkbox-container input {
    width: 16px;
    height: 16px;
    accent-color: #333;
  }

  .checkbox-container a {
    color: #2979FF;
    text-decoration: none;
  }

  .checkbox-container a:hover {
    text-decoration: underline;
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
  .login-link {
    text-align: center;
    margin-top: 10px;
  }

  .login-link a {
    text-decoration: none;
    color: #2979FF;
  }

  .login-link a:hover {
    text-decoration: underline;
  }

  .modal {
    display: none;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
  }

  .modal-content {
    background: #FFFFFFFF;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    width: 300px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
  }

  .modal-content h3 {
    color: #333;
    margin-bottom: 10px;
  }

  .modal button {
    background: #30DFAB;
    color: #FFFFFFFF;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    font-weight: bold;
    transition: 0.3s;
  }

  .modal button:hover {
    background: #26C499;
  }

  .field {
    position: relative;
  }

  .field i {
    position: absolute;
    right: 25px;
    top: 50%;
    transform: translateY(-50%);
    color: #ccc;
    cursor: pointer;
  }

  .eye {
    color: #ccc;
    position: absolute;
    right: 15px;
  }
</style>
</head>
<body>


  <div class="container">
    <h2 class="heading"><span style="color: gray;">Welcome to </span>BandMate</h2>
      <p style="text-align: center;">Sign up and register to unlock the best experience!</p>
      <hr>

      <form id="signupForm" class="form" action="register.php" method="POST">
        <input type="text" name="fName" placeholder="First Name" required>
        <input type="text" name="lName" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phonenumber" placeholder="Contact Number" required>
          
        <label>Gender:</label>
          <div class="gender-container">
            <label class="gender-option">
              Female
              <input type="radio" name="gender" value="Female" required>
            </label>
            <label class="gender-option">
              Male
              <input type="radio" name="gender" value="Male" required>
            </label>
          </div>

          <div class="field input">
          <input type="text" name="username" placeholder="Username" required>
          <div class="field">
          <input type="password" name="password" placeholder="Password" required>
          <i class="fa-solid fa-eye eye"></i>
          </div>
          <div class="field">
          <input type="password" name="confirm_password" placeholder="Confirm Password" required>
          <i class="fa-solid fa-eye eye"></i>
          </div>
          </div>

          <label class="checkbox-container">
            <input type="checkbox" name="terms" required>
            <span class="checkbox">I Agree to all <a href="#">Terms & Conditions</a></span>
          </label>

          <button type="submit" name="signUp" class="btn">Sign Up</button>

          <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
      </form>
  </div>

  <div id="successModal" class="modal">
    <div class="modal-content">
      <h3>Succesfully Registered!</h3>
      <p>Welcome to BandMate. You can now log in.</p>
      <button onclick="redirectToLogin()">OK</button>
    </div>
  </div>

  <script>

  const signupForm = document.getElementById("signupForm");
  const passwordField = signupForm.querySelector("input[name='password']");
  const confirmPasswordField = signupForm.querySelector("input[name='confirm_password']");

  signupForm.addEventListener("submit", function (event) {
    if (passwordField.value !== confirmPasswordField.value) {
      event.preventDefault(); 
      alert("Passwords do not match. Please confirm your password.");
    }
  });

  const togglePasswordVisibility = (toggleIcon, passwordInput) => {
    toggleIcon.addEventListener("click", () => {
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
      } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
      }
    });
  };

  const passwordToggleIcon = signupForm.querySelector("input[name='password'] ~ .eye");
  const confirmPasswordToggleIcon = signupForm.querySelector("input[name='confirm_password'] ~ .eye");

  togglePasswordVisibility(passwordToggleIcon, passwordField);
  togglePasswordVisibility(confirmPasswordToggleIcon, confirmPasswordField);
</script>

</body>
</html>
