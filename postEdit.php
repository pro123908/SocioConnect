<?php
    require_once('functions.php');
    if(!isset($_SESSION['user_id'])){
        redirection("index.php");
    }
    $post_body = $_POST['postContent'];
    $post_id = $_POST['postID'];
queryFunc("UPDATE posts set post = '{$post_body}', edited = 1 where post_id ={$post_id}");
?>