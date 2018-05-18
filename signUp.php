<?php
  require('header.php');
  

  if(isset($_POST['submit'])){
    
  
    $fname = mysqli_real_escape_string($connection,$_POST['fname']);
    $lname = mysqli_real_escape_string($connection,$_POST['lname']);
    $email = mysqli_real_escape_string($connection,$_POST['email']);
    $password = hashString(mysqli_real_escape_string($connection,$_POST['password']));
    $age = mysqli_real_escape_string($connection,$_POST['age']);
    $gender = mysqli_real_escape_string($connection,$_POST['genderBox']);
    
    

    $queryResult = queryFunc("INSERT INTO users(first_name,last_name,email,password,age,gender) VALUES('$fname','$lname','$email','$password','$age','$gender')");
    
    $queryResult2 = queryFunc("SELECT user_id from users where email='$email'");

    if($queryResult && $queryResult2){
      $row = isRecord($queryResult2);
      $_SESSION['user'] = $fname.' '.$lname;
      $_SESSION['user_id'] = $row['user_id'];
      redirection('main.php');
    }
  }

?>


<html>

 <body>
 <h1>Sign Up</h1>
<form action="signUp.php" method='POST'>
  <input type="text" name='fname' placeholder='First Name'><br><br>
  <input type="text" name='lname' placeholder='Last Name'><br><br>
  <input type="email" name='email' placeholder='Email'><br><br>
  <input type="password" name='password' placeholder='Password'><br><br>
  <input type="number" name='age' placeholder='Age'><br><br>
  <select name="genderBox" id="">
    <option value="male">Male</option>
    <option value="female">Female</option>
    <option value="other">Other</option>
  </select><br><br>
  <input type="submit" name='submit' value='Register'><br><br>
</form>

 </body>
</html>
