<?php

  require_once('functions.php');

  if(!isset($_SESSION['user_id'])){
    redirection('index.php');
  }

  // Calling logout function
  logout();
  
?>