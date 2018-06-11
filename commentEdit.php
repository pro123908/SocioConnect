<?php
    require_once('function.php');
    if(!isset($_SESSION['user_id'])){
        redirection("index.php");
    }
    $comment_body = $_POST['comment'];
    $comment_id = $_POST['comment_id'];
    queryFunc("update messages set comment = '$comment_body' and edited = 1 where comment_id ='$comment_id' ");
?>