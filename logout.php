<?php 
session_start();
// session_unset();

// Clear all user-specific session keys
// foreach ($_SESSION as $key => $value) {
//   if (strpos($key, 'userCards_') === 0 || strpos($key, 'userDetails_') === 0) {
//       unset($_SESSION[$key]);
//   }
// }

// session_destroy();

header("location: login.php");
exit();
?>