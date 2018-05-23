<?php 

  require_once('header.php');

  // Main file of the website

  // Getting current user name
  $user = $_SESSION['user'];
?>

<!-- User name display section -->
<div class='firstSection'>
<h1>Welcome <?php echo $user ?></h1>
</div>

<!-- Notification Area of the page -->
<div class='notificationArea'>
  <div class='notifications'>
  <?php showNotifications();?>
  </div>
  
</div>


<?php 
// Add post functionality
addPost(true,"");

?>

<h3 id="postHeading">Posts</h3>
<div id='postArea'>
  
<?php
// Showing posts of friends only
showPosts('a') 
?>
</div>

</div>

</body>
</html>

<script src="script.js" >

</script>
