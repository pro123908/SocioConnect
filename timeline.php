<link rel="stylesheet" href="styles/styles.css">
<?php require_once('header.php');
 
if(isset($_GET['visitingUserID']) && isset($_SESSION['user_id'])){
// If both conditions are satisfied then you have come to this page by searching
    $flag = false;
    if(isset($_POST['add_friend']))
        addFriend($_GET['visitingUserID']);
    else if(isset($_POST['cancel_req']))
        cancelReq($_GET['visitingUserID']);
    else if(isset($_POST['respond_to_request'])) 
        redirection("requests.php");
    else if(isset($_POST['remove_friend']))
        removeFriend($_GET['visitingUserID']);    
}
else if(isset($_SESSION['user_id'])){
    // If this condition is true then you have come to the page by clicking on profile button on your profile - So you ain't searching anybody xD
    $flag = true;
}
else{
    // Not authorized dude,go back to login page xD
    redirection("index.php"); // previously it was set to main.php
}
?>

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

<script src="script.js" >

</script>