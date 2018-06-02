<?php 

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
  redirection('index.php');
}

// Addding new post

if(isset($_POST['post'])){
  // Calling function to add post
  newPost($_POST['post']);
}

?>