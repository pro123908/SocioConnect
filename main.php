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
  <?php show_notifications();?>
  </div>
  
</div>

<?php add_post();?>

<h3 id="postHeading">Posts</h3>
<div id='postArea'>
  
<?php show_posts(1) ?>
</div>


</html>

<script src="script.js" >

</script>
