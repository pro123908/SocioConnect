<?php // require_once('header.php');

// If user has already logged in, then redirect user to main.php
if(isset($_SESSION['user_id'])){
  redirection('main.php');
}

?>

<html>
<head>
  <link rel="stylesheet" href="main.css">
</head>
<!-- Login page, the first page where user comes -->
<body>
  <?php
    
  ?>
<!--<h1>Welcome to SocioConnect</h1>  
  

<h1>Login</h1>

<form action="login.php" method='POST'>
  <input type="text" name='email' placeholder='Email'><br><br>
  <input type="password" name='password' placeholder='Password'><br><br>
  <input type="submit" name='submit'><br><br>
</form>
<a href="signUp.php">Haven't registered?</a>
-->

<div class="header">
    <h1 class="header-heading">Socio Connect</h1>
    <h3 class="header-text">Not a member?</h3>
    <a href="signUp.php" class='header-btn'>Sign Up</a>
  </div>
  <div class='container'>
    <div class="heading-container">
      <h1 class='heading'>Socio Connect</h1>
    </div>

    <div class="login-container">
      <form action="login.php" method='POST' class="login">
        <input type="text" name='email' placeholder="Email" class="login-input">
        <br>
        <input type="password" name='password' placeholder="Password" class="login-input">
        <br>
        <input type="submit" name='submit'  class='login-submit'>
      </form>
    </div>

  </div>

</body>

</html>