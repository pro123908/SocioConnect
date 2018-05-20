<?php include "header.php"; ?>

<div class="personal_info">
    <?php personalInfo(); ?>
</div>

<div class='add_post_timeline'>
<?php  addPost()?>
</div>

<div id='postArea'>
<h3>Your Posts</h3>
<?php showPosts(2); ?>
</div>

<script src="script.js" >

</script>