<?php require_once('header.php'); 

if(isset($_SESSION['user_id'])){
  if(isset($_POST['accept'])){
      // If request is accepted
      acceptReq($_POST['id']);
      redirection('requests.php');
  }
  else if(isset($_POST['ignore'])){
      // If request is rejected
      ignoreReq($_POST['id']);
      redirection('requests.php');
  }

}

?>