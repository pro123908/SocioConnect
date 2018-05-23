
<?php 

// Just giving likesCount to the Ajax call as a response
if (isset($_GET['likeCount'])) {
    echo $_GET['likeCount'];
}
else{
  echo "Not set";
}

?>