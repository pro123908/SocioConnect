<?php
    require_once('functions.php');

    /* 
        Edit the comment in database 
    */

    /* DEAD */

    if(!isset($_SESSION['user_id'])){
        redirection("index.php");
    }
    global $connection;
    $comment_body = mysqli_real_escape_string($connection, $_POST['comment']);
    $comment_id = $_POST['comment_id'];
    queryFunc("UPDATE comments set comment = '{$comment_body}', edited = 1 where comment_id ={$comment_id}");
?>