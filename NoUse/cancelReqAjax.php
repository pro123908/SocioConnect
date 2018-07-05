<?php

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}

if(!isset($_GET['id'])){
    redirection('main.php');
}

queryFunc("DELETE FROM friend_requests WHERE to_id ={$_POST['id']} AND from_id ={$_SESSION['user_id']}");

queryFunc("DELETE from notifications where s_user_id={$_SESSION['user_id']} AND d_user_id={$_POST['id']} AND typeC='request'");
?>