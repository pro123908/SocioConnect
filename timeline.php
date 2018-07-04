
<?php require_once('functions.php');
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

require_once('header.php')
?>
<div class='user-timeline'>
    <div class='user-cover-area'>
        <?php $flag ? coverArea($_SESSION['user_id']) : coverArea($_GET['visitingUserID']) ?>
    </div>

    <div class='user-attributes-area'>
    <div class="user-friend-button">
        <!-- If you are comming here through searching or by clicking on your profile button -->
        <?php $flag ? showFriendButton(0) : showFriendButton($_GET['visitingUserID']) ?>
        <?php if (!isset($_GET['visitingUserID']) || isFriend($_GET['visitingUserID']) ||  $_GET['visitingUserID'] == $_SESSION['user_id']) {?>
        <a class='timeline-message-button' href="about.php?id=<?php $visitor = isset($_GET['visitingUserID']) ? $_GET['visitingUserID'] : $_SESSION['user_id']; echo $visitor; ?>">About</a>
        <?php } ?>
        <?php if (isset($_GET['visitingUserID']) && $_GET['visitingUserID']!=$_SESSION['user_id']){?>
        <a class='timeline-message-button' href="messages.php?id=<?php echo $_GET['visitingUserID']; ?>">Message</a>
        <?php } ?>
    </div>
    </div>
    <div class='content-area'>
        <?php
            if($flag || isFriend($_GET['visitingUserID'])){ ?>
            <div class='user-activities-summary-area'>
                <div class='user-activities-summary-heading'>Activites Summary</div>
                <div class='user-activities-summary-content'>
                    <?php $flag ? showUserActivitiesSummary($_SESSION['user_id']) : showUserActivitiesSummary($_GET['visitingUserID']) ?> 
                </div>
            </div>
            <?php } ?>
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
    <?php
        if($flag || isFriend($_GET['visitingUserID'])){ ?>
    <div class='people-you-may-know-area'>
        <div class='people-you-may-know-heading'> People you may know</div>
        <div class='people-you-may-know-content'>
            <?php showPeopleYouMayKnow()?>
        </div>
    </div>
    <?php  } ?>
</div>

<script src="script.js" >

</script>