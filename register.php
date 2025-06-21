<?php 

include 'connectdb.php';

if (isset($_POST['signUp'])) {
  $firstName = $_POST['fName'];
  $lastName = $_POST['lName'];
  $email = $_POST['email'];
  $contactNumber = $_POST['phonenumber'];
  $userName = $_POST['username'];
  $password = $_POST['password'];
  $password = md5($password);

  $checkUsername = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $checkUsername->bind_param("s", $userName);
  $checkUsername->execute();
  $result = $checkUsername->get_result();
  if ($result->num_rows > 0) {
    echo "Username already exists.";
  } else {
    $status = "Active now";
    $random_id = rand(time(), 10000000);

    $insertQuery = $conn->prepare("INSERT INTO users (unique_id, firstname, lastname, email, phonenumber, username, password, profile_picture, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertQuery->bind_param("isssissis", $random_id, $firstName, $lastName, $email, $contactNumber, $userName, $password, $profilePicture, $status);
    $profilePicture = 'images/default.jpg'; //Default profile picture
    if ($insertQuery->execute()) {
      session_start();
      $_SESSION['firstname'] = $firstName;
      $_SESSION['lastname'] = $lastName;
      $_SESSION['unique_id'] = $row['unique_id']; //new code, remove if not useful or throwing an error
      header("location: login.php");
    }
    else {
      echo "Error:".$conn->error;
    }
  }
}

if (isset($_POST['logIn'])) {
  $userName = $_POST['username'];
  $password = $_POST['password'];
  $password = md5($password);

  $sql = "SELECT * FROM users WHERE username = '$userName' and password = '$password'";
  $result = $conn->query($sql);
  if ($result -> num_rows > 0) {
    session_start();
    $row = $result -> fetch_assoc();
    $_SESSION['username'] = $row['username'];
    $_SESSION['unique_id'] = $row['unique_id']; //new code, remove if not useful or throwing an error
    header("location: dashboard.php");
    exit();
  }
  else {
    echo "Not Found, Incorrect Email or Password";
  }
}
?>