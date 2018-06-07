<?php  include "functions.php"; ?>
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
      ?>
      <div class="header-search-bar">
        <input type="text" class="search-input" placeholder="Search" onkeyup="getUsers(this.value,1)" name="q" autocomplete = "off" id="search_text_input">
      <div class='search-result'></div>
      </div>
      
      <div class="header-links">
        <a href="allNotification.php" class="header-btn mr-1" id="noti_id" ><i class="fas fa-bell fa-lg"></i> <span  id="noti_desc">Notifications</span></a>
        <a href="messages.php" class="header-btn mr-1" id="msg_id"><i class="fas fa-envelope fa-lg"></i><span  id="msg_desc">Messages</span></a>
        <a href="requests.php" class="header-btn mr-1" id="req_id"><i class="fas fa-user-plus fa-lg"></i><span  id="req_desc">Friend Requests</span></a>
        <a href="timeline.php" class="header-btn mr-1" id="timeline_id"><i class="fas fa-user-circle fa-lg"></i><span  id="timeline_desc">Timeline</span></a>
        <a href="logout.php" class="header-btn mr-1" id="logout_id"><i class="fas fa-sign-out-alt fa-lg"></i><span  id="logout_desc">Logout</span></a>
      </div>
  </div>
      <?php
  } ?>