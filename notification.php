<?php
  require_once('header.php');

  if(isset($_GET['postID'])){
    $_SESSION['notiPostID'] = $_GET['postID'];
    $_SESSION['notiType'] = $_GET['type'];


    
    ?>
    <div id='postArea'>
    <h3>Post</h3>
    <?php show_posts(3); ?>
    </div>
    <?php
    
  }

?>

<script src="script.js" >

</script>