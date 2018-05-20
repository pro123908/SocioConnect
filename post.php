<?php 

include("header.php");

if(isset($_POST['post'])){
    show_new_post($_POST['post']);
  }

?>