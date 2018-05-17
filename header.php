
<?php 
  include "db.php";
  include "functions.php";
  session_start();
?>

<html>
<head>
 <link rel="stylesheet" href="styles/styles.css">
</head>

<div id='header'>
<h1>SocioConnect</h1>
<?php
  // if user is logged In
  if (isset($_SESSION['user_id'])) { ?>
    <a href="logout.php">Logout</a>
    <a href="timeline.php">Profile</a>
    <a href="main.php">Newsfeed</a>
  <?php 
}
?>
</div>



</html>