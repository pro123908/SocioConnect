<?php 

require('header.php');

// We need to add a new column to likes table, that would store the user_id of the person whose post in being liked.

if(isset($_GET['like'])){
  $postID = mysqli_real_escape_string($connection,$_GET['like']);
  $userID = $_SESSION['user_id'];

  //Checking if the post is already been liked.
  $checkLikeResult = queryFunc("SELECT * from likes where post_id ='$postID' AND user_id ='$userID'");
  

  //if it is already been liked,then unlike it
  if(isData($checkLikeResult)){
    $unlikeResult = queryFunc("DELETE from likes where post_id='$postID' AND user_id ='$userID'");
  }
  else{
   //else like it
   $likeResult = queryFunc("INSERT INTO likes (post_id,user_id) VALUES('$postID','$userID')");
   
   $whosePostQuery = queryFunc("SELECT user_id from posts where post_id='$postID'");
   $whosePost = isRecord($whosePostQuery);
   notification($userID,$whosePost['user_id'],$postID,'liked');
  }

  //Getting total number of likes for a post
  $likesResult = queryFunc("SELECT count(*) as count from likes where post_id='$postID'");
  $likes = isRecord($likesResult);

  
  

  
  // echo 'POST : '.$postID;
  // echo ' S_user : '.$userID;
  // echo ' D_user : '.$data; 
  

  redirection('likesCount.php?likeCount='.$likes['count']);

  }


?>


