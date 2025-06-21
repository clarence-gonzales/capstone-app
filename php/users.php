<?php 
  session_start();
  include_once "connectdb.php";
  $sql = mysqli_query($conn, "SELECT * FROM users");
  $output = "";
  if(mysqli_num_rows($sql) == 0) {
    $output .= "No user are available to chat";
  }elseif(mysqli_num_rows($sql) > 0) {
   include_once "php/data.php";
  }
?>