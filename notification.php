<?php
  require_once('header.php');
  require_once('functions.php');

  // Rendering the post that was clicked through notification

  if(isset($_GET['postID'])){
    $_SESSION['notiPostID'] = $_GET['postID']; // Post ID
    $_SESSION['notiType'] = $_GET['type']; // Type of the notification
    $notiID = $_GET['notiID']; // Notification ID

    // Now notification has been seen, so set flag to 1
    $queryResult = queryFunc("UPDATE notifications SET seen=1  WHERE noti_id='$notiID'");

    ?>
    <div id='postArea'>
    <h3>Post</h3>
    <?php
    // Displaying that post only
    showPosts('c'); ?>
    </div>
    <?php
    
  }

?>

<script src="script.js" >

</script>