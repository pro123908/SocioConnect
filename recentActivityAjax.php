<?php
require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}
// activity_type == 0 ==> Like
// activity_type == 1 ==> Comment
// activity_type == 2 ==> Post
// activity_type == 3 ==> Added Friend

$userLoggedIn = $_SESSION['user_id'];
$activity_type = $_POST['activity_type'];

if($activity_type == 2){
    $target_id = queryFunc("select post_id from posts order by post_id desc limit 1");
    $target_id = isRecord($target_id);
    $target_id = $target_id['post_id']; 
}
else{
    $target_id = $_POST['target_id'];
}

queryFunc("insert into recent_activities (activity_type, activity_at_id, user_id) values ('$activity_type','$target_id','$userLoggedIn')");
//To decide the noti incon
// if ($type=='post') {
//     $conflict = 'posted';
//     $notiIcon = 'far fa-user';
// } elseif ($type=='commented') {
//     $conflict = 'commented on your post';
//     $notiIcon = 'far fa-comment-dots';
// } elseif($type == 'request') {
//     $conflict = 'sent you a request';
//     $notiIcon = 'fas fa-user-plus';
//     $notiLink = "requests.php?notiID=$notiID";
// }
// else{ 
//     $conflict = 'liked your post';
//     $notiIcon = 'far fa-thumbs-up';
// }

// $noti = <<<NOTI
//     <a href={$notiLink} class='notification recent_activity'>
//         <span class='notification-info'>
//             <span class='notification-text'>You {$conflict}</span><i class='noti-icon {$notiIcon}'></i><span class='noti-time'>{$time}</span>
//         </span>
//     </a>
// NOTI;
// echo $noti;

?>