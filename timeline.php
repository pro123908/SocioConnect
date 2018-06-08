
<?php require_once('header.php');
   $_SESSION['no_of_posts_changed'] = 0;
    
if(isset($_GET['visitingUserID']) && isset($_SESSION['user_id'])){
// If both conditions are satisfied then you have come to this page by searching
    if($_GET['visitingUserID'] == $_SESSION['user_id'])
        $flag = true;
    else    
        $flag = false;
    if(isset($_POST['add_friend'])){
        addFriend($_GET['visitingUserID']);
    } elseif (isset($_POST['cancel_req'])) {
        cancelReq($_GET['visitingUserID']);
    } elseif (isset($_POST['respond_to_request'])) {
        redirection("requests.php");
    } elseif (isset($_POST['remove_friend'])) {
        removeFriend($_GET['visitingUserID']);
    } 
} elseif (isset($_SESSION['user_id'])) {
    // If this condition is true then you have come to the page by clicking on profile button on your profile - So you ain't searching anybody xD
    $flag = true;
}
else{
    // Not authorized dude,go back to login page xD
    redirection("index.php"); // previously it was set to main.php
}
?>
<div class='user-timeline'>
    <div class='user-cover-area'>
        <?php $flag ? profilePic($_SESSION['user_id']) : profilePic($_GET['visitingUserID']) ?>
    </div>

    <div class='user-attributes-area'>
    <div class="user-friend-button">
        <!-- If you are comming here through searching or by clicking on your profile button -->
        <?php $flag ? showFriendButton(0) : showFriendButton($_GET['visitingUserID']) ?>
        <?php if (isset($_GET['visitingUserID']) && $_GET['visitingUserID']!=$_SESSION['user_id']){?>
        <a class='timeline-message-button' href="messages.php?id=<?php echo $_GET['visitingUserID']; ?>">Message</a>
        <?php } ?>
    </div>
    </div>
    <div class='content-area'>
        <div class='user-info-area'>
        </div>
        <div class='post-area'>
            <div class='new-post'>
            <?php 
            // Add post functionality
            if (isset($_GET['visitingUserID'])) {
                addPost(false, $_GET['visitingUserID']);
            } else {
                addPost(true, "abc");
            }

            ?>
            </div>

            <div class='posts'>
  
            <?php
            $user = $flag ? 'b' : $_GET['visitingUserID'];
            showPosts($user, 1, 10);
            ?>

            </div>
    
            <?php
            if ($flag) {
                $show = true;
            } else {
                $show = isFriend($_GET['visitingUserID']) ? true : false;
            }
            if ($show) {
                $showMoreButton = <<<MSG
                <div id='loading' class='loading-messages'></div>
MSG;
                echo $showMoreButton;
            }
            ?>
        </div>    
    <div class='friends-area'></div>
</div>

<script src="script.js" >

</script>