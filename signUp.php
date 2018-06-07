<?php

  require_once('header.php');
  
  // Sign up form for registration of new user

  if (isset($_POST['submit'])) {
      $fname = mysqli_real_escape_string($connection, $_POST['fname']); // First name
    $lname = mysqli_real_escape_string($connection, $_POST['lname']); // Last name
    $email = mysqli_real_escape_string($connection, $_POST['email']); // Email
    $password = hashString(mysqli_real_escape_string($connection, $_POST['password'])); // Password
    $age = mysqli_real_escape_string($connection, $_POST['age']); //Age
    $gender = mysqli_real_escape_string($connection, $_POST['genderBox']);  // Gender

    // Placing all fields value in session variables
      $_SESSION['s_first_name'] = $fname;
      $_SESSION['s_last_name'] = $lname;
      $_SESSION['s_email'] = $email;
      $_SESSION['s_age'] = $age;

      // Validating the value of input fields
      if (!(formValidation($email, $_POST['password'], $_POST['repeatPassword']))) {
          redirection('signUp.php');
      } else {
          // If fields are validated then adding the user to database
          if($gender == "female")
            $profile_pic = "assets/profile_pictures/female.jpg";
          else
          $profile_pic = "assets/profile_pictures/male.jpg";
          $queryResult = queryFunc("INSERT INTO users(first_name,last_name,email,password,age,gender,profile_pic) VALUES('$fname','$lname','$email','$password','$age','$gender','$profile_pic')");

          //Selecting ID of new inserted user
          $queryResult2 = queryFunc("SELECT user_id from users where email='$email'");

          if ($queryResult && $queryResult2) {
              $row = isRecord($queryResult2);
              $_SESSION['user'] = $fname.' '.$lname; // Name of new user inserted
              $_SESSION['user_id'] = $row['user_id'];
              redirection('main.php');
          }
      }
  }
?>



 <!-- <h1>Sign Up</h1>
<form action="signUp.php" method='POST'>
  <input type="text" id="fname"  name='fname' placeholder='First Name' maxlength="20" minlength='3' required value=<?php  if (isset($_SESSION['s_first_name'])) {
    echo $_SESSION['s_first_name'];
}?> ><br><br>
  <input type="text" id="lname" name='lname' placeholder='Last Name' maxlength="20" minlength='3' required value=<?php  if (isset($_SESSION['s_last_name'])) {
    echo $_SESSION['s_last_name'];
}?>><br><br>
  <input type="email" id="email" name='email' placeholder='Email' required value=<?php  if (isset($_SESSION['s_email'])) {
    echo $_SESSION['s_email'];
}?>><br><?php if (isset($_SESSION['s_email_error'])) {
    echo $_SESSION['s_email_error'];
}?><br>
  <input type="password" id="pass" name='password' placeholder='Password' maxlength="20" minlength='8' required ><br><br>
  <input type="password" id="rPass" name='repeatPassword' placeholder='Confirm Password' maxlength="20" minlength='8' required><br><?php if (isset($_SESSION['s_pass_error'])) {
    echo $_SESSION['s_pass_error'];
}?><br>
  <input type="number" name='age' placeholder='Age' required value=<?php  if (isset($_SESSION['s_age'])) {
    echo $_SESSION['s_age'];
}?>><br><br>
  <select name="genderBox" id="" required>
    <option value="male">Male</option>
    <option value="female">Female</option>
    <option value="other">Other</option>
  </select><br><br>
  <input type="submit" name='submit' value='Register'><br><br>
</form> -->


<div class='header-links'>
        <a href="index.php" class="header-btn mr-1">Login</a>
      </div>
      
    </div>


    <div class="login-container">
      <!-- <h1 class="login-heading"></h1> -->
      <form action="signUp.php" method='POST' class="login-form">
      <input type="text" id="fname"  class='login-input' name='fname' placeholder='First Name' maxlength="20" minlength='3' required value=<?php  if (isset($_SESSION['s_first_name'])) {
    echo $_SESSION['s_first_name'];
}?> ><br>
  <input type="text" id="lname" name='lname' class='login-input' placeholder='Last Name' maxlength="20" minlength='3' required value=<?php  if (isset($_SESSION['s_last_name'])) {
    echo $_SESSION['s_last_name'];
}?>><br>
  <input type="email" id="email" name='email' class='login-input' placeholder='Email' required value=<?php  if (isset($_SESSION['s_email'])) {
    echo $_SESSION['s_email'];
}?>><br><?php if (isset($_SESSION['s_email_error'])) {
    echo $_SESSION['s_email_error'];
}?><br>
  <input type="password" id="pass" name='password' class='login-input' placeholder='Password' maxlength="20" minlength='8' required ><br><br>
  <input type="password" id="rPass" name='repeatPassword' class='login-input' placeholder='Confirm Password' maxlength="20" minlength='8' required><br><?php if (isset($_SESSION['s_pass_error'])) {
    echo $_SESSION['s_pass_error'];
}?><br>
  <input type="number" name='age' placeholder='Age' class='login-input' required value=<?php  if (isset($_SESSION['s_age'])) {
    echo $_SESSION['s_age'];
}?>><br>
  <select name="genderBox" id="" required class='login-input'>
    <option value="male">Male</option>
    <option value="female">Female</option>
    <option value="other">Other</option>
  </select><br>
  <input type="submit" name='submit' class='login-submit' value='Register'>
      </form>
    </div>

