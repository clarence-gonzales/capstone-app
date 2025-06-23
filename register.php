<?php

include 'connectdb.php'; // Ensure this file correctly establishes $conn

// --- User Sign-Up ---
if (isset($_POST['signUp'])) {
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $contactNumber = $_POST['phonenumber'];
    $userName = $_POST['username'];
    $password = $_POST['password'];

    // !!! IMPORTANT SECURITY WARNING !!!
    // DO NOT use MD5 for password hashing in production.
    // MD5 is insecure and easily crackable.
    // ALWAYS use password_hash() for hashing and password_verify() for verification.
    // For example:
    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    //
    // For now, keeping MD5 as per your original code for direct comparison,
    // but this MUST be changed for a secure application.
    $hashedPassword = md5($password);

    // Check if username already exists using prepared statements
    $checkUsername = $conn->prepare("SELECT username FROM users WHERE username = ?");
    if ($checkUsername === false) {
        die("Prepare failed: " . $conn->error); // Check for prepare errors
    }
    $checkUsername->bind_param("s", $userName);
    $checkUsername->execute();
    $result = $checkUsername->get_result();

    if ($result->num_rows > 0) {
        echo "Username already exists. Please choose a different one.";
    } else {
        $status = "Active now";
        $random_id = rand(time(), 10000000); // Generates a unique ID
        $profilePicture = 'images/default.jpg'; // Correctly set the default profile picture path BEFORE binding

        // Insert new user into the database using prepared statements
        $insertQuery = $conn->prepare("INSERT INTO users (unique_id, firstname, lastname, email, phonenumber, username, password, profile_picture, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($insertQuery === false) {
            die("Prepare failed: " . $conn->error); // Check for prepare errors
        }
        // 'isssissis' corresponds to:
        // i: unique_id (integer)
        // s: firstname (string)
        // s: lastname (string)
        // s: email (string)
        // i: phonenumber (integer or string depending on column type, assuming string for flexibility)
        // s: username (string)
        // s: password (string)
        // s: profile_picture (string)
        // s: status (string)
        $insertQuery->bind_param("isssissss", $random_id, $firstName, $lastName, $email, $contactNumber, $userName, $hashedPassword, $profilePicture, $status);

        if ($insertQuery->execute()) {
            session_start();
            // Set session variables upon successful registration
            $_SESSION['unique_id'] = $random_id; // Use the newly generated unique_id
            $_SESSION['firstname'] = $firstName;
            $_SESSION['lastname'] = $lastName;
            // You might also want to set $_SESSION['username'] here if needed immediately
            $_SESSION['username'] = $userName;

            header("location: login.php");
            exit(); // Always exit after a header redirect
        } else {
            echo "Error during registration: " . $conn->error;
        }
        $insertQuery->close(); // Close the statement
    }
    $checkUsername->close(); // Close the statement
}

// --- User Log-In ---
if (isset($_POST['logIn'])) {
    $userName = $_POST['username'];
    $password = $_POST['password'];

    // Hash the entered password using the same method as during registration for comparison
    // IMPORTANT: In a real app, use password_verify($password, $row['password']) with password_hash().
    $hashedPassword = md5($password);

    // Retrieve user by username using prepared statements to prevent SQL injection
    $sql = $conn->prepare("SELECT unique_id, username, password FROM users WHERE username = ?");
    if ($sql === false) {
        die("Prepare failed: " . $conn->error); // Check for prepare errors
    }
    $sql->bind_param("s", $userName);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Compare the hashed password from the database with the hashed entered password
        // In a real app, use: if (password_verify($password, $row['password'])) { ... }
        if ($hashedPassword === $row['password']) {
            session_start();
            $_SESSION['username'] = $row['username'];
            $_SESSION['unique_id'] = $row['unique_id']; // This is correct for login

            header("location: dashboard.php");
            exit(); // Always exit after a header redirect
        } else {
            echo "Incorrect username or password.";
        }
    } else {
        echo "Incorrect username or password."; // User not found
    }
    $sql->close(); // Close the statement
}

?>