<?php 

// Checked

require_once('header.php');

// If user has already logged in, then redirect user to main.php
if(isset($_SESSION['user_id'])){
  redirection('main.php');
}

?>

<!-- Login page, the first page where user comes -->

  <?php
    
  ?>
      <div class='header-links header-links-login'>
        <a href="signUp.php" class="header-btn mr-1">Sign Up</a>
      </div>
      
      <!-- div for completing header -->
    </div>

    <div class="login-container">
      <h1 class="login-heading">Welcome</h1>
      <form action="login.php" method='POST' class="login-form">
        <input type="text"  name='email' placeholder="Email" class="login-input" required><br>
        <input type="password" name='password' placeholder="Password" class="login-input" required><br>
        <input type="submit" name='submit' class="login-submit">
      </form>
    </div>
 