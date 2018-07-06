<?php 

require_once('functions.php');

/* Dead */

if(!isset($_SESSION['user_id'])){
  redirection('index.php');
}


// When user deletes the comment
  if($_GET['id']){
    $commentID = $_GET['id']; // ID of the deleted comment

    // Function called to delete the comment with given ID
    if(deleteComment($commentID)){
      echo 'Comment Deleted';
    }
  }

?>