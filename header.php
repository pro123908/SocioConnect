
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
  if (isset($_SESSION['user_id'])) { ?>
    <a href="logout.php">Logout</a>
  <?php 
}
?>
</div>

</html>