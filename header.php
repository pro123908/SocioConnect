<?php  include "functions.php"; ?>

<!-- Header Section of the website. Will be included in every page  -->

<html>
<head>
<!-- <link href="fonts/fontawesome-all.css" rel="stylesheet"> -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet">
 <link rel="stylesheet" href="main.css">
</head>
<body onload="setUserId('<?php echo $_SESSION['user_id'];?>')">
<!-- Starting div of main content area of the website, where all the stuff lies -->
<!-- <div class='contentArea'>
<div id='header'>
<a href='main.php' class='headerHeading'>SocioConnect</a>
 
<?php
  // Displaying this navbar if user is logged in
  // if (isset($_SESSION['user_id'])) {
  ?>
    <a class="header_links" href="logout.php">Logout</a>
    <a class="header_links" href="timeline.php">Profile</a>
    <a class="header_links" href="main.php">Newsfeed</a>  
    <a class="header_links" href="requests.php">Friend Requests</a> 
    <a class="header_links" href="messages.php">Messages</a>  
    <a class='header_links' href='allNotification.php'>Notifications</a>
    <div class="search">
      <form action="search.php" method="get" name="search_form">
        <input type="text"  onkeyup="getUsers(this.value,1)" name="q" placeholder="Search..." autocomplete = "off" id="search_text_input">
      </form>
      <div class="search_results"></div>
      <div class="search_results_footer_empty"></div>
    </div>
  <?php
  
?>
</div> --> 


<div class="main-container">
    <div class="header">
      <div class="header-heading">
        <h1>Socio Connect</h1>
      </div>
      
      
      <?php
  // Displaying this navbar if user is logged in
  if (isset($_SESSION['user_id'])) {
      ?>
      <div class="header-search-bar">
        <input type="text" class="search-input" placeholder="Search">
      </div>
      <div class="header-links">
        <a href="allNotification.php" class="header-btn mr-1" id="noti_id" ><i class="fas fa-bell"></i> <span  id="noti_desc">Notifications</span></a>
        <a href="messages.php" class="header-btn mr-1" id="msg_id"><i class="fas fa-envelope"></i><span  id="msg_desc">Messages</span></a>
        <a href="requests.php" class="header-btn mr-1" id="req_id"><i class="fas fa-user-plus"></i><span  id="req_desc">Friend Requests</span></a>
        <a href="main.php" class="header-btn mr-1" id="newsfeed_id"><i class="fas fa-users"></i><span  id="newsfeed_desc">Newsfeed</span></a>
        <a href="timeline.php" class="header-btn mr-1" id="timeline_id"><i class="fas fa-user-circle"></i><span  id="timeline_desc">Timeline</span></a>
        <a href="logout.php" class="header-btn mr-1" id="logout_id"><i class="fas fa-sign-out-alt"></i><span  id="logout_desc">Logout</span></a>
      </div>
  </div>
      <?php
  } ?>
  
  <style>
 #noti_id:hover #noti_desc {
    display: block;
}
#msg_id:hover #msg_desc {
    display: block;
} 
#req_id:hover #req_desc {
    display: block;
} 
#newsfeed_id:hover #newsfeed_desc {
    display: block;
}
#timeline_id:hover #timeline_desc {
    display: block;
}
#logout_id:hover #logout_desc {
    display: block;
}
#noti_desc, #logout_desc, #timeline_desc, #newsfeed_desc, #req_desc, #msg_desc{
    display: none;
    background-color:black;
    color:white;
    margin-left: 98px;
    padding: 2px;
    position: absolute;
    z-index: 1000;
    width:160px;
    height:22px;
    border-radius:5px;
}
  </style>
    
