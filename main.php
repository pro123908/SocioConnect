<?php 

  include "header.php";

  $user = $_SESSION['user'];
?>


<html>


<div class='firstSection'>
<h1>Welcome <?php echo $user ?></h1>


</div>

<?php add_post();?>

<h3 id="postHeading">Posts</h3>
<div id='postArea'>
<?php show_posts(true) ?>
</div>


</html>

<script src="script.js" >

</script>
