<?php
  session_start();
  include "connectdb.php";

  if(isset($_SESSION['user_details'])) {
    $outgoing_id = trim(mysqli_real_escape_string($conn, $_POST['outgoing_id']));
    $incoming_id = trim(mysqli_real_escape_string($conn, $_POST['incoming_id']));
    $message = trim(mysqli_real_escape_string($conn, $_POST['message']));

    if(!empty($message)) {
      $sql = mysqli_query($conn, "INSERT INTO messagess (incoming_msg_id, outgoing_msg_id, msg)
                        VALUES ('{$incoming_id}', '{$outgoing_id}', '{$message}')") or die();
                        
    }
  }else {
    header("Location:../login.php");
  }

?>