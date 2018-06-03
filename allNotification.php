

<?php 

require_once('header.php');
if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}


$userID = $_SESSION['user_id'];

$queryResult = queryFunc("SELECT * from notifications WHERE d_user_id='$userID' order by noti_id desc");
$noti = '<div class="notification-display"><h1>Notifications</h1>';
if (isData($queryResult)) {
    while ($row = isRecord($queryResult)) {
        $sUser = $row['s_user_id'];
        $postID = $row['post_id'];
        $type = $row['typeC'];
        $notiID = $row['noti_id'];
        $diffTime = differenceInTime($row['createdAt']);
        $time = timeString($diffTime);
        $colorNoti = '';

        if ($row['seen'] == 0) {
            $colorNoti = 'noSeen';
        }


        // Selecting name of the user who generated the notification
        $personQuery = queryFunc("SELECT profile_pic,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$sUser'");
        $sPerson = isRecord($personQuery);

        if ($type=='post') {
            $conflict = 'posted';
            $notiIcon = 'far fa-user';
        } elseif($type=='commented') {
            $conflict = 'on your post';
            $notiIcon = 'far fa-comment-dots';
        }else{
            $conflict = 'your post';
            $notiIcon = 'far fa-thumbs-up';
        }

        $noti .= <<<NOTI
            <div class='notification  {$colorNoti}'>
            <img src='{$sPerson['profile_pic']}' class='post-avatar' />
            <div class='notification-info'>
    <a class='notification-text' href='notification.php?postID={$postID}&type={$type}&notiID={$notiID}'>{$sPerson['name']} has {$type} {$conflict}</a><i class='noti-icon {$notiIcon}'></i><span class='noti-time'>{$time}</span></div></div>
NOTI;
    }

    $noti .= '</div>';
    echo $noti;
} else {
    echo '<h1>No Notifications</h1>';
}