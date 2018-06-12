<?php
    require_once('functions.php');
    if(!isset($_SESSION['user_id'])){
        redirection("index.php");
    }
    $comment_body = $_POST['comment'];
    $comment_id = $_POST['comment_id'];
queryFunc("UPDATE comments set comment = '{$comment_body}', edited = 1 where comment_id ={$comment_id}");
?>