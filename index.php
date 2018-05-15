<html>
<head></head>

<body>
  <?php
    include "header.php";
  ?>
<h1>Welcome to SocioConnect</h1>

<h1>Sign Up</h1>
<form action="signUp.php" method='POST'>
  <input type="text" name='username' placeholder='Username'><br><br>
  <input type="password" name='password' placeholder='Password'><br><br>
  <input type="submit" name='submit'><br><br>
</form>


<h1>Login</h1>
<form action="login.php" method='POST'>
  <input type="text" name='username' placeholder='Username'><br><br>
  <input type="password" name='password' placeholder='Password'><br><br>
  <input type="submit" name='submit'><br><br>
</form>


</body>

</html>