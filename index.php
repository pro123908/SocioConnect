<?php require_once('header.php');

// If user has already logged in, then redirect user to main.php
if(isset($_SESSION['user_id'])){
  redirection('main.php');
}

?>

<!-- Login page, the first page where user comes -->

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

      <div class='header-links'>
        <a href="signUp.php" class="header-btn mr-1">Sign Up</a>
      </div>
      
    </div>
    <div class="login-container">
      <h1 class="login-heading">Welcome</h1>
      <form action="login.php" method='POST' class="login-form">
        <input type="text"  name='email' placeholder="Email" class="login-input"><br>
        <input type="password" name='password' placeholder="Password" class="login-input"><br>
        <input type="submit" name='submit' class="login-submit">
      </form>
    </div>
 