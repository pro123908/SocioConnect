<?php

require_once dirname(__FILE__,2) . '/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirection('../../index.php');
}

$userID = $_SESSION['user_id'];

// Redirected here from requests.php
// Request will be accepted or rejected based on user answer

if (isset($_POST['accept'])) {
    $_POST['id'] = clearString($_POST['id']);
    // If request is accepted
    acceptReq($_POST['id']);

    $activity_type = 3;
    $target_id = $_POST['id'] . " " . $_SESSION['user_id'];
    queryFunc("insert into recent_activities (activity_type, activity_at_id, user_id) values ('$activity_type','$target_id','$userID')");
} else if (isset($_POST['ignore'])) {
    // If request is rejected
    ignoreReq($_POST['id']);

}
$dUser = $_POST['id'];
queryFunc("UPDATE notifications set seen=1 where s_user_id='$dUser' AND d_user_id='$userID' AND typeC='request'");
redirection('../../requests.php');
