<?php

  require_once('functions.php');
  session_start();
  if(!isset($_SESSION['user_id'])){
    redirection('index.php');
  }

  // Calling logout function
  logout();
  
?>