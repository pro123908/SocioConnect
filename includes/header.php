<?php require_once dirname(__FILE__) . '/functions.php';

ob_start(); // Turn on ouput buffering

?>

<!-- Header Section of the website. Will be included in every page  -->

<html>
  <head>
    <link rel="stylesheet" href="./icons/css/all.css" >
    <link href='http://fonts.googleapis.com/css?family=Berkshire+Swash' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Merienda+One' rel='stylesheet' type='text/css'>

    <link rel="stylesheet" href="./styles/styles.css">
  </head>
  <body onload="setUserId('<?php if (isset($_SESSION['user_id'])) {
    echo $_SESSION['user_id'];
}
?>')">

  <!-- Starting div of main content area of the website, where all the stuff lies -->
  <?php if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user']; // loggedIn username ?>
  <div class="main-container">

    <!-- Start of header section -->


    <div class="header-container">
      <div class="header">
        <div class="header-heading">
          <h1><a href="main.php" class="heading_link">Socio Connect</a></h1>
        </div>

    <?php
// Displaying this navbar if user is logged in

    // Getting user pic
    $pic = getUserProfilePic($_SESSION['user_id']);
    $image = "<img src='{$pic}' class='post-avatar post-avatar-40' />";
    ?>

        <!-- Search Bar to search Users -->
        <div class="header-search-bar">
          <input type="text" class="search-input" placeholder="Search" onkeyup="getUsers(this.value,1)" name="q" autocomplete = "off" >

          <!-- Results of search will be displayed in this div -->
          <div class='search-result'></div>
        </div>

        <!-- Header Buttons -->
        <div class="header-links">

          <!--************************* Notification Dropdown ****************************-->
          <div class='notification-dropdown'>
            <a href="javascript:toggleDropdown('.noti-dropdown');" class="header-btn mr-1 "><i class="noti-click fas fa-bell fa-lg"></i></a>

            <!-- Dropdown Content -->
            <div class='noti-dropdown'>
              <?php showNotifications(2, 1, 20)?>
            </div>

            <div class='noti-count'>
              <?php $value = CountDropdown(1);
    countDropdownDisplay($value, 'noti');
    ?>
            </div>
          </div>
          <!-- ********************************************************** -->

          <!-- ********** Message Dropdown ************** -->
          <div class='message-dropdown'>
            <a href="javascript:toggleDropdown('.msg-dropdown');" class="header-btn mr-1"><i class="msg-click fas fa-envelope fa-lg"></i></a>



          <div class='msg-dropdown'>
              <h3>Messages</h3>
            <div class='recent-chats-dropdown'>
              <?php showRecentChats(1);?>
            </div>
          </div>

            <div class='msg-count'>
              <?php $value = CountDropdown(2);
    countDropdownDisplay($value, 'msg');
    ?>
            </div>
          </div>
          <!-- ********************************************************** -->

          <!-- ********** Request Dropdown ************** -->
          <div class='request-dropdown'>
            <a href="javascript:toggleDropdown('.req-dropdown');" class="header-btn mr-1"><i class="req-click fas fa-user-plus fa-lg"></i></a>

            <div class='req-dropdown'>
              <?php showNotifications(1, 0, 10);?>


            </div>
            <div class='req-count'>
              <?php
$value = CountDropdown(3);
    countDropdownDisplay($value, 'req');
    ?>
            </div>

          </div>
          <!-- ********************************************************** -->

          <!-- log out button -->
          <a href="./includes/EventHandlers/logout.php" class="header-btn mr-1"><i class="fas fa-sign-out-alt fa-lg"></i></a>

          <!--  Login User Name -->
          <a class='logged-user' href='timeline.php'>
            <?php echo $image ?>
            <span><?php echo $user ?></span>
          </a>
        </div>
        <!-- ********************* Header Links **********************  -->
      </div>
    <!-- ***************************** Header *************************** -->
    </div>
  <?php }?>

