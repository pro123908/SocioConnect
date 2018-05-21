<?php
  require_once('header.php');
  require_once('functions.php');

  if(isset($_GET['postID'])){
    $_SESSION['notiPostID'] = $_GET['postID'];
    $_SESSION['notiType'] = $_GET['type'];
    $notiID = $_GET['notiID'];

    $queryResult = queryFunc("UPDATE notifications SET seen=1  WHERE noti_id='$notiID'");


    
    ?>
    <div id='postArea'>
    <h3>Post</h3>
    <?php showPosts(3); ?>
    </div>
    <?php
    
  }

?>

<script src="script.js" >

</script>