<?php 

require('header.php');


if(isset($_GET['like'])){
  $postID = mysqli_real_escape_string($connection,$_GET['like']);
  $userID = $_SESSION['user_id'];

  $checkLikeResult = queryFunc("SELECT * from likes where post_id ='$postID' AND user_id ='$userID'");
  

  if(isData($checkLikeResult)){
    $unlikeResult = queryFunc("DELETE from likes where post_id='$postID' AND user_id ='$userID'");
  }
  else{
   
   $likeResult = queryFunc("INSERT INTO likes (post_id,user_id) VALUES('$postID','$userID')");
  }

  $likesResult = queryFunc("SELECT count(*) as count from likes where post_id='$postID'");
  $likes = isRecord($likesResult);

  redirection('likesCount.php?likeCount='.$likes['count']);

  }
  
  

?>