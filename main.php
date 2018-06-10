<?php 

  include('header.php');

  if(!isset($_SESSION['user_id'])){
    redirection('index.php');
  }

  // Main file of the website
  $_SESSION['no_of_posts_changed'] = 0;
  $_SESSION['last_msg_id'] = 0;
  $_SESSION['last_message_retrieved_for_recent_convos'] = 0;
  // Getting current user name
?>

<!-- Notification Area of the page -->

<div class="content-area">
      <!-- <div class="notification-area">
        <div class='notifications'>
          <div class='notification-heading'>Notifications</div>
          <?php //showNotifications(10);?>
          <a href='allNotification.php' class='see-more'>
        <span>See more</span>
      </a>
        </div>
      </div> -->


      <div class="notification-area">
        <div class='notifications' id="recent_activities">
          <div class='notification-heading'>Recent Activities</div>
          <div class='activities-content'>
          <?php showRecentActivities(1,10,10);
          ?>
          </div>
          <div class="show-more-activities">
            <?php 
              if($_SESSION['more_activities'] == 1) 
                echo "<a href='allActivities.php' class='see-more'><span>See more</span></a>";
              else if($_SESSION['more_activities'] == 0) 
                echo "<p class='see-more'>No Activities to Show</p>";
              else if($_SESSION['more_activities'] == 2)   
                echo "<p class='see-more'>No More Activities to Show</p>";
              unset($_SESSION['more_activities']);   
            ?>
          </div>
        </div>
      </div>

<div class='post-area'>
  <div class='new-post'>
<?php 
// Add post functionality
addPost(true,"");

?>
</div>

<div class='posts'>
  
<?php
  showPosts('a',1,10);
?>
</div>
<div id='loading' class='loading-messages'></div>
</div>

<div class="friends-area">
    <div class='friend-heading'>Friends</div>
    <div class='friends-container'>
    <?php displayFriends(10); ?>
    </div>
    <div class="show-more-friends">
    <?php 
      if($_SESSION['more_friends'] == 1) 
        echo "<a href='requests.php' class='see-more'><span>See more</span></a>";
      else if($_SESSION['more_friends'] == 0) 
        echo "<p class='see-more'>No Friends to Show</p>";
      else if($_SESSION['more_friends'] == 2)   
        echo "<p class='see-more'>No More Friends to Show</p>";
      unset($_SESSION['more_friends']);   
    ?>
    </div>  
</div>

</body>
</html>
<script src="script.js" ></script>

<script>
// window.addEventListener('scroll',function(){
//   showNextPageCaller('a')
// });
</script>





