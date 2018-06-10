<?php  require_once('functions.php'); ?>
<!-- Dont remove this comment-->

<!-- Header Section of the website. Will be included in every page  -->

<html>
<head>
<!-- <link href="fonts/fontawesome-all.css" rel="stylesheet" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous"> -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous"> 
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet">
 <link rel="stylesheet" href="main.css">
</head>
<body onload="setUserId('<?php echo $_SESSION['user_id'];?>')">
<!-- Starting div of main content area of the website, where all the stuff lies -->

<div class="main-container">
    <div class="header">
      <div class="header-heading">
        <h1><a href="main.php" id="heading_link">Socio Connect</a></h1>
      </div>
      
      
      <?php
  // Displaying this navbar if user is logged in
  if (isset($_SESSION['user_id'])) {
      $user = $_SESSION['user'];

      $pic = getUserProfilePic($_SESSION['user_id']);
      $image = "<img src='{$pic}' class='post-avatar post-avatar-40' />"; ?>
      <div class="header-search-bar">
        <input type="text" class="search-input" placeholder="Search" onkeyup="getUsers(this.value,1)" name="q" autocomplete = "off" id="search_text_input">
      <div class='search-result'></div>
      </div>
      
      <div class="header-links">
        <div class='notification-dropdown'>
        <a href="javascript:notificationDropdown();" class="test header-btn mr-1 "><i class="fas fa-bell fa-lg"></i></a>
          
          <div class='noti-dropdown'>
          <h3>Notifications</h3>  
          <?php showNotifications(10)?>
          <a href="allNotification.php" class='see-more'>
            <span>See more</span>
          </a>
        </div>
  </div>
        <div class='message-dropdown'>
        <a href="javascript:messageDropdown()" class="header-btn mr-1"><i class="fas fa-envelope fa-lg"></i></a>

        <div class='msg-dropdown'>
        
          <h3>Messages</h3> 
          <div class='recent-chats-dropdown'>
          <?php showRecentChats(); ?>

  </div>
          <a href="messages.php" class='see-more'>
            <span>See more</span>
          </a>
        </div>
  </div>  
        

    <div class='request-dropdown'>
        <a href="javascript:requestDropdown();" class="header-btn mr-1"><i class="fas fa-user-plus fa-lg"></i></a>

        <div class='req-dropdown'>
          <h3>Friend Requests</h3> 
          
          <?php showNotifications(1); ?>
  
          <a href="requests.php" class='see-more'>
            <span>See more</span>
          </a>
        </div>
  </div>
        <a href="logout.php" class="header-btn mr-1" id="logout_id"><i class="fas fa-sign-out-alt fa-lg"></i></a>
        <a class='logged-user' href='timeline.php'>
          <?php echo $image ?>
          <span><?php echo $user ?></span>
        </a>
      </div>
  </div>
  <?php
  } else {
      ?>
    <!-- to complete the header on sign up and login page -->
    
  <?php
  }
  ?>