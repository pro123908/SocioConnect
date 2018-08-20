<?php require_once dirname(__FILE__) . '/functions.php';

ob_start(); // Turn on ouput buffering

$value = $_SERVER['REQUEST_URI'];


$index = strripos($value,'=');

$loc = null;
if($index)
  $loc = substr($value,$index+1);

?>

<!-- Header Section of the website. Will be included in every page  -->

<html>
  <head>
    <title class='pageTitle'></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" href="./icons/css/all.css" > -->
    <link rel="shortcut icon" href="favicon.ico" />
    <!-- Latest compiled and minified CSS -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->

    <link rel="stylesheet" href="./styles/bootstrap.min.css" >

<link rel="stylesheet" href="./styles/bootstrap-grid.min.css" >

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">  
    <link href='http://fonts.googleapis.com/css?family=Berkshire+Swash' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Merienda+One' rel='stylesheet' type='text/css'>


    <link rel="stylesheet" href="./styles/styles.css">
  </head>
  <body onload="setUserId('<?php if (isset($_SESSION['user_id'])) {
    echo $_SESSION['user_id'];
}
?>');">

  <!-- Starting div of main content area of the website, where all the stuff lies -->
  <?php if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user']; // loggedIn username ?>
  <div class="main-container">

    <!-- Start of header section -->


    <div class="header-container">
      <div class="header row no-gutters">
        <div class="header-heading col-md-3 col-lg-3 col-xl-3">
          <h1><a href="main.php" class="heading_link">Socio Connect</a></h1>
        </div>

    <?php
// Displaying this navbar if user is logged in

    // Getting user pic
    $pic = getUserProfilePic($_SESSION['user_id']);
    $image = "<img src='{$pic}' class='post-avatar post-avatar-40' />";
    ?>

        <!-- Search Bar to search Users -->
        <div class="header-search-bar col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
          <input type="text" class="search-input" placeholder="Search" onkeyup="getUsers(this.value,1)" name="q" autocomplete = "off" >

          <!-- Results of search will be displayed in this div -->
          <div class='search-result'></div>
        </div>

        <!-- Header Buttons -->
        <div class="header-links col-12 col-sm-12 col-md-5 col-lg-5 col-xl-5 ">

          <!--************************* Notification Dropdown ****************************-->
          <div class='row col-12 '>
          <div class='notification-dropdown col-3 col-sm-3 col-md-1 col-lg-1 col-xl-1'>
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
          <div class='message-dropdown  col-3 col-sm-3 col-md-1 col-lg-1 col-xl-1'>
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
          <div class='request-dropdown col-3 col-sm-3 col-md-1 col-lg-1 col-xl-1'>
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

        

          <!--  Login User Name -->
          <div class='logged-user-div '>
          <a class='logged-user' href='timeline.php'>
            <?php echo $image ?>
            <span><?php echo $user ?></span>
          </a>
          </div>


          <!-- log out button -->
          <div class='col-3 col-sm-3 col-md-1 col-lg-1 col-xl-1'>
          <a href="./includes/EventHandlers/logout.php" class="header-btn mr-1 "><i class="fas fa-sign-out-alt fa-lg"></i></a>
          </div>
          
        </div>
  </div>
 
        <!-- ********************* Header Links **********************  -->
      </div>
    <!-- ***************************** Header *************************** -->
    <div class='side-bar-res-container'>
<div class='side-bar-res'>
    <?php 
     sideBar(2);
    ?>
</div>
</div>
    </div>

   
    <?php /*echo "<div height='500px' style='margin-top:500px'>$loc</div>"; */}?>


<script>

  var title = document.querySelector('.pageTitle');
  var page = window.location.pathname;

  if (page == '/socioConnect/main.php' || page == '/main.php') {
    title.innerHTML = 'NewsFeed';
  } else if (page == '/socioConnect/timeline.php' || page == '/timeline.php') {
    title.innerHTML = 'Profile';
  } else if (page == '/socioConnect/messages.php' || page == '/messages.php') {
    title.innerHTML = 'Messages';
  } else if (page == '/socioConnect/requests.php' || page == '/requests.php') {
    title.innerHTML = 'Friends';
  } else if (page == '/socioConnect/notification.php' || page == '/notification.php') {
    title.innerHTML = 'Notification';
  } else if (page == '/socioConnect/allNotification.php' || page == '/allNotification.php') {
    title.innerHTML = 'Notifications';
  } else if (page == '/socioConnect/allSearchResults.php' || page == '/allSearchResults.php') {
    title.innerHTML = 'Search';
  } else if (page == '/socioConnect/index.php' || page == '/index.php') {
    title.innerHTML = 'Socio Connect';
  } else if (page == '/socioConnect/allActivities.php' || page == '/allActivities.php') {
    title.innerHTML = 'Activities';
  }


</script>