<?php 

/* DEAD */


/* 
 -> like or unlike the post
 -> generates notification for like
 -> returns likes count for the post
*/

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
  redirection('index.php');
}

// We need to add a new column to likes table, that would store the user_id of the person whose post in being liked.

// For adding like to the current post
if(isset($_GET['like'])){
  $postID = mysqli_real_escape_string($connection,$_GET['like']); // Post ID
  $userID = $_SESSION['user_id']; // user who liked the post

  //Checking if the post is already been liked.
  $checkLikeResult = queryFunc("SELECT * from likes where post_id ='$postID' AND user_id ='$userID'");
  

  //if it is already been liked,then unlike it
  if(isData($checkLikeResult)){
    $unlikeResult = queryFunc("DELETE from likes where post_id='$postID' AND user_id ='$userID'");
  }
  else{
  //else like it
  $likeResult = queryFunc("INSERT INTO likes (post_id,user_id,createdAt) VALUES('$postID','$userID',now())");

  // Getting the user_id of the user whose post is liked
  $whosePostQuery = queryFunc("SELECT user_id from posts where post_id='$postID'");
  $whosePost = isRecord($whosePostQuery);

  // Creating notification
  notification($userID,$whosePost['user_id'],$postID,'liked');
  }

  //Getting total number of likes for a post
  $likesResult = queryFunc("SELECT count(*) as count from likes where post_id='$postID'");
  $likes = isRecord($likesResult);

  // Sending likes count as a response to AJAX call
  echo $likes['count'];

}
?>


