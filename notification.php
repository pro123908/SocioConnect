<?php

require_once dirname(__FILE__) . '/includes/header.php';

if (!isset($_SESSION['user_id'])) {
    redirection('index.php');
}

// Rendering the post that was clicked through notification

if (isset($_GET['postID'])) {
    $_SESSION['notiPostID'] = $_GET['postID']; // Post ID
    $_SESSION['notiType'] = $_GET['type']; // Type of the notification
    $notiID = $_GET['notiID']; // Notification ID

    // Now notification has been seen, so set flag to 1
    $queryResult = queryFunc("UPDATE notifications SET seen=1  WHERE noti_id='$notiID'");

    ?>
    <div class='content-area'>
    <div class='notification-area' style='border:none'></div>
    <div class='post-area'>

    <?php
// Displaying that post only
    showPosts('c', 1, 1);?>
    </div>
    <div class='friends-area' style='border:none'></div>
    </div>
    <?php

}

?>

<script src="./includes/script.js" >

</script>