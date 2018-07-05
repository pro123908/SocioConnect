<?php
    require_once('functions.php');

    if(!isset($_SESSION['user_id'])){
        redirection('index.php');
    }

    if(isset($_GET['notiPage'])){

        $limit = 10;
    showNotifications(3,$_GET['page'],$limit);

    }elseif(isset($_GET['messagePage'])){

        $limitMsg = 10;
    showMessages($_GET['id'], $_GET['page'], $limitMsg);
    }elseif(0){

    }
?>