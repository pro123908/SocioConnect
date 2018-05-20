<?php 

require_once('functions.php');

if(isset($_POST['post'])){
    new_post($_POST['post']);
  }

?>