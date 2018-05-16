<?php 

  include "header.php";

  $user = $_SESSION['user'];
?>


<html>


<div class='firstSection'>
<h1>Welcome <?php echo $user ?></h1>


</div>

<div id='addPost'>
<h2>Add a post</h2>
<form action="post.php" method='POST'>
  <textarea name="post" id="" cols="50" rows="10" placeholder='Start Writing'></textarea><br><br>
  <input type="file"><br><br>
  <input type="submit" name='submit' value='Post' class='postBtn'>
</form>
</div>





<div id='postArea'>
  <h3>Posts</h3>
<?php show_posts() ?>
</div>


</html>

<script >
  function showCommentField(id){
  document.getElementById("post_id_"+id).classList.toggle('hidden');
}
</script>
