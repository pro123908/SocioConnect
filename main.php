<?php 

  include "header.php";

  $user = $_SESSION['user'];
?>


<html>


<div class='firstSection'>
<h1>Welcome <?php echo $user ?></h1>
</div>

<div class='notificationArea'>
  <div class='notifications'>
  <?php showNotifications();?>
  </div>
  
</div>

<?php addPost(true);?>

<h3 id="postHeading">Posts</h3>
<div id='postArea'>
  
<?php showPosts('a') ?>
</div>


</html>

<script src="script.js" >

</script>
