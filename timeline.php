<?php include "header.php"; ?>

<div class="personal_info">
    <?php show_personal_info(); ?>
</div>

<div class='add_post_timeline'>
<?php  add_post()?>
</div>

<div id='postArea'>
<h3>Your Posts</h3>
<?php show_posts(2); ?>
</div>

<script src="script.js" >

</script>