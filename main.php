<?php 

  include "header.php";

  $user = $_SESSION['user'];
?>


<html>


<div class='firstSection'>
<h1>Welcome <?php echo $user ?></h1>


</div>

<?php add_post();?>

<div id='postArea'>
  <h3>Posts</h3>
<?php show_posts(true) ?>
</div>


</html>

<script src="script.js" >

</script>
