<?php 

require_once('functions.php');

/* DEAD */

// Deletes POST

if(!isset($_SESSION['user_id'])){
  redirection('index.php');
}


// For deleting post
if($_GET['id']){
  $postID = $_GET['id']; // ID of the post to be deleted

  // Function to call for the deletion of post with post ID
  if(deletePost($postID)){
    echo 'Post Deleted';
  }
}


?>