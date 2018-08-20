<?php
// Main file of the website

// Including header
require_once dirname(__FILE__) . '/includes/header.php';

// If user is not logged in then redirect user to login page
if (!isset($_SESSION['user_id'])) {
    redirection('index.php');
}

// Declaring session variables
$_SESSION['no_of_posts_changed'] = 0;
// $_SESSION['last_msg_id'] = 0;
// $_SESSION['last_message_retrieved_for_recent_convos'] = 0;

?>
<div class='side-bar-container'>
<div class='side-bar'>
    <?php sideBar(1);?>
</div>
</div>


  <!-- Content Area - Where all the content of the page lies -->
<div class="content-area row">

    <!-- ******************** Recent activities ***************** -->
    <div class="recent-activities-area col-lg-3 col-xl-3">
        <div class='recent-activities' id="recent_activities">
            <div class='recent-activities-heading'>Recent Activities</div>
            <div class='activities-content'>
                <?php showRecentActivities(1, 10, 1);?>
            </div>
            <div class="show-more-activities">
                <?php
// Displaying relevant message according to the criteria
if ($_SESSION['more_activities'] == 1) {
    echo "<a href='allActivities.php' class='see-more'><span>See more</span></a>";
} else if ($_SESSION['more_activities'] == 0) {
    echo "<p class='see-more'>No Activities to Show</p>";
} else if ($_SESSION['more_activities'] == 2) {
    echo "<p class='see-more'>No More Activities to Show</p>";
}
?>
            </div>
        </div>
    </div>
  <!-- *********************** Recent Activities Ended ****************** -->

    <div class='post-area col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6'>
        <div class='new-post'>
            <!-- Add post functionality -->
            <?php addPost();?>
        </div>

        <div class='posts'>
            <?php showPosts('a', 1, 10);?>
        </div>

        <div id='loading' class='loading-posts loading-messages'></div>
    </div>

    <div class="friends-area col-lg-3 col-xl-3">
        <div class='friend-heading'>Friends</div>
        <div class='friends-container'>
            <?php displayFriends(10);?>
        </div>

        <div class="show-more-friends">
            <?php
if ($_SESSION['more_friends'] == 1) {
    echo "<a href='requests.php' class='see-more'><span>See more</span></a>";
} else if ($_SESSION['more_friends'] == 0) {
    echo "<p class='see-more'>No Friends to Show</p>";
} else if ($_SESSION['more_friends'] == 2) {
    echo "<p class='see-more'>No More Friends to Show</p>";
}
unset($_SESSION['more_friends']);
?>
        </div>
    </div>
</div>

</body>
</html>

<?php

require_once dirname(__FILE__) . '/includes/footer.php';
?>

<script src="./includes/script.js" ></script>