<?php 
require_once('functions.php'); 


if(!isset($_SESSION['user_id'])){
    redirection('index.php');
  }

  $userID = $_SESSION['user_id'];

// Redirected here from requests.php
// Request will be accepted or rejected based on user answer

if(isset($_POST['accept'])){
    // If request is accepted
    acceptReq($_POST['id']);   
}
else if(isset($_POST['ignore'])){
    // If request is rejected
    ignoreReq($_POST['id']);
    
}
$dUser = $_POST['id'];
queryFunc("UPDATE notifications set seen=1 where s_user_id='$dUser' AND d_user_id='$userID' AND typeC='request'");
redirection('requests.php');

?>