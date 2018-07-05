<?php

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}

if(!isset($_GET['id'])){
    redirection('main.php');
}

$friend = queryFunc("INSERT INTO friend_requests (to_id, from_id) values({$_POST['id']},{$_SESSION['user_id']})");

notification($_SESSION['user_id'], $_POST['id'], 0, 'request');
?>