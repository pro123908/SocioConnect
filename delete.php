<?php 

require('functions.php');

if($_GET['id']){
  $postID = $_GET['id'];

  

  if(deletePost($postID)){
    echo 'Post Deleted';
}
}

?>