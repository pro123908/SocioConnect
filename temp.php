<div class="personal_info">
<!-- If you are comming here through searching or by clicking on your profile button -->
    <?php $flag ? personalInfo(true,"") : personalInfo(false,$_GET['visitingUserID']) ?>
</div>

<div class="friend_button">
<!-- If you are comming here through searching or by clicking on your profile button -->
    <?php $flag ? showFriendButton(0) : showFriendButton($_GET['visitingUserID']) ?>
    <a href="messages.php?id=<?php echo $_GET['visitingUserID']; ?>">Message</a>
</div>

<div class='add_post_timeline'>
<!-- If you are comming here through searching or by clicking on your profile button -->
<?php  $flag ? addPost(true,"") : addPost(false,$_GET['visitingUserID']) ?>
</div>

<div id='postArea'>
<h3>Posts</h3>
<!-- If you are comming here through searching or by clicking on your profile button -->
<?php $flag ? showPosts('b') : showPosts($_GET['visitingUserID']) ?>
</div>