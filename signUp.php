<?php
  require('header.php');
  

  if(isset($_POST['submit'])){
  
    $fname = mysqli_real_escape_string($connection,$_POST['fname']);
    $lname = mysqli_real_escape_string($connection,$_POST['lname']);
    $email = mysqli_real_escape_string($connection,$_POST['email']);
    $password = hashString(mysqli_real_escape_string($connection,$_POST['password']));
    $age = mysqli_real_escape_string($connection,$_POST['age']);
    $gender = mysqli_real_escape_string($connection,$_POST['genderBox']);

    $_SESSION['s_first_name'] = $fname;
    $_SESSION['s_last_name'] = $lname;
    $_SESSION['s_email'] = $email;
    $_SESSION['s_age'] = $age;
    
    $queryResult2 = queryFunc("SELECT user_id from users where email='$email'");
    $row = isRecord($queryResult2);

    if($_POST['password'] != $_POST['repeatPassword'] || $row['user_id'] > 0 || preg_match("/[0-9]+/", $_POST['password']) == 0 || preg_match("/[A-Za-z]+/", $_POST['password']) == 0){
        if($row['user_id'] > 0)
            $_SESSION['s_email_error'] = "Email Already in Use"; 
        else
            $_SESSION['s_email_error'] = "";     
        if($_POST['password'] != $_POST['repeatPassword'])  
            $_SESSION['s_pass_error'] = "Passwords Don't Match";
        else if(preg_match("/[0-9]+/", $_POST['password']) == 0 ||  preg_match("/[A-Za-z]+/", $_POST['password']) == 0)
            $_SESSION['s_pass_error'] = "Password Must Contain Alphanumeric Characters";    
        else
            $_SESSION['s_pass_error'] = "";     
      redirection('signUp.php');
    }
    else{
    $queryResult = queryFunc("INSERT INTO users(first_name,last_name,email,password,age,gender) VALUES('$fname','$lname','$email','$password','$age','$gender')");
    $queryResult2 = queryFunc("SELECT user_id from users where email='$email'");

    if($queryResult && $queryResult2){
      $row = isRecord($queryResult2);
      $_SESSION['user'] = $fname.' '.$lname;
      $_SESSION['user_id'] = $row['user_id'];
      redirection('main.php');
    }
  }
}
?>


<html>

 <body>
 <h1>Sign Up</h1>
<form action="signUp.php" method='POST'>
  <input type="text" id="fname" name='fname' placeholder='First Name' maxlength="20" minlength='3' required value=<?php  if(isset($_SESSION['s_first_name']))echo $_SESSION['s_first_name']?> ><br><br>
  <input type="text" id="lname" name='lname' placeholder='Last Name' maxlength="20" minlength='3' required value=<?php  if(isset($_SESSION['s_last_name']))echo $_SESSION['s_last_name']?>><br><br>
  <input type="email" id="email" name='email' placeholder='Email' required value=<?php  if(isset($_SESSION['s_email']))echo $_SESSION['s_email']?>><br><?php if(isset($_SESSION['s_email_error'])) echo $_SESSION['s_email_error']?><br>
  <input type="password" id="pass" name='password' placeholder='Password' maxlength="20" minlength='8' required ><br><br>
  <input type="password" id="rPass" name='repeatPassword' placeholder='Confirm Password' maxlength="20" minlength='8' required><br><?php if(isset($_SESSION['s_pass_error'])) echo $_SESSION['s_pass_error']?><br>
  <input type="number" name='age' placeholder='Age' required value=<?php  if(isset($_SESSION['s_age'])) echo $_SESSION['s_age']?>><br><br>
  <select name="genderBox" id="" required>
    <option value="male">Male</option>
    <option value="female">Female</option>
    <option value="other">Other</option>
  </select><br><br>
  <input type="submit" name='submit' value='Register'><br><br>
</form>

 </body>
</html>

<script>
  function validateSignUpForm(){
    <?php   session_start(); $_SESSION['error_signup'] = true;?>
    var hasNumber = /\d/;
    var hasAlphabet = /[a-zA-Z]/;
    var password = document.getElementById("pass").value;
    var rPass = document.getElementById("rPass").value;
    if(password != rPass){ 
      <?php   session_start(); $_SESSION['error_signup'] = false; ?>
      alert("Passwords Don't Match");   
    }
    if(!hasNumber.test(password) || !hasAlphabet.test(password)){
      <?php session_start(); $_SESSION['error_signup'] = false; ?>
      alert("Password Must Be An Alphanumeric Combination");
    }
        
  }
</script>