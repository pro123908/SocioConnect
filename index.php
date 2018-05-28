<?php require_once('header.php');

// If user has already logged in, then redirect user to main.php
if(isset($_SESSION['user_id'])){
  redirection('main.php');
}

?>

<html>
<head></head>
<!-- Login page, the first page where user comes -->
<body>
  <?php
    
  ?>
<h1>Welcome to SocioConnect</h1>  
  

<h1>Login</h1>
<!--  -->
<form action="login.php" method='POST'>
  <input type="text" name='email' placeholder='Email'><br><br>
  <input type="password" name='password' placeholder='Password'><br><br>
  <input type="submit" name='submit'><br><br>
</form>
<a href="signUp.php">Haven't registered?</a>


</body>

</html>