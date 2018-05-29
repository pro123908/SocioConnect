<?php  include "functions.php"; ?>

<!-- Header Section of the website. Will be included in every page  -->

<html>
<head>
 <link rel="stylesheet" href="styles/styles.css"> 
</head>
<body onload="setUserId('<?php echo $_SESSION['user_id'];?>')">
<!-- Starting div of main content area of the website, where all the stuff lies -->
<div class='contentArea'>
<div id='header'>
<a href='main.php' class='headerHeading'>SocioConnect</a>
 
<?php
  // Displaying this navbar if user is logged in
  if (isset($_SESSION['user_id'])) {
      ?>
    <a class="header_links" href="logout.php">Logout</a>
    <a class="header_links" href="timeline.php">Profile</a>
    <a class="header_links" href="main.php">Newsfeed</a>  
    <a class="header_links" href="requests.php">Friend Requests</a> 
    <a class="header_links" href="message.php">Messages</a>  
    <a class='header_links' href='allNotification.php'>Notifications</a>
    <div class="search">
      <form action="search.php" method="get" name="search_form">
        <input type="text"  onkeyup="getUsers(this.value)" name="q" placeholder="Search..." autocomplete = "off" id="search_text_input">
      </form>
      <div class="search_results"></div>
      <div class="search_results_footer_empty"></div>
    </div>
  <?php
  }
?>
</div>



</html>