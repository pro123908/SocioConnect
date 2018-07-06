<?php 

/* ------------------------- No need -------------------- */

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
  redirection('index.php');
}

  // Just giving likesCount to the Ajax call as a response
if (isset($_GET['likeCount'])) {
    echo $_GET['likeCount'];
}
else{
  echo "Not set";
}
?>