<?php
require_once('functions.php');

/* 
    Deletes,stores activity from/in database
    add activity to display
*/

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
    // if unlike activity

    // Target Content ID or IDs like post etc
    $target_id = $_POST['target_id'];

    // Deleting the like activity
    queryFunc("DELETE from recent_activities where activity_at_id = '$target_id' AND user_id = '$userLoggedIn' and activity_type = 0");

    //Dislaying Activites
    showRecentActivities(1,10,1);
}
else{
    if($activity_type == 2){
        // If any post is added
        $target_id = queryFunc("SELECT post_id from posts order by post_id desc limit 1");
        // target content 
        $target_id = isRecord($target_id);
        $target_id = $target_id['post_id']; 
    }
    else{
        // if activity was like,comment or friend addtion
        $target_id = $_POST['target_id'];
    }

    // Storing activity in database
    queryFunc("INSERT into recent_activities (activity_type, activity_at_id, user_id) values ('$activity_type','$target_id','$userLoggedIn')");

    // Displaying activity
    addActivity($activity_type,$target_id,$userLoggedIn);
}
?>