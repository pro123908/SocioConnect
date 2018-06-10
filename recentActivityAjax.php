<?php
require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}
// activity_type == 0 ==> Like
// activity_type == 1 ==> Comment
// activity_type == 2 ==> Post
// activity_type == 3 ==> Added Friend
// activity_type == 4 ==> Unlike

$userLoggedIn = $_SESSION['user_id'];
$activity_type = $_POST['activity_type'];
if($activity_type == 4){
    $target_id = $_POST['target_id'];
    queryFunc("delete from recent_activities where activity_at_id = '$target_id' AND user_id = '$userLoggedIn'");
    showRecentActivities(1,10,10);
}
else{
    if($activity_type == 2){
        $target_id = queryFunc("select post_id from posts order by post_id desc limit 1");
        $target_id = isRecord($target_id);
        $target_id = $target_id['post_id']; 
    }
    else{
        $target_id = $_POST['target_id'];
    }
    queryFunc("insert into recent_activities (activity_type, activity_at_id, user_id) values ('$activity_type','$target_id','$userLoggedIn')");
    addActivity($activity_type,$target_id,$userLoggedIn);
}
?>