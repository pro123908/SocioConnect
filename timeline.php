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
                        <div class='recent-uploads-footer'></div>
                    </div>
                </div>
                <!-- Right Side content Finished -->

                <div class='post-area'>
                    <div class='new-post'>
                        <?php
// Add post functionality only if the user is visiting his own timeline
    if ($flag == 1) {
        addPost();
    }

    ?>
                    </div>

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
                </div>
                <!-- Right Side content Finished -->
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