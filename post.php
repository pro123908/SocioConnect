<?php 

require_once('functions.php');
// Addding new post

if(isset($_POST['post'])){
  // Calling function to add post
  newPost($_POST['post']);
  }

?>