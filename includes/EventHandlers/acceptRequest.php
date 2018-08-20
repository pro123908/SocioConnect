<?php

require_once dirname(__FILE__,2) . '/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirection('../../index.php');
}

$userID = $_SESSION['user_id'];

// Redirected here from requests.php
// Request will be accepted or rejected based on user answer

if (isset($_GET['action'])) {
    $value = $_GET['action'];

    if($value == 'accept'){
        $_GET['id'] = clearString($_GET['id']);

   
        // If request is accepted
        acceptReq($_GET['id']);
        
    
        $activity_type = 3;
        $target_id = $_GET['id'] . " " . $_SESSION['user_id'];
        queryFunc("insert into recent_activities (activity_type, activity_at_id, user_id) values ('$activity_type','$target_id','$userID')");
    }else{
        // If request is rejected
    ignoreReq($_GET['id']);

    }
    $dUser = $_GET['id'];
queryFunc("UPDATE notifications set seen=1 where s_user_id='$dUser' AND d_user_id='$userID' AND typeC='request'");
 $content = friendRequest();
 echo $content;
}

