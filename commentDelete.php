<?php 

require('functions.php');


if($_GET['id']){
  $commentID = $_GET['id'];

  if(deleteComment($commentID)){
    echo 'Comment Deleted';
  }
}

?>