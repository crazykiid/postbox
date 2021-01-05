<?php
  session_start();

  unset($_SESSION['username']);
  session_unset();
  session_destroy();
  exit(header('Location: login.php'));
?>

