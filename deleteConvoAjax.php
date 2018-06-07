<?php
require_once('functions.php');
    if(!isset($_SESSION['user_id'])){
        redirection('index.php');
    }

    if(isset($_POST['id'])){
        deleteConvo($_POST['id']);
        if($_POST['id'] == $_POST['urlID']){
            redirection("messages.php");
        }
        else{
            showRecentChats();
        }
    }


?>