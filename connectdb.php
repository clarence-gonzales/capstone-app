<?php

$host = "127.0.0.1:3307";
$user = "root";
$pass = "";
$db = "capstone";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Failed to connect to DB: " .$conn->connect_error);
}
?>