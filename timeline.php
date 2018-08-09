<?php
require_once dirname(__FILE__) . '/includes/functions.php';
require_once './includes/header.php';

$_SESSION['no_of_posts_changed'] = 0;

// flag 1 => User's own timeline
// flag 2 => User's friend's timeline
// flag 0 => for All other users

if (isset($_GET['visitingUserID']) && isset($_SESSION['user_id'])) {
    $_GET['visitingUserID'] = clearString($_GET['visitingUserID']);
// If both conditions are satisfied then you have come to this page by searching
    if ($_GET['visitingUserID'] == $_SESSION['user_id']) {
        $flag = 1;
    } else {
        if (isFriend($_GET['visitingUserID'])) {
            $flag = 2;
        } else {
            $flag = 0;
        }

    }

    if (isset($_POST['add_friend'])) {
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
    $flag = 1;
} else {
    // Not authorized dude,go back to login page xD
    redirection("index.php"); // previously it was set to main.php
}
?>

<div class='user-timeline'>
    <div class='user-cover-area'>
        <?php $flag == 1 ? coverArea($_SESSION['user_id']) : coverArea($_GET['visitingUserID'])?>
    </div>

<?php
if (isset($_GET['visitingUserID']) && $_GET['visitingUserID'] != $_SESSION['user_id']) {?>

<div class='user-attributes-area'>
        <div class="user-friend-button">
            <!-- Don't show the friend button if user visiting his own timeline  -->
            <?php $flag == 1 ? showFriendButton(0) : showFriendButton($_GET['visitingUserID']);?>
        </div>

        <div class="user-message-button">
            <!-- if its not user's own timeline, show message button -->
            <?php if (isset($_GET['visitingUserID']) && $_GET['visitingUserID'] != $_SESSION['user_id']) {?>
                <a class='timeline-message-button' href="messages.php?id=<?php echo $_GET['visitingUserID']; ?>">Message</a>
            <?php }?>
        </div>
    </div>

  <?php }
?>



    <div class='content-area'>
        <?php
if ($flag == 1 || $flag == 2) {?>
                <div class="content-left-side">

                    <div class='user-info-area'>
                        <div class='user-info-heading'>User Details</div>
                        <div class='user-info-content'>
                            <?php $flag == 1 ? showUserInfo($_SESSION['user_id']) : showUserInfo($_GET['visitingUserID']);?>
                        </div>
                    </div>

                    <div class='recenet-uploads-area'>
                        <div class='recenet-uploads-heading'>Recent Uploads</div>
                        <div class='recenet-uploads-content'>
                            <?php $flag == 1 ? getUploadedPics($_SESSION['user_id']) : getUploadedPics($_GET['visitingUserID']);?>
                        </div>
                        <div class='recent-uploads-footer'><?php if(isset($_SESSION['recent_uploads'])) echo "<p class='see-more'>No Recent Uploads</p>"; unset($_SESSION['recent_uploads'])?></div>
                    </div>

                    <?php if ($_SESSION['user_id'] == 1) {?>
                         <div class='remove-user-area'>
                            <div class='remove-user-heading'>Remove User</div>
                            <div class='remove-user-content'>
                                <input type="number" class= "remove-user-input" autocomplete = "off" placeholder="Enter User Id to remove account"><br><br>
                                <input type="button" class ="remove-user-submit" value= "Remove Account" onclick="deleteUser();">
                            </div>
                        </div>

                        <div class='latest-users-area'>
                            <div class='latest-users-heading'>Latest Registered Users</div>
                            <div class='latest-users-content'>
                                <?php showLatestRegisteredUsers()?>
                            </div>
                        </div>
                    <?php }?>
                </div>
                <!-- Right Side content Finished -->

                <div class='post-area'>
                    <?php
                        // Add post functionality only if the user is visiting his own timeline
                        if ($flag == 1) {
                            ?>
                            <div class='new-post'>
                                <?php addPost(); ?>
                            </div>
                        <?php
                        }
                    ?>

                    <div class='posts'>
                        <?php
                            $user = $flag == 1 ? 'b' : $_GET['visitingUserID'];
                            showPosts($user, 1, 10);
                        ?>
                    </div>

                    <div id='loading' class='loading-messages'></div>
                </div>
                <!-- Posts Div ended -->

                <div class='content-right-side'>
                    <?php if ($flag == 2) {?>
                        <div class='mutual-friends-area'>
                            <div class='mutual-friends-heading'>Mutual Friends</div>
                            <div class='mutual-friends-content'>
                                <?php showMutualFriends($_GET['visitingUserID'])?>
                            </div>
                        </div>     
                    <?php }?>

                    <div class='user-activities-summary-area'>
                        <div class='user-activities-summary-heading'>Activites Summary</div>
                        <div class='user-activities-summary-content'>
                            <?php $flag == 1 ? showUserActivitiesSummary($_SESSION['user_id']) : showUserActivitiesSummary($_GET['visitingUserID'])?>
                        </div>
                    </div>

                    <div class='people-you-may-know-area'>
                        <div class='people-you-may-know-heading'> People you may know</div>
                        <div class='people-you-may-know-content'>
                            <?php showPeopleYouMayKnow()?>
                        </div>
                    </div>

                    <?php if ($_SESSION['user_id'] == 1) {?>
                        <div class='user-activities-summary-area-for-admin'>
                            <div class='user-activities-summary-heading-for-admin'>Detailed Activites Summary Of Users</div>
                            <div class='user-activities-summary-input-area-for-admin'>
                                <input type="number" class= "search-user-details-input" autocomplete = "off" placeholder="Enter User Id..."><br><br>
                                <input type="button" class ="search-user-details-submit" value= "Get Details" onclick="getUserDetails();">
                            </div>
                            <div class='user-activities-summary-content-for-admin'>

                            </div>
                        </div>     
                    <?php }?>
                        
                </div>
                <!-- Right Side content Finished -->
        <?php
}else{?>
        <div class='user-isnt-friend'>
            <div class='mutual-friends-heading'>Mutual Friends</div>
            <div class='mutual-friends-content'>
                <?php showMutualFriends(clearString($_GET['visitingUserID']))?>
            </div>
        </div>         
    <?php       
}
?>
    </div>
    <!-- Content area ended -->
</div>
<!-- timeline Ended -->

<?php
require_once dirname(__FILE__) . '/includes/footer.php';
?>

<script src="./includes/script.js" ></script>