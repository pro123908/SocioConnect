<?php 

  require_once('header.php');

  if(!isset($_SESSION['user_id'])){
    redirection('index.php');
  }

  // Main file of the website

  // Getting current user name
  $user = $_SESSION['user'];
?>

<!-- User name display section -->
<!-- <div class='firstSection'>
<h1>Welcome <?php echo $user ?></h1>
</div> -->


<!-- Notification Area of the page -->

<div class="content-area">
      <div class="notification-area">
        <div class='notifications'>
          <div class='notification-heading'>Notifications</div>
          <?php showNotifications();?>
        </div>
      </div>


<div class='post-area'>
  <div class='new-post'>
<?php 
// Add post functionality
addPost(true,"");

?>


<div class='posts'>
  
<?php
// Showing posts of friends only
showPosts('a') 
?>
</div>
</div>

<div class="friends-area">
    <div class='friend-heading'>Friends</div>
      <?php displayFriends(); ?>
</div>

</body>
</html>

<script src="script.js" >

</script>
